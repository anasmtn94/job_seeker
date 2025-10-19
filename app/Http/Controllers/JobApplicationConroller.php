<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\JobApplication;
class JobApplicationConroller extends Controller
{

    public function index(){
      
        $applications = JobApplication::latest()->where('userId', auth()->user()->id)->paginate(10);
        return view('job-application.index', compact('applications'));
    }

}
