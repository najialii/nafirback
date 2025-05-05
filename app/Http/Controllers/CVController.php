<?php

namespace App\Http\Controllers;
use App\Http\Requests\StoreCvRequest;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Cv;

use OpenAI\Laravel\Facades\OpenAI;


class CVController extends Controller
{
    public function rate(Request $request)
    {
        if (!$request->hasFile('cv')) {
            return response()->json(['message' => 'Please upload a file first'], 400);
        }

        $file = $request->file('cv');

        $extension = $file->getClientOriginalExtension();
        if (!in_array($extension, ['pdf', 'doc', 'docx'])) {
            return response()->json(['message' => 'Only PDF or Word files are supported'], 400);
        }

        $text = $this->extractText($file);


        $suggestions = $this->getSuggestions($text);

        return response()->json([
            'message' => 'CV has been rated successfully',
            'text' => $text,
            'suggestions' => $suggestions,
        ]);
    }

    private function extractText($file)
    {
        $ext = $file->getClientOriginalExtension();

        if ($ext === 'pdf') {
            $parser = new \Smalot\PdfParser\Parser();
            $pdf = $parser->parseFile($file->getPathname());
            return $pdf->getText();
        }

        if (in_array($ext, ['doc', 'docx'])) {
            $phpWord = \PhpOffice\PhpWord\IOFactory::load($file->getPathname());
            $text = '';
            foreach ($phpWord->getSections() as $section) {
                foreach ($section->getElements() as $element) {
                    if (method_exists($element, 'getText')) {
                        $text .= $element->getText() . ' ';
                    }
                }
            }
            return $text;
        }

        return '';
    }



    private function getSuggestions($text)
    {

        $prompt = <<<"EOT"
    You are an expert in CV writing and ATS (Applicant Tracking Systems) optimization.

    Analyze the following CV and provide detailed feedback and suggestions. The analysis should be tailored to common job postings in the software/tech industry and focus on optimizing the CV for ATS filters , Suggestions must be in  a list and i will  render this to AI.

    CV Text:
    ---
    $text
    ---

    Respond in this exact JSON format:

    {
      "title": "CV Review & Suggestions",
      "description": "A summary of the CV analysis based on ATS and professional CV writing standards.",
      "sections_present": [],
      "missing_sections": [],
      "keyword_analysis": {
        "matched_keywords": [],
        "missing_keywords": []
      },
      "formatting_issues": [],
      "clarity_issues": [],
      "grammar_spelling": [],
      "suggestions": []
    }

    Important:
    - The keyword_analysis must be dynamic. Extract keywords from the CV text AND compare them to commonly expected skills for a software/tech position.
    - All fields must be included even if empty.
    - Ensure output is always valid JSON.
    EOT;

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . config('services.openrouter.key'),
            'X-Title' => 'CV Review Tool',
        ])->post('https://openrouter.ai/api/v1/chat/completions', [
                    'model' => 'openai/gpt-3.5-turbo-0125',
                    'messages' => [
                        [
                            'role' => 'user',
                            'content' => $prompt,
                        ]
                    ],
                ]);


        return $response->json();
    }



    public function store(StoreCvRequest $request ){
        try {
            $validatedData = $request->validated(); 

            $cv = Cv::create($validatedData);

            return response()->json([
                'message' => 'CV reviewer created successfully',
                'data' => $cv
            ], 201);

            // return new 
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json([
                'error'=> 'something went wrong, please try again',
                'message'=> $th->getMessage()
            ]);
        }

    }   

}
