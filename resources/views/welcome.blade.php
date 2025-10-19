<x-main-layout title="Shaghalni">
    <div 
        x-data="{ show: false }" 
        x-init="setTimeout(() => show = true, 100)" 
        class="min-h-screen flex flex-col items-center justify-center bg-black text-center text-white px-4 overflow-hidden"
    >
        <div 
            x-show="show" 
            x-transition:enter="transition ease-out duration-700"
            x-transition:enter-start="opacity-0 translate-y-6"
            x-transition:enter-end="opacity-100 translate-y-0"
        >
            <h1 class="text-sm tracking-widest text-gray-500 mb-6">Shaghalni</h1>
            
            <h2 class="text-5xl md:text-6xl font-semibold leading-tight">
                Find your
                <br>
                <span class="italic font-serif bg-gradient-to-r from-gray-300 via-purple-400 to-pink-400 bg-clip-text text-transparent">
                    Dream Job
                </span>
            </h2>

            <p class="mt-4 text-gray-400 text-sm md:text-base">
                connect with top employers, and find exciting opportunities
            </p>

            <div class="mt-8 flex space-x-4 justify-center">
                <a href="{{ route('register') }}"
                   class="px-6 py-2 bg-gray-800 hover:bg-gray-700 rounded-md text-sm transition">
                   Create an Account
                </a>

                <a href="{{ route('login') }}"
                   class="px-6 py-2 bg-gradient-to-r from-purple-500 to-pink-500 hover:opacity-90 rounded-md text-sm transition">
                   Login
                </a>
            </div>
        </div>
    </div>
</x-main-layout>
