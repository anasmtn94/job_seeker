<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Spatie\PdfToText\Pdf;
use OpenAI\Laravel\Facades\OpenAI;
use Illuminate\Support\Facades\Log;

class ResumesAnalysisService
{
    public function extractResumeFormation(string $fileUrl){

        //extrct the raw text data from pdf file
        $rawText = $this->extractRawTextFromPdf($fileUrl);

        //getting the json data from the raw text using AI chatGPT
        $jsonData = $this->getJsonDataFromRawText($rawText);
        
        //returning the json data
        return $jsonData;
    }


    private function extractRawTextFromPdf(string $fileUrl){
        $tempFile = tempnam(sys_get_temp_dir(), 'resume');
        $filePath=parse_url($fileUrl, PHP_URL_PATH);
        if(!$filePath){
            throw new \Exception('Invalid file URL');
        }

        $fileName=basename($filePath);
        $storagePath="resumes/".$fileName;
        if(!Storage::disk('cloud')->exists($storagePath)){
            throw new \Exception('Failed to retrieve file from cloud storage: '.$storagePath);
        }
        $fileContent = Storage::disk('cloud')->get($storagePath);
        if(!$fileContent){
            throw new \Exception('Failed to read file content from cloud storage: '.$storagePath);
        }
        file_put_contents($tempFile, $fileContent); 
        
        //check if pdftotext binary is installed in common system locations
        $pdftotextPaths = [
            '/usr/bin/pdftotext',
            '/usr/local/bin/pdftotext',
            '/opt/homebrew/bin/pdftotext', // macOS with Homebrew on Apple Silicon
            '/usr/local/opt/poppler/bin/pdftotext', // macOS with Homebrew
            '/bin/pdftotext',
            '/sbin/pdftotext'
        ];
        
        $pdftotextPath = null;
        foreach ($pdftotextPaths as $path) {
            if (file_exists($path) && is_executable($path)) {
                $pdftotextPath = $path;
                break;
            }
        }
        
        if (!$pdftotextPath) {
            // Fallback: try to find pdftotext using which command
            $whichOutput = shell_exec('which pdftotext 2>/dev/null');
            if ($whichOutput && trim($whichOutput)) {
                $pdftotextPath = trim($whichOutput);
            }
        }
        
        if (!$pdftotextPath) {
            throw new \Exception('pdftotext binary not found. Please install poppler-utils package (Linux) or poppler via Homebrew (macOS)');
        }
        
       $instance = new Pdf();
       $instance->setPdf($tempFile);       
       $text = $instance->text();
        unlink($tempFile);
        return $text;
    }

    private function getJsonDataFromRawText(string $rawText){
        //getting the json data from the raw text using AI chatGPT
        $response = OpenAI::chat()->create([
            'model' => 'gpt-4o-mini',
            'messages' => [
                ['role' => 'system', 'content' => 'You are a precise and accurate resume analyzer that extracts the information from the resume as it os appearing in the resume without adding any other interpretation or adding any other information, the output should be in JSON format.'],
                ['role' => 'user', 'content' =>  "Parse the following resume and extract the information as a JSON object with the exact keys: 'summary', 'skills', 'experience', 'education' the resume contents is: {$rawText}
               Return an empty string for a key if not found.All values in the JSON must be plain strings, not arrays or objects."],                  
            ],
            'temperature' => 0,
            'response_format' => [
                'type' => 'json_object',
            ]
        ]);    
    $result = $response->choices[0]->message->content;
    $parsedData = json_decode($result, true);
    if(json_last_error() !== JSON_ERROR_NONE){
        throw new \Exception('Failed to parse JSON: '.$result);
    }

    $requiredKeys = ['summary', 'skills', 'experience', 'education'];
    $missingKeys = array_diff($requiredKeys, array_keys($parsedData));
    if(count($missingKeys) > 0){
        throw new \Exception('Missing required keys: '.implode(', ', $missingKeys));
    }
    
    return ['summary' => $parsedData['summary'], 'skills' => $parsedData['skills'], 'experience' => $parsedData['experience'], 'education' => $parsedData['education']];
    }





    public function evaluateResume($jobVacancy, $resumeData){

        $jobVacancy=json_encode([
            'job_title' => $jobVacancy['title'],
            'job_description' => $jobVacancy['description'],
            'job_location'=>$jobVacancy['location'],
            'job_type'=>$jobVacancy['type'],
            'job_salary'=>$jobVacancy['salary'],            
        ]);
        $resumeData=json_encode([
            'summary' => $resumeData['summary'] ?? '',
            'skills' => $resumeData['skills'] ?? '',
            'experience' => $resumeData['experience'] ?? '',
            'education' => $resumeData['education'] ?? '',
        ]);
        $response = OpenAI::chat()->create([
            'model' => 'gpt-4o-mini',
            'messages' => [
                [
                    'role' => 'system',
                    'content' => "You are an expert HR professional and job recruiter. You are given a job vacancy and a resume. 
                                    Your task is to analyze the resume and determine if the candidate is a good fit for the job. 
                                    The output should be in JSON format. 
                                    Provide a score from 0 to 100 for the candidate's suitability for the job, and a detailed feedback. 
                                    Response should only be JSON that has the following keys: 'aiGeneratedScore', 'aiGeneratedFeedback'. 
                                    aiGeneratedFeedback should be detailed and specific to the job and the candidate's resume."
                ],
                [
                    'role' => 'user',
                    'content' => "Please evaluate this job application. Job Details: {$jobVacancy}. Resume Details: {$resumeData}"
                ],
            ],
            'response_format' => [
                'type' => 'json_object',
            ],
            'temperature' => 0,
        ]);

        $result = $response->choices[0]->message->content;
        $parsedData = json_decode($result, true);
        if(json_last_error() !== JSON_ERROR_NONE){
            throw new \Exception('Failed to parse JSON: '.$result);
        }
    
        if(!isset($parsedData['aiGeneratedScore']) || !isset($parsedData['aiGeneratedFeedback'])){
            throw new \Exception('Missing required keys: aiGeneratedScore or aiGeneratedFeedback');
        }

        return ['aiGeneratedScore' => $parsedData['aiGeneratedScore'], 'aiGeneratedFeedback' => $parsedData['aiGeneratedFeedback']];

    }




}