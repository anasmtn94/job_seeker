<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-200 leading-tight">
            {{ $job->title }} - Apply
        </h2>
    </x-slot>

    <div class="py-12 bg-[#111] min-h-screen text-white">
        <div class="max-w-4xl mx-auto bg-black shadow-lg rounded-lg p-6">

            <!-- Back -->
            <a href="{{ route('vacancy.show', $job->id) }}" class="text-indigo-400 hover:underline">
                ← Back to Job Details
            </a>

            <!-- Job Info -->
            <div class="flex justify-between items-start mt-6">
                <div>
                    <h1 class="text-2xl font-semibold">{{ $job->title }}</h1>
                    <p class="text-gray-400">{{ $job->company->name ?? 'Company' }}</p>
                    <p class="text-gray-400 text-sm mt-1">
                        {{ $job->location ?? 'Location' }}
                        @if($job->salary)
                            <span class="mx-2">•</span> ${{ number_format($job->salary, 0, '.', ',') }}
                        @endif
                        <span class="ml-2 inline-block bg-indigo-600 text-xs px-2 py-1 rounded-md">{{ $job->type }}</span>
                    </p>
                </div>
            </div>

            <!-- Form -->
            <form action="{{ route('vacancy.processApplication', $job->id) }}" method="POST" enctype="multipart/form-data" class="mt-10" 
                  x-data="{ selectedResume: '', newFileName: '', dragOver: false }">
                @csrf

                <h3 class="text-lg font-semibold mb-4">Choose Your Resume</h3>

                <!-- Existing Resumes -->
                <div class="mb-6 space-y-2">
                    <p class="text-gray-400 text-sm mb-2">Select from your existing resumes:</p>

                    @foreach ($resumes as $resume)
                        <label class="flex items-center space-x-2 cursor-pointer">
                            <input type="radio" name="resume_choice" value="{{ $resume->id }}"
                                   x-model="selectedResume"
                                   class="text-indigo-500 focus:ring-indigo-500">
                            <span class="text-gray-200">
                                {{ $resume->filename }}
                                <span class="text-gray-500 text-sm">(Last updated: {{ $resume->updated_at->format('M d, Y') }})</span>
                            </span>
                        </label>
                    @endforeach
                </div>

                <!-- Upload New Resume -->
                <div class="mb-6">
                    <label class="flex items-center space-x-2 cursor-pointer">
                        <input type="radio" name="resume_choice" value="new"
                               x-model="selectedResume"
                               class="text-indigo-500 focus:ring-indigo-500">
                        <span class="text-gray-200">Upload a new resume:</span>
                    </label>

                    <div 
                        @click="$refs.fileInput.click()" 
                        @dragover.prevent="dragOver = true"
                        @dragleave.prevent="dragOver = false"
                        @drop.prevent="
                            dragOver = false;
                            const file = $event.dataTransfer.files[0];
                            if (file && file.type === 'application/pdf' && file.size <= 5 * 1024 * 1024) {
                                $refs.fileInput.files = $event.dataTransfer.files;
                                newFileName = file.name;
                                selectedResume = 'new';
                            } else {
                                alert('Please upload a valid PDF under 5MB.');
                            }
                        "
                        class="mt-2 border-2 border-dashed rounded-lg p-6 text-center transition cursor-pointer"
                        :class="dragOver ? 'border-blue-500 bg-[#0f172a]/40' : 'border-gray-600 hover:border-blue-400'"
                    >
                        <input type="file" x-ref="fileInput" name="resume_file" class="hidden" accept=".pdf"
                               @change="
                                   const file = $event.target.files[0];
                                   if (file && file.type === 'application/pdf' && file.size <= 5 * 1024 * 1024) {
                                       newFileName = file.name;
                                       selectedResume = 'new';
                                   } else {
                                       alert('Please upload a valid PDF under 5MB.');
                                       $event.target.value = '';
                                   }
                               ">

                        <template x-if="!newFileName">
                            <p class="text-gray-400">Click or Drag & Drop PDF (Max 5MB)</p>
                        </template>
                        <template x-if="newFileName">
                            <p class="text-blue-400 font-medium" x-text="newFileName"></p>
                        </template>
                    </div>
                </div>

                <!-- Submit -->
                <div class="mt-8">
                    <button type="submit"
                            class="w-full bg-gradient-to-r from-purple-500 to-pink-500 text-white py-3 rounded-md font-semibold text-sm hover:opacity-90 transition">
                        Apply Now
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
