<?php

use App\Http\Controllers\DashboardConroller;
use App\Http\Controllers\JobApplicationConroller;
use App\Http\Controllers\JobVacancyController;
use App\Http\Controllers\ProfileController;
use App\Models\JobApplication;
use App\Models\JobVacancy;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});


 //{{ testing open AI request}}
    Route::get('/test-ai',[DashboardConroller::class,"testai"]);

// Route::get('/dashboard', function () {
//     return view('dashboard');
// })->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth','role:seeker'])->group(function () {
    Route::get("/dashboard",[DashboardConroller::class,"index"])->name("dashboard");
    Route::get("/job-applications",[JobApplicationConroller::class,"index"])->name("job-applications.index");
    Route::get('/vacancy/{id}',[JobVacancyController::class,'show'])->name("vacancy.show");
    
    Route::get('/job-applications',[JobApplicationConroller::class,'index'])->name("job-applications.index");


    Route::get('/vacancy/{id}/apply',[JobVacancyController::class,'apply'])->name("vacancy.apply");
    Route::post('/vacancy/{id}/apply',[JobVacancyController::class,'processApplication'])->name("vacancy.processApplication");



    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');


   
});

require __DIR__.'/auth.php';
