<?php

namespace App\Http\Controllers;

use App\Models\JobVacancy;
use Illuminate\Http\Request;
use App\Http\Requests\ApplyJobRequest;
use App\Models\Resume;
use App\Models\JobApplication;
use App\Services\ResumesAnalysisService;

class JobVacancyController extends Controller
{
    private $resumesAnalysisService;

    public function __construct(ResumesAnalysisService $resumesAnalysisService)
    {
    //this is the dependency injection of the resumesAnalysisService
        $this->resumesAnalysisService = $resumesAnalysisService;
    }

    public function show(string $id){

        $job = JobVacancy::findOrFail($id);
        return view("job-vacancy.show",compact('job'));

    }


        public function apply(string $id){

        $job=JobVacancy::findOrFail($id);
        $resumes = Resume::where('userId', auth()->user()->id)->get();
        return view("job-vacancy.apply",compact('job', 'resumes'));


    }



    public function processApplication (ApplyJobRequest $request,string $id){
        $resumeID =null;
        $job = JobVacancy::findOrFail($id);
        $user = auth()->user();
        $validated = $request->validated();


        if($request->resume_choice == 'new'){

        $file = $request->file('resume_file');
        $fileOriginalName = $file->getClientOriginalName();
        $fileExtension = $file->getClientOriginalExtension();
        $fileName = "resume_-".time().'.' . $fileExtension;

        $path = $file->storeAs('resumes',$fileName, 'cloud');

            $fileUrl = config('filesystems.disks.cloud.url').'/'.$path;

            $extractedData = $this->resumesAnalysisService->extractResumeFormation($fileUrl);
            // $extractedData = [
            //     'summary' => $extractedData_response['summary'],
            // 'skills' => $extractedData_response['skills'],
            // 'experience' => $extractedData_response['experience'],
            // 'education' => $extractedData_response['education'],
            // ];
        
        $resume = Resume::createOrFirst([
            'filename' => $fileOriginalName,
            'fileUri' => $path,
            'userId' => $user->id,
            'contactDetails' => json_encode([
                'name' => $user->name,
                'email' => $user->email,
            ]),
            'summary' => $extractedData['summary'],
            'skills' => $extractedData['skills'],
            'experience' => $extractedData['experience'],
            'education' => $extractedData['education'],
          
        ]);
        $resumeID = $resume->id;

        }else{
            $resume = Resume::findOrFail($request->resume_choice);
            $resumeID = $resume->id;
            $extractedData = [
                'summary' => $resume->summary,
                'skills' => $resume->skills,
                'experience' => $resume->experience,
                'education' => $resume->education,
            ];  
        }

        // TODO: evaluate the resume and generate a score and feedback using CHATGPT API
        $evaluationResult = $this->resumesAnalysisService->evaluateResume($job, $extractedData);
        $jobApplication = JobApplication::createOrFirst([
            'jobVacancyId' => $job->id,
            'resumeId' => $resumeID,
            'userId' => $user->id,
            'status' => 'pending',
            'aiGeneratedScore' => $evaluationResult['aiGeneratedScore'],
            'aiGeneratedFeedback' => $evaluationResult['aiGeneratedFeedback']   ,    
        ]);
        return redirect()->route('job-applications.index')->with('success', 'Application submitted successfully');
    }
}
