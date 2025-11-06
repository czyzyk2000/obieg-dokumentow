<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <a href="{{ route('documents.index') }}" class="mr-4 text-gray-600 hover:text-gray-900">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                </a>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ __('Szczegóły Dokumentu') }}
                </h2>
            </div>
            <div class="flex items-center space-x-2">
                @can('update', $document)
                    <a href="{{ route('documents.edit', $document) }}" 
                       class="inline-flex items-center px-3 py-2 bg-yellow-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-600">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        Edytuj
                    </a>
                @endcan
                @can('delete', $document)
                    <form action="{{ route('documents.destroy', $document) }}" 
                          method="POST" 
                          onsubmit="return confirm('Czy na pewno chcesz usunąć ten dokument?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" 
                                class="inline-flex items-center px-3 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                            Usuń
                        </button>
                    </form>
                @endcan
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- Messages --}}
            @if(session('success'))
                <div class="mb-4 bg-green-50 border-l-4 border-green-400 p-4 rounded-md">
                    <div class="flex">
                        <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        <p class="ml-3 text-sm text-green-700">{{ session('success') }}</p>
                    </div>
                </div>
            @endif

            @if(session('error'))
                <div class="mb-4 bg-red-50 border-l-4 border-red-400 p-4 rounded-md">
                    <div class="flex">
                        <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                        </svg>
                        <p class="ml-3 text-sm text-red-700">{{ session('error') }}</p>
                    </div>
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                {{-- Main Content --}}
                <div class="lg:col-span-2 space-y-6">
                    {{-- Document Details Card --}}
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="flex items-start justify-between mb-6">
                                <div class="flex-1">
                                    <h3 class="text-2xl font-bold text-gray-900 mb-2">{{ $document->title }}</h3>
                                    <div class="flex items-center space-x-4 text-sm text-gray-500">
                                        <span class="flex items-center">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                            </svg>
                                            {{ $document->user->name }}
                                        </span>
                                        <span class="flex items-center">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                            </svg>
                                            {{ $document->created_at->format('d.m.Y H:i') }}
                                        </span>
                                    </div>
                                </div>
                                <span class="px-3 py-1 text-sm font-semibold rounded-full
                                    @if($document->status->value === 'draft') bg-gray-100 text-gray-800
                                    @elseif($document->status->value === 'pending_manager_approval') bg-yellow-100 text-yellow-800
                                    @elseif($document->status->value === 'pending_finance_approval') bg-blue-100 text-blue-800
                                    @elseif($document->status->value === 'approved') bg-green-100 text-green-800
                                    @else bg-red-100 text-red-800
                                    @endif">
                                    {{ $document->status->label() }}
                                </span>
                            </div>

                            <div class="border-t border-gray-200 pt-6">
                                <h4 class="text-sm font-medium text-gray-900 mb-2">Opis / Uzasadnienie</h4>
                                <p class="text-gray-700 whitespace-pre-line">{{ $document->content }}</p>
                            </div>

                            <div class="border-t border-gray-200 mt-6 pt-6">
                                <div class="grid grid-cols-2 gap-6">
                                    <div>
                                        <h4 class="text-sm font-medium text-gray-500 mb-1">Kwota</h4>
                                        <p class="text-2xl font-bold text-gray-900">{{ $document->formatted_amount }}</p>
                                        @if($document->requiresFinanceApproval())
                                            <p class="text-xs text-blue-600 mt-1">Wymaga akceptacji finansowej</p>
                                        @endif
                                    </div>
                                    @if($document->hasFile())
                                        <div>
                                            <h4 class="text-sm font-medium text-gray-500 mb-1">Załącznik</h4>
                                            <a href="{{ route('documents.download-file', $document) }}" 
                                               class="inline-flex items-center text-indigo-600 hover:text-indigo-900">
                                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                                </svg>
                                                <div>
                                                    <div class="font-medium">{{ $document->file_name }}</div>
                                                    <div class="text-xs text-gray-500">{{ $document->file_size }}</div>
                                                </div>
                                            </a>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- History Timeline --}}
                    @if($document->history->count() > 0)
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6">
                                <h3 class="text-lg font-semibold text-gray-900 mb-4">Historia Dokumentu</h3>
                                <div class="flow-root">
                                    <ul class="-mb-8">
                                        @foreach($document->history as $history)
                                            <li>
                                                <div class="relative pb-8">
                                                    @if(!$loop->last)
                                                        <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200" aria-hidden="true"></span>
                                                    @endif
                                                    <div class="relative flex space-x-3">
                                                        <div>
                                                            <span class="h-8 w-8 rounded-full flex items-center justify-center ring-8 ring-white
                                                                @if($history->isApproval()) bg-green-500
                                                                @elseif($history->isRejection()) bg-red-500
                                                                @else bg-blue-500
                                                                @endif">
                                                                <svg class="h-5 w-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                                    @if($history->isApproval())
                                                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                                                    @elseif($history->isRejection())
                                                                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                                                    @else
                                                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                                                    @endif
                                                                </svg>
                                                            </span>
                                                        </div>
                                                        <div class="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4">
                                                            <div>
                                                                <p class="text-sm text-gray-900">
                                                                    <strong>{{ $history->user->name }}</strong> - {{ $history->action_label }}
                                                                </p>
                                                                @if($history->comment)
                                                                    <p class="mt-1 text-sm text-gray-600 italic">"{{ $history->comment }}"</p>
                                                                @endif
                                                            </div>
                                                            <div class="text-right text-sm whitespace-nowrap text-gray-500">
                                                                <time>{{ $history->relative_time }}</time>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                {{-- Sidebar Actions --}}
                <div class="space-y-6">
                    {{-- Submit for Approval --}}
                    @can('submit', $document)
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6">
                                <h3 class="text-lg font-semibold text-gray-900 mb-4">Wyślij do Akceptacji</h3>
                                @if(auth()->user()->manager)
                                    <p class="text-sm text-gray-600 mb-4">
                                        Dokument zostanie wysłany do Twojego menedżera: <strong>{{ auth()->user()->manager->name }}</strong>
                                    </p>
                                @endif
                                <form action="{{ route('documents.submit', $document) }}" method="POST">
                                    @csrf
                                    <button type="submit" 
                                            class="w-full inline-flex justify-center items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                                        </svg>
                                        Wyślij do Akceptacji
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endcan

                    {{-- Manager Approval --}}
                    @can('approveAsManager', $document)
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6">
                                <h3 class="text-lg font-semibold text-gray-900 mb-4">Akceptacja Menedżera</h3>
                                
                                {{-- Approve Form --}}
                                <form action="{{ route('documents.approve-manager', $document) }}" method="POST" class="mb-3">
                                    @csrf
                                    <textarea name="comment" 
                                              rows="2" 
                                              placeholder="Komentarz (opcjonalnie)"
                                              class="w-full mb-3 rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 text-sm"></textarea>
                                    <button type="submit" 
                                            class="w-full inline-flex justify-center items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                        </svg>
                                        Zaakceptuj
                                    </button>
                                </form>

                                {{-- Reject Form --}}
                                <form action="{{ route('documents.reject-manager', $document) }}" method="POST" x-data="{ showComment: false }">
                                    @csrf
                                    <button type="button" 
                                            @click="showComment = !showComment"
                                            class="w-full inline-flex justify-center items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                        Odrzuć
                                    </button>
                                    <div x-show="showComment" x-cloak class="mt-3">
                                        <textarea name="comment" 
                                                  rows="2" 
                                                  placeholder="Powód odrzucenia (wymagany)"
                                                  required
                                                  class="w-full mb-2 rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 text-sm"></textarea>
                                        <button type="submit" 
                                                class="w-full inline-flex justify-center items-center px-4 py-2 bg-red-700 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-800">
                                            Potwierdź Odrzucenie
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    @endcan

                    {{-- Finance Approval --}}
                    @can('approveAsFinance', $document)
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6">
                                <h3 class="text-lg font-semibold text-gray-900 mb-4">Akceptacja Finansowa</h3>
                                
                                {{-- Approve Form --}}
                                <form action="{{ route('documents.approve-finance', $document) }}" method="POST" class="mb-3">
                                    @csrf
                                    <textarea name="comment" 
                                              rows="2" 
                                              placeholder="Komentarz (opcjonalnie)"
                                              class="w-full mb-3 rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 text-sm"></textarea>
                                    <button type="submit" 
                                            class="w-full inline-flex justify-center items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                        </svg>
                                        Zaakceptuj Finansowo
                                    </button>
                                </form>

                                {{-- Reject Form --}}
                                <form action="{{ route('documents.reject-finance', $document) }}" method="POST" x-data="{ showComment: false }">
                                    @csrf
                                    <button type="button" 
                                            @click="showComment = !showComment"
                                            class="w-full inline-flex justify-center items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                        Odrzuć
                                    </button>
                                    <div x-show="showComment" x-cloak class="mt-3">
                                        <textarea name="comment" 
                                                  rows="2" 
                                                  placeholder="Powód odrzucenia (wymagany)"
                                                  required
                                                  class="w-full mb-2 rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 text-sm"></textarea>
                                        <button type="submit" 
                                                class="w-full inline-flex justify-center items-center px-4 py-2 bg-red-700 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-800">
                                            Potwierdź Odrzucenie
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    @endcan

                    {{-- Document Info --}}
                    <div class="bg-blue-50 border-l-4 border-blue-400 p-4 rounded-md">
                        <div class="flex">
                            <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                            </svg>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-blue-800">Informacje</h3>
                                <div class="mt-2 text-sm text-blue-700">
                                    <p>Status: <strong>{{ $document->status->label() }}</strong></p>
                                    <p class="mt-1">Utworzono: {{ $document->created_at->format('d.m.Y H:i') }}</p>
                                    @if($document->updated_at != $document->created_at)
                                        <p class="mt-1">Zaktualizowano: {{ $document->updated_at->format('d.m.Y H:i') }}</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
