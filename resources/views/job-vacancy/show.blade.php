<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-200 leading-tight">
            {{ $job->title }}
        </h2>
    </x-slot>

    <div class="py-10 bg-[#111] min-h-screen text-white">
        <div class="max-w-5xl mx-auto px-4">

            <div class="mb-4">
                <a href="{{ route('dashboard') }}" class="text-indigo-400 hover:underline">
                    ← Back to Jobs
                </a>
            </div>

            <div class="bg-black rounded-xl shadow-lg p-6 flex flex-col md:flex-row justify-between">
                <div class="flex-1 pr-0 md:pr-8">

                    <!-- Title + Company -->
                    <div class="flex justify-between items-start">
                        <div>
                            <h1 class="text-2xl font-bold">{{ $job->title }}</h1>
                            <p class="text-gray-400">{{ $job->company->name ?? 'Company Name' }}</p>
                            <p class="text-gray-400 text-sm mt-1">
                                {{ $job->location }} 
                                @if($job->salary)
                                    <span class="mx-2">•</span> 
                                    ${{ number_format($job->salary, 0, '.', ',') }}
                                @endif
                                <span class="ml-2 inline-block bg-indigo-600 text-xs px-2 py-1 rounded-md">
                                    {{ $job->type }}
                                </span>
                            </p>
                        </div>

                        <a href="{{route( "vacancy.apply",$job->id) }}"
                           class="bg-gradient-to-r from-purple-500 to-pink-500 text-white px-4 py-2 rounded-md text-sm hover:opacity-90 transition">
                           Apply Now
                        </a>
                    </div>

                    <!-- Description -->
                    <div class="mt-8">
                        <h3 class="text-lg font-semibold mb-2">Job Description</h3>
                        <p class="text-gray-300 leading-relaxed">
                            {{ $job->description ?? 'No description provided for this job.' }}
                        </p>
                    </div>
                </div>

                <!-- Overview -->
                <div class="bg-[#1a1a2e] rounded-xl p-5 mt-8 md:mt-0 md:w-64">
                    <h3 class="text-lg font-semibold mb-4">Job Overview</h3>
                    <ul class="space-y-3 text-sm text-gray-300">
                        <li><span class="block text-gray-400">Published Date</span>
                            <span class="text-white">{{ $job->created_at->format('M d, Y') }}</span></li>

                        <li><span class="block text-gray-400">Company</span>
                            <span class="text-white">{{ $job->company->name ?? '-' }}</span></li>

                        <li><span class="block text-gray-400">Location</span>
                            <span class="text-white">{{ $job->location ?? '-' }}</span></li>

                        <li><span class="block text-gray-400">Salary</span>
                            <span class="text-white">
                                @if($job->salary) ${{ number_format($job->salary, 0, '.', ',') }} @else N/A @endif
                            </span></li>

                        <li><span class="block text-gray-400">Type</span>
                            <span class="text-white">{{ $job->type }}</span></li>

                        <li><span class="block text-gray-400">Category</span>
                            <span class="text-white">{{ $job->category->name ?? '-' }}</span></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
