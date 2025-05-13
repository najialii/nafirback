<?php

declare(strict_types=1);

namespace Modules\Zoom\Services;

use Carbon\Carbon;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Modules\Zoom\Exceptions\ZoomApiException;
use Throwable;

class ZoomService
{
    private const CACHE_TTL_USERS = 60*120; // 120 minutes
    private const CACHE_TTL_MEETINGS = 60*10; // 5 minutes
    private const API_TIMEOUT = 60; // 60 seconds

    private readonly string $baseUrl;
    private array $headers;

    public function __construct()
    {
        $this->baseUrl = config('zoom.baseUrl', 'https://api.zoom.us/v2/');
        $this->headers = [
            'Authorization' => 'Bearer ' . $this->getAccessToken(),
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ];
    }

    public function generateZoomToken(): string
    {
        $key = config('zoom.clientId');
        $secret = config('zoom.clientSecret');
        $payload = [
            'iss' => $key,
            'exp' => strtotime('+1 minute'),
        ];

        return \Firebase\JWT\JWT::encode($payload, $secret, 'HS256');
    }

    protected function getAccessToken(): string
    {
        return Cache::remember('zoom_access_token', 55 * 60, function (): string {
            try {
                $response = Http::asForm()
                    ->timeout(self::API_TIMEOUT)
                    ->withBasicAuth(
                        config('zoom.clientId'),
                        config('zoom.clientSecret')
                    )
                    ->post('https://zoom.us/oauth/token', [
                        'grant_type' => config('zoom.grantType'),
                        'account_id' => config('zoom.accountId'),
                    ]);

                $this->handleFailedResponse($response, 'Failed to get Zoom token');

                return $response->json('access_token');
            } catch (Throwable $e) {
                $this->logError('Error getting Zoom access token', $e);
                throw new ZoomApiException('Failed to get Zoom access token: ' . $e->getMessage());
            }
        });
    }

    public function getAvailableUsers(Carbon $startTime, int $duration): Collection
    {
        $cacheKey = "zoom_available_users_{$startTime->timestamp}_{$duration}";

        return Cache::remember($cacheKey, self::CACHE_TTL_MEETINGS, function () use ($startTime, $duration): Collection {
            try {
                // Get all active Zoom users with caching
                $zoomUsers = $this->getCachedUsers();

                // If we're not filtering by availability yet, return cached users
                //TODO: enable filter
                return $zoomUsers;

                // Get the time window we need to check
                $endTime = $startTime->copy()->addMinutes($duration);

                return $zoomUsers->filter(fn ($zoomUser) =>
                    $this->isUserAvailable($zoomUser['id'], $startTime, $endTime)
                );
            } catch (Throwable $e) {
                $this->logError('Error getting available users', $e, [
                    'start_time' => $startTime,
                    'duration' => $duration,
                ]);
                throw $e;
            }
        });
    }

    protected function getCachedUsers(): Collection
    {
        return Cache::remember('zoom_active_users', self::CACHE_TTL_USERS, function (): Collection {
            $response = $this->getUsers(
                status: 'active',
                pageSize: '100'
            );
            return collect($response['users']);
        });
    }

    public function assignUserToMeeting(array $criteria = []): string
    {
        $startTime = Carbon::parse($criteria['start_time']);
        $duration = $criteria['duration'];

        $availableUsers = $this->getAvailableUsers($startTime, $duration);
        if ($availableUsers->isEmpty()) {
            throw new ZoomApiException('No available Zoom users found for the specified time');
        }

        // Round-robin assignment with caching
        $userIndex = Cache::get('zoom_user_assignment_index', 0);
        $user = $availableUsers[$userIndex % $availableUsers->count()];

        Cache::put('zoom_user_assignment_index', $userIndex + 1);

        return $user['id'];
    }

    public function createMeeting(string $userId, array $data): array
    {
        try {
            $response = Http::withHeaders($this->headers)
                ->timeout(self::API_TIMEOUT)
                ->post("{$this->baseUrl}/users/{$userId}/meetings", $data);

            $this->handleFailedResponse($response, 'Failed to create Zoom meeting', [
                'user_id' => $userId,
                'data' => $data,
            ]);

            return $response->throw()->json();
        } catch (Throwable $e) {
            $this->logError('Error creating Zoom meeting', $e, [
                'user_id' => $userId,
            ]);

            throw new ZoomApiException('Error creating meeting: ' . $e->getMessage());
        }
    }

    public function getMeeting(string $meetingId): array
    {
        try {
            $response = Http::withHeaders($this->headers)
                ->timeout(self::API_TIMEOUT)
                ->get("{$this->baseUrl}/meetings/{$meetingId}");

            $this->handleFailedResponse($response, 'Failed to get Zoom meeting', [
                'meeting_id' => $meetingId,
            ]);

            return $response->throw()->json();
        } catch (Throwable $e) {
            $this->logError('Error getting Zoom meeting', $e, [
                'meeting_id' => $meetingId,
            ]);
            throw new ZoomApiException('Error getting meeting: ' . $e->getMessage());
        }
    }

    public function updateMeeting(string $meetingId, array $data): array
    {
        try {
            $response = Http::withHeaders($this->headers)
                ->timeout(self::API_TIMEOUT)
                ->patch("{$this->baseUrl}/meetings/{$meetingId}", $data);

            $this->handleFailedResponse($response, 'Failed to update Zoom meeting', [
                'meeting_id' => $meetingId,
                'data' => $data,
            ]);

            return $response->throw()->json();
        } catch (Throwable $e) {
            $this->logError('Error updating Zoom meeting', $e, [
                'meeting_id' => $meetingId,
            ]);
            throw new ZoomApiException('Error updating meeting: ' . $e->getMessage());
        }
    }

    public function deleteMeeting(string $meetingId): bool
    {
        try {
            $response = Http::withHeaders($this->headers)
                ->timeout(self::API_TIMEOUT)
                ->delete("{$this->baseUrl}/meetings/{$meetingId}");

            if (!$response->successful()) {
                $this->logWarning('Failed to delete Zoom meeting', [
                    'meeting_id' => $meetingId,
                    'status' => $response->status(),
                    'response' => $response->throw()->json(),
                ]);
                return false;
            }

            return true;
        } catch (Throwable $e) {
            $this->logError('Error deleting Zoom meeting', $e, [
                'meeting_id' => $meetingId,
            ]);
            return false;
        }
    }

    public function getUsers(?string $status = null, ?string $pageSize = null, ?string $nextPageToken = null): array
    {
        try {
            $response = Http::withHeaders($this->headers)
                ->timeout(self::API_TIMEOUT)
                ->get("{$this->baseUrl}/users", array_filter([
                    'status' => $status,
                    'page_size' => $pageSize,
                    'next_page_token' => $nextPageToken,
                ]));

            return $response->throw()->json();
        } catch (RequestException $e) {
            if (property_exists($e, 'response')) {
                $statusCode = property_exists($e, 'response') ? $e->response?->status() : 500;
                $responseBody = property_exists($e, 'response') ? $e->response?->json() ?? [] : [];
                $this->logError('Error fetching Zoom users', $e, [
                    'status' => $statusCode,
                    'response' => $responseBody,
                ]);
            }


            throw new ZoomApiException('Error fetching users: ' . $e->getMessage());
        }
    }

    public function getUserMeetings(string $userId, array $params = []): array
    {
        $cacheKey = "zoom_user_meetings_{$userId}_" . md5(serialize($params));

        return Cache::remember($cacheKey, self::CACHE_TTL_MEETINGS, function () use ($userId, $params): array {
            try {
                $response = Http::withHeaders($this->headers)
                    ->timeout(self::API_TIMEOUT)
                    ->get("{$this->baseUrl}/users/{$userId}/meetings", $params);

                $this->handleFailedResponse($response, 'Failed to get user meetings', [
                    'user_id' => $userId,
                ]);

                return $response->throw()->json();
            } catch (Throwable $e) {
                $this->logError('Error getting user meetings', $e, [
                    'user_id' => $userId,
                ]);
                throw new ZoomApiException('Error getting user meetings: ' . $e->getMessage());
            }
        });
    }

    protected function isUserAvailable(string $userId, Carbon $startTime, Carbon $endTime): bool
    {
        $cacheKey = "zoom_user_availability_{$userId}_{$startTime->timestamp}";

        return Cache::remember($cacheKey, self::CACHE_TTL_MEETINGS, function () use ($userId, $startTime, $endTime): bool {
            try {
                $meetings = collect($this->getUserMeetings($userId, [
                    'type' => 'scheduled',
                    'page_size' => '100'
                ])['meetings']);

                return !$meetings->some(function (array $meeting) use ($startTime, $endTime): bool {
                    $meetingStart = Carbon::parse($meeting['start_time']);
                    $meetingEnd = $meetingStart->copy()->addMinutes($meeting['duration']);

                    return $this->hasTimeConflict(
                        $startTime,
                        $endTime,
                        $meetingStart,
                        $meetingEnd
                    );
                });
            } catch (Throwable $e) {
                $this->logError('Error checking user availability', $e, [
                    'user_id' => $userId,
                ]);
                return false;
            }
        });
    }

    protected function hasTimeConflict(
        Carbon $start1,
        Carbon $end1,
        Carbon $start2,
        Carbon $end2
    ): bool {
        return $start1 < $end2 && $start2 < $end1;
    }

    protected function handleFailedResponse(Response $response, string $message, array $context = []): void
    {
        if (!$response->successful()) {
            $errorContext = [
                'status' => $response->status(),
                'response' => $response->throw()->json(),
                ...$context,
            ];

            Log::error($message, $errorContext);
            throw new ZoomApiException($message);
        }
    }

    protected function logError(string $message, Throwable $exception, array $context = []): void
    {
        Log::error($message, [
            'error' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString(),
            ...$context,
        ]);
    }

    protected function logWarning(string $message, array $context = []): void
    {
        Log::warning($message, $context);
    }
}
