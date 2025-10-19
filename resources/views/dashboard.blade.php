@php
    $current = request()->all();
@endphp

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-200 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-10 bg-[#111] min-h-screen">
        <div class="max-w-5xl mx-auto px-4">

            <!-- Welcome -->
            <div class="bg-black rounded-xl shadow-lg p-6 text-white">
                <h1 class="text-2xl font-semibold mb-6">Welcome back, {{ Auth::user()->name ?? 'User' }}!</h1>

                <!-- Search + Filters -->
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
                
                <form action="{{ route('dashboard') }}" method="get" class="flex space-x-2 mb-8">
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Search for a job"
                        class="w-full p-2 rounded-l-md bg-gray-800 text-white border border-gray-700 focus:outline-none">
                    <button type="submit" class="px-4 py-2 bg-purple-600 hover:bg-purple-700 rounded-r-md">Search</button>

                    @foreach ($filters->except('search') as $key => $value)
                        <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                    @endforeach
                </form>
                <div class="flex space-x-2 mb-4">
                    @foreach (['Full-Time', 'Remote', 'Hybrid', 'Contract'] as $type)
                        <a href="{{ route('dashboard', array_merge($current, ['type' => $type])) }}"
                        class="px-4 py-2 rounded-md text-sm
                                {{ request('type') === $type ? 'bg-purple-600' : 'bg-gray-700 hover:bg-gray-600' }}">
                        {{ $type }}
                        </a>
                    @endforeach

                    @if(request('type'))
                        <a href="{{ route('dashboard', collect($current)->except('type')->toArray()) }}"
                        class="px-4 py-2 rounded-md bg-red-600 text-sm text-white">
                            Clear
                        </a>
                    @endif
                </div>


                </div>

                <!-- Jobs List -->
                <div class="space-y-4">
                    @foreach ($vacancies as $vacancy)
                        <div class="flex justify-between items-start bg-[#0d0d0d] hover:bg-[#1b1b1b] transition rounded-lg p-4 border border-gray-800">
                            <div>
                                <a href="{{ route('vacancy.show',$vacancy->id) }}"><h3 class="text-blue-400 font-semibold text-lg">{{$vacancy->title}}</h3></a>
                                <p class="text-gray-400 text-sm">{{$vacancy->company->name}} - {{$vacancy->location}}</p>
                                <p class="text-gray-500 text-sm mt-1">$ {{number_format($vacancy->salary,2)  }} / month</p>
                            </div>
                            <span class="px-3 py-1 bg-blue-600 text-white text-xs rounded-md h-fit">
                                {{$vacancy->type}}
                            </span>
                        </div>
                    @endforeach
                </div>
                {{ $vacancies->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
