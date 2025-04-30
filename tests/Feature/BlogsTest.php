<?php
use App\Models\Blog;
use App\Models\Department;
use Database\Seeders\BlogSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;
use App\Models\User;
use Tests\TestCase;


class BlogsTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);
        $this->seed(BlogSeeder::class);
    }

    private function authUser(){
        $department = \App\Models\Department::factory()->create();

        $user = User::factory()->create([
            'password' =>hash::make('password'),
            'isActive'=> 1,
            'department_id' => $department->id,
        ]);
        $user->assignRole('admin');
        Sanctum::actingAs($user, ['*']);
        return $user;
    }

    public function test_can_list_blogs()
    {
        $response = $this->get('/api/posts');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'img',
                    'author_id',
                    'title',
                    'department_id',
                    'content',
                    'slug',
                    'updated_at',
                    'created_at',
                    'featured',
                ]
            ]
        ]);
    }
    public function test_can_show_single_blog()
    {
        $this->authUser();
        $blog = Blog::factory()->create();

        $response = $this->get('/api/post/' . $blog->id);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                'img',
                'author_id',
                'title',
                'department_id',
                'content',
                'slug',
                'featured',
            ]
        ]);
    }



    public function test_can_create_blog()
{
    $this->authUser();
    $author = \App\Models\User::factory()->create();
    $department = Department::factory()->create();

    $blog = Blog::factory()->make();

    Storage::fake('public');

    $response = $this->postJson('/api/post', [
        'img' => UploadedFile::fake()->image('blog.jpg'),
        'author_id' => $author->id,
        'title' => $blog->title,
        'department_id' => $department->id,
        'content' => $blog->content,
        'slug' => $blog->slug,
        'featured' => $blog->featured,
    ]);

    $response->assertStatus(201);

    $response->assertJsonStructure([
        'data' => [
            'img',
            'author_id',
            'title',
            'department_id',
            'content',
            'slug',
            'featured',
        ]
    ]);
}

    public function test_can_update_blog()
    {
        $this->authUser();


        $blog = Blog::factory()->create();
        $auther_id = User::factory()->create();

        $response = $this->put('/api/post/' . $blog->id, [
            'title' => 'Updated Title',
            'author_id' => $auther_id->id,
            'slug' =>  'updated_title_sss',
            'content' => 'Updated Content',
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                'id',
                'img',
                'author_id',
                'title',
                'department_id',
                'content',
                'slug',
                'featured',
               'created_at',
               'updated_at',
            ]
        ]);
    }

    public function test_can_delete_blog()
    {
        $this->authUser();

        $blog = Blog::factory()->create();

        $response = $this->delete('/api/post/' . $blog->id);

        $response->assertStatus(200);
        $this->assertDatabaseMissing('blogs', ['id' => $blog->id]);
    }

    public function test_can_search_blogs()
    {
        $blog = Blog::factory()->create(['title'=> 'laravel blog']);
        $response = $this->get('/api/search/' . $blog->title);
        $response->assertStatus(200);
    }

    public function test_can_get_blogs_by_department()
    {
        $departmentId = 1;

        Department::factory()->create([
            'id' => $departmentId,
        ]);

        $blog = Blog::factory()->create([
            'department_id' => $departmentId,
        ]);

        $response = $this->get('/posts/department/' . $blog->department_id);

        $response->assertStatus(200);
    }


}



