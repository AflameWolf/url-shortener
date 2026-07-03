{{-- resources/views/links/edit.blade.php --}}
@extends('layouts.app')

@section('title', 'Редактирование ссылки')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-center">
            <div class="w-full max-w-2xl">
                <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                    {{-- Заголовок --}}
                    <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
                        <div class="flex items-center justify-between">
                            <h2 class="text-xl font-bold text-gray-800">
                                <i class="fas fa-edit mr-2 text-blue-600"></i>
                                Редактирование ссылки
                            </h2>
                            <span class="text-sm text-gray-500">
                            <i class="fas fa-link mr-1"></i>
                            {{ $link->short_code }}
                        </span>
                        </div>
                    </div>

                    {{-- Тело --}}
                    <div class="p-6">
                        <form action="{{ route('links.update', $link) }}" method="POST">
                            @csrf
                            @method('PUT')

                            {{-- Оригинальный URL --}}
                            <div class="mb-4">
                                <label for="original_url" class="block text-sm font-medium text-gray-700 mb-1">
                                    <i class="fas fa-globe mr-1 text-gray-400"></i>
                                    Оригинальный URL <span class="text-red-500">*</span>
                                </label>
                                <input type="url"
                                       id="original_url"
                                       name="original_url"
                                       value="{{ old('original_url', $link->original_url) }}"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('original_url') border-red-500 @enderror"
                                       placeholder="https://example.com/page"
                                       required>
                                @error('original_url')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Название --}}
                            <div class="mb-4">
                                <label for="title" class="block text-sm font-medium text-gray-700 mb-1">
                                    <i class="fas fa-tag mr-1 text-gray-400"></i>
                                    Название (опционально)
                                </label>
                                <input type="text"
                                       id="title"
                                       name="title"
                                       value="{{ old('title', $link->title) }}"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('title') border-red-500 @enderror"
                                       placeholder="Название ссылки">
                                @error('title')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Активность --}}
                            <div class="mb-4">
                                <label class="flex items-center cursor-pointer">
                                    <input type="checkbox"
                                           name="is_active"
                                           value="1"
                                           class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                                        {{ $link->is_active ? 'checked' : '' }}>
                                    <span class="ml-2 text-sm text-gray-700">
                                    <i class="fas fa-power-off mr-1 {{ $link->is_active ? 'text-green-600' : 'text-gray-400' }}"></i>
                                    Ссылка активна
                                </span>
                                </label>
                                <p class="mt-1 text-xs text-gray-500">
                                    Если отключить, переход по ссылке будет недоступен
                                </p>
                            </div>

                            {{-- Короткая ссылка (только для просмотра) --}}
                            <div class="mb-6">
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    <i class="fas fa-shortcode mr-1 text-gray-400"></i>
                                    Короткая ссылка
                                </label>
                                <div class="flex items-center bg-gray-50 rounded-lg border border-gray-200 px-4 py-2">
                                    <span class="text-sm text-gray-500">{{ url('/') }}/</span>
                                    <span class="text-sm font-mono text-gray-800 flex-1">{{ $link->short_code }}</span>
                                    <button type="button"
                                            onclick="copyToClipboard('{{ $link->short_url }}')"
                                            class="text-gray-400 hover:text-blue-600 transition ml-2"
                                            title="Копировать">
                                        <i class="fas fa-copy"></i>
                                    </button>
                                </div>
                                <p class="mt-1 text-xs text-gray-500">
                                    Короткая ссылка генерируется автоматически и не может быть изменена
                                </p>
                            </div>

                            {{-- Информация о ссылке --}}
                            <div class="bg-gray-50 rounded-lg p-4 mb-6">
                                <div class="grid grid-cols-2 gap-4 text-sm">
                                    <div>
                                        <span class="text-gray-500">Создана:</span>
                                        <span class="text-gray-800 font-medium ml-1">
                                        {{ $link->created_at->format('d.m.Y H:i') }}
                                    </span>
                                    </div>
                                    <div>
                                        <span class="text-gray-500">Переходов:</span>
                                        <span class="text-gray-800 font-medium ml-1">
                                        {{ $link->clicks_count }}
                                    </span>
                                    </div>
                                    @if($link->expires_at)
                                        <div class="col-span-2">
                                            <span class="text-gray-500">Истекает:</span>
                                            <span class="text-gray-800 font-medium ml-1">
                                            {{ $link->expires_at->format('d.m.Y H:i') }}
                                                @if($link->expires_at->isPast())
                                                    <span class="text-red-600 ml-2">(Истекла)</span>
                                                @endif
                                        </span>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            {{-- Кнопки --}}
                            <div class="flex items-center justify-between pt-4 border-t border-gray-200">
                                <a href="{{ route('links.show', $link) }}"
                                   class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition">
                                    <i class="fas fa-arrow-left mr-2"></i>
                                    Назад
                                </a>
                                <button type="submit"
                                        class="inline-flex items-center px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition duration-200">
                                    <i class="fas fa-save mr-2"></i>
                                    Сохранить изменения
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            function copyToClipboard(text) {
                navigator.clipboard.writeText(text).then(() => {
                    showNotification('Ссылка скопирована!', 'success');
                }).catch(() => {
                    // Fallback для старых браузеров
                    const input = document.createElement('input');
                    input.value = text;
                    document.body.appendChild(input);
                    input.select();
                    document.execCommand('copy');
                    document.body.removeChild(input);
                    showNotification('Ссылка скопирована!', 'success');
                });
            }

            function showNotification(message, type = 'success') {
                const colors = {
                    success: 'bg-green-500',
                    error: 'bg-red-500',
                    info: 'bg-blue-500'
                };

                const notification = document.createElement('div');
                notification.className = `fixed bottom-4 right-4 ${colors[type]} text-white px-6 py-3 rounded-lg shadow-lg z-50 transition-all duration-300 transform translate-y-0`;
                notification.textContent = message;

                document.body.appendChild(notification);

                setTimeout(() => {
                    notification.style.transform = 'translateY(100%)';
                    notification.style.opacity = '0';
                    setTimeout(() => notification.remove(), 300);
                }, 3000);
            }
        </script>
    @endpush
@endsection
