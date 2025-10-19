<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-100 leading-tight">
            {{ __('My Job Applications') }}
        </h2>
    </x-slot>

    <div class="py-10 bg-gray-950 min-h-screen">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('success'))
                <div class="bg-purple-600 text-white font-semibold px-4 py-3 rounded-md shadow mb-6">
                    {{ session('success') }}
                </div>
            @endif

            @forelse ($applications as $application)
                <div class="bg-gray-900 border border-gray-800 rounded-2xl shadow-lg p-6 hover:border-purple-500 transition-all duration-300">
                    <div class="flex items-start justify-between mb-4">
                        <div>
                            <h3 class="text-2xl font-semibold text-white">{{ $application->jobVacancy->title }}</h3>
                            <p class="text-gray-400 text-sm">{{ $application->jobVacancy->company->name }}</p>
                            <p class="text-gray-500 text-sm">{{ $application->jobVacancy->location }}</p>
                            <p class="text-gray-600 text-xs mt-1">{{ $application->jobVacancy->created_at->format('d M Y') }}</p>
                            <p class="mt-1 text-sm text-gray-300">
                                Applied With: <span class="font-semibold text-gray-200">{{ basename($application->resume->filename) }}</span>
                                · <a href="{{ Storage::disk('cloud')->url($application->resume->fileUri) }}" target="_blank" class="text-purple-400 hover:underline">View Resume</a>
                            </p>
                        </div>

                        <div class="flex flex-col items-end gap-2">
                            <span class="text-sm font-medium px-3 py-1 rounded-md
                                {{ $application->jobVacancy->type === 'Full-Time' ? 'bg-blue-700 text-white' : 'bg-gray-700 text-gray-200' }}">
                                {{ $application->jobVacancy->type }}
                            </span>
                            <div class="flex gap-2 mt-1">
                                <span class="text-xs bg-yellow-500/90 text-black font-semibold px-3 py-1 rounded-md">Status: {{ ucfirst($application->status) }}</span>
                                <span class="text-xs bg-purple-500 text-white font-semibold px-3 py-1 rounded-md">Score: {{ $application->aiGeneratedScore }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 border-t border-gray-800 pt-4">
                        <h4 class="text-gray-200 font-semibold mb-2">AI Feedback:</h4>
                        <p class="text-gray-400 text-sm leading-relaxed whitespace-pre-line">
                            {{ $application->aiGeneratedFeedback }}
                        </p>
                    </div>
                </div>
            @empty
                <div class="text-center py-20">
                    <p class="text-gray-400 text-lg">You haven’t submitted any job applications yet.</p>
                </div>
            @endforelse
        </div>
    </div>
</x-app-layout>
