
@extends('layouts.app')

@section('title', 'Создать ссылку')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-2xl mx-auto">
            <h1 class="text-2xl font-bold text-gray-800 mb-6">Создать короткую ссылку</h1>

            <div class="bg-white rounded-xl shadow-lg p-6">
                <form action="{{ route('links.store') }}" method="POST">
                    @csrf

                    <div class="mb-4">
                        <label for="original_url" class="block text-sm font-medium text-gray-700 mb-1">
                            Оригинальный URL *
                        </label>
                        <input type="url"
                               name="original_url"
                               id="original_url"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('original_url') border-red-500 @enderror"
                               placeholder="https://example.com/page"
                               value="{{ old('original_url') }}"
                               required>
                        @error('original_url')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="title" class="block text-sm font-medium text-gray-700 mb-1">
                            Название (опционально)
                        </label>
                        <input type="text"
                               name="title"
                               id="title"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               placeholder="Название ссылки"
                               value="{{ old('title') }}">
                    </div>

                    <div class="mb-6">
                        <label for="expires_at" class="block text-sm font-medium text-gray-700 mb-1">
                            Истекает (опционально)
                        </label>
                        <input type="datetime-local"
                               name="expires_at"
                               id="expires_at"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('expires_at') border-red-500 @enderror"
                               value="{{ old('expires_at') }}">
                        @error('expires_at')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-sm text-gray-500">
                            Если не указать, ссылка будет действовать бесконечно
                        </p>
                    </div>

                    <div class="flex justify-end space-x-3">
                        <a href="{{ route('links.index') }}"
                           class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                            Отмена
                        </a>
                        <button type="submit"
                                class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition">
                            <i class="fas fa-link mr-2"></i>Создать ссылку
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
