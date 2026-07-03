@extends('layouts.app')

@section('title', 'Мои ссылки')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Мои ссылки</h1>
            <a href="{{ route('links.create') }}"
               class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition">
                <i class="fas fa-plus mr-2"></i>Создать ссылку
            </a>
        </div>

        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Короткая ссылка
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Оригинальный URL
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Клики
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Статус
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Действия
                        </th>
                    </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                    @forelse($links as $link)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4">
                                <a href="{{ $link->short_url }}"
                                   target="_blank"
                                   class="text-blue-600 hover:text-blue-800">
                                    {{ $link->short_code }}
                                </a>
                            </td>
                            <td class="px-6 py-4">
                                <div class="max-w-xs truncate text-gray-600" title="{{ $link->original_url }}">
                                    {{ $link->original_url }}
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="font-medium">{{ $link->clicks_count }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 text-xs font-medium rounded-full
                                    {{ $link->isAvailable() ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $link->isAvailable() ? 'Активна' : 'Неактивна' }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex space-x-2">
                                    <a href="{{ route('links.show', $link) }}"
                                       class="text-gray-500 hover:text-blue-600 transition">
                                        Просмотр
                                    </a>
                                    <a href="{{ route('links.edit', $link) }}"
                                       class="text-gray-500 hover:text-blue-600 transition">
                                        Изменить
                                    </a>
                                    <form action="{{ route('links.toggle', $link) }}"
                                          method="POST"
                                          class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit"
                                                class="text-gray-500 hover:text-yellow-600 transition"
                                                title="{{ $link->is_active ? 'Деактивировать' : 'Активировать' }}">
                                        {{ $link->is_active ? 'Отключить' : 'Включить' }}
                                        </button>
                                    </form>
                                    <form action="{{ route('links.destroy', $link) }}"
                                          method="POST"
                                          class="inline"
                                          onsubmit="return confirm('Удалить ссылку?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="text-gray-500 hover:text-red-600 transition">
                                            Удалить
                                        </button>
                                    </form>
                                    <button onclick="copyToClipboard('{{ $link->short_url }}')"
                                            class="text-gray-500 hover:text-gray-700 transition"
                                            title="Копировать ссылку">
                                        Копировать
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                                <i class="fas fa-link text-4xl mb-4 block"></i>
                                <p class="text-lg">У вас пока нет ссылок</p>
                                <a href="{{ route('links.create') }}"
                                   class="inline-block mt-4 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition">
                                    Создать первую ссылку
                                </a>
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-6">
            {{ $links->links() }}
        </div>
    </div>

    @push('scripts')
        <script>
            function copyToClipboard(text) {
                navigator.clipboard.writeText(text).then(() => {
                    showNotification('Ссылка скопирована!');
                });
            }

            function showNotification(message) {
                const notification = document.createElement('div');
                notification.className = 'fixed bottom-4 right-4 bg-gray-800 text-white px-6 py-3 rounded-lg shadow-lg';
                notification.textContent = message;
                document.body.appendChild(notification);
                setTimeout(() => notification.remove(), 3000);
            }
        </script>
    @endpush
@endsection
