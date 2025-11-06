<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center">
            <a href="{{ route('documents.index') }}" class="mr-4 text-gray-600 hover:text-gray-900">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Nowy Dokument') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form action="{{ route('documents.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        {{-- Title --}}
                        <div class="mb-6">
                            <label for="title" class="block text-sm font-medium text-gray-700 mb-2">
                                Tytuł dokumentu <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   name="title" 
                                   id="title" 
                                   value="{{ old('title') }}"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('title') border-red-500 @enderror"
                                   required
                                   autofocus>
                            @error('title')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Content --}}
                        <div class="mb-6">
                            <label for="content" class="block text-sm font-medium text-gray-700 mb-2">
                                Opis / Uzasadnienie <span class="text-red-500">*</span>
                            </label>
                            <textarea name="content" 
                                      id="content" 
                                      rows="6"
                                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('content') border-red-500 @enderror"
                                      required>{{ old('content') }}</textarea>
                            @error('content')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-xs text-gray-500">Opisz szczegółowo powód utworzenia dokumentu</p>
                        </div>

                        {{-- Amount --}}
                        <div class="mb-6">
                            <label for="amount" class="block text-sm font-medium text-gray-700 mb-2">
                                Kwota (PLN) <span class="text-red-500">*</span>
                            </label>
                            <div class="mt-1 relative rounded-md shadow-sm">
                                <input type="number" 
                                       name="amount" 
                                       id="amount" 
                                       value="{{ old('amount') }}"
                                       step="0.01"
                                       min="0"
                                       class="block w-full rounded-md border-gray-300 pl-3 pr-12 focus:border-indigo-500 focus:ring-indigo-500 @error('amount') border-red-500 @enderror"
                                       required>
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                    <span class="text-gray-500 sm:text-sm">PLN</span>
                                </div>
                            </div>
                            @error('amount')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-xs text-gray-500">
                                <svg class="inline w-4 h-4 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                </svg>
                                Dokumenty o wartości >= 1000 PLN wymagają dodatkowej akceptacji działu finansowego
                            </p>
                        </div>

                        {{-- File Upload --}}
                        <div class="mb-6">
                            <label for="file" class="block text-sm font-medium text-gray-700 mb-2">
                                Załącznik (opcjonalnie)
                            </label>
                            <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md hover:border-indigo-400 transition-colors">
                                <div class="space-y-1 text-center">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                        <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                    <div class="flex text-sm text-gray-600">
                                        <label for="file" class="relative cursor-pointer bg-white rounded-md font-medium text-indigo-600 hover:text-indigo-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-indigo-500">
                                            <span id="file-label">Wybierz plik</span>
                                            <input id="file" name="file" type="file" class="sr-only" accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png" onchange="updateFileName(this)">
                                        </label>
                                        <p class="pl-1">lub przeciągnij i upuść</p>
                                    </div>
                                    <p class="text-xs text-gray-500">PDF, DOC, DOCX, XLS, XLSX, JPG, PNG do 5MB</p>
                                    <p id="file-info" class="text-xs text-indigo-600 font-medium hidden"></p>
                                </div>
                            </div>
                            @error('file')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <script>
                            function updateFileName(input) {
                                const fileLabel = document.getElementById('file-label');
                                const fileInfo = document.getElementById('file-info');
                                
                                if (input.files && input.files[0]) {
                                    const file = input.files[0];
                                    const fileName = file.name;
                                    const fileSize = (file.size / 1024 / 1024).toFixed(2);
                                    
                                    fileLabel.textContent = fileName;
                                    fileInfo.textContent = `Rozmiar: ${fileSize} MB`;
                                    fileInfo.classList.remove('hidden');
                                } else {
                                    fileLabel.textContent = 'Wybierz plik';
                                    fileInfo.classList.add('hidden');
                                }
                            }
                        </script>

                        {{-- Buttons --}}
                        <div class="flex items-center justify-end space-x-3 pt-6 border-t">
                            <a href="{{ route('documents.index') }}" 
                               class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                Anuluj
                            </a>
                            <button type="submit" 
                                    class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                Zapisz Dokument
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Info Box --}}
            <div class="mt-6 bg-blue-50 border-l-4 border-blue-400 p-4 rounded-md">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-blue-800">Informacja o procesie akceptacji</h3>
                        <div class="mt-2 text-sm text-blue-700">
                            <ul class="list-disc list-inside space-y-1">
                                <li>Dokument zostanie zapisany jako szkic</li>
                                <li>Po zapisaniu możesz go edytować lub wysłać do akceptacji</li>
                                <li>Dokumenty < 1000 PLN: akceptacja tylko przez menedżera</li>
                                <li>Dokumenty >= 1000 PLN: akceptacja przez menedżera + dział finansowy</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
