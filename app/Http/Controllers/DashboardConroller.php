<?php

namespace App\Http\Controllers;

use App\Models\JobVacancy;
use Illuminate\Http\Request;
use OpenAI\Laravel\Facades\OpenAI;


class DashboardConroller extends Controller
{
    // public function index(){


    //     $vacancies = JobVacancy::query()->latest()->paginate(10)->withQueryString();


    //     return view("dashboard",compact("vacancies"));
    // }

public function index(Request $request)
{
    $query = JobVacancy::query();

    // تعريف الفلاتر المسموح بها
    $allowedFilters = [
        'search', 'type', 'location', 'company', 'salary_min', 'salary_max'
    ];

    // استخراج الفلاتر الموجودة فعلياً بالـ request
    $filters = collect($request->only($allowedFilters))->filter();

    // بناء الاستعلام بشكل ديناميكي
    $query->when($filters->has('search'), function ($q) use ($filters) {
        $q->where(function ($sub) use ($filters) {
            $search = $filters['search'];
            $sub->where('title', 'like', "%{$search}%")
                ->orWhere('location', 'like', "%{$search}%")
                ->orWhereHas('company', fn($c) =>
                    $c->where('name', 'like', "%{$search}%"));
        });
    });

    // باقي الفلاتر التلقائية
    $filters->except('search')->each(function ($value, $key) use ($query) {
        match ($key) {
            'salary_min' => $query->where('salary', '>=', $value),
            'salary_max' => $query->where('salary', '<=', $value),
            default => $query->where($key, $value),
        };
    });

    // التصفّح مع المحافظة على الفلاتر
    $vacancies = $query->paginate(10)->appends($filters->toArray());

    return view('dashboard', compact('vacancies', 'filters'));
}


public function testai(){


    $response = OpenAI::responses()->create([
    'model' => 'gpt-4o-mini',
    'input' => 'Hello!',
]);

echo $response->outputText; // Hello! How can I assist you today?


}


}
