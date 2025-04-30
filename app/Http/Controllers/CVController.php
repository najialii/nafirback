<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

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

        $score = $this->rateCVContent($text);

        return response()->json([
            'message' => 'CV has been rated successfully',
            'score' => $score,
            'text'=> $text,
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

    private function dummyRateCVContent($text)
    {
        $score = 0;

        if (stripos($text, 'experience') !== false) {
            $score += 30;
        }

        if (stripos($text, 'education') !== false) {
            $score += 30;
        }

        if (stripos($text, 'skills') !== false) {
            $score += 30;
        }

        if (strlen($text) > 300) {
            $score += 10;
        }

        return min($score, 100);
    }
}
