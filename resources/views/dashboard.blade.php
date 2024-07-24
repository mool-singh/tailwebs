<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden p-5 shadow-sm sm:rounded-lg">
                
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <!-- Card 1 -->
                    <a href="{{ route('students.index') }}">
                        <div class="bg-white p-4 rounded-lg shadow-md">
                            <h2 class="text-2xl font-semibold mb-2">{{ $students }}</h2>
                            <p class="text-gray-600">Students</p>
                          </div>
                    </a>
              
        
                </div>


            </div>
        </div>
    </div>
</x-app-layout>
