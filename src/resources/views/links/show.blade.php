@extends('layouts.app')

@section('title', 'Просмотр ссылки')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-center">
            <div class="w-full max-w-3xl">
                <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                    {{-- Заголовок --}}
                    <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
                        <div class="flex items-center justify-between">
                            <h2 class="text-xl font-bold text-gray-800">
                                <i class="fas fa-eye mr-2 text-blue-600"></i>
                                {{ $link->title ?? 'Просмотр ссылки' }}
                            </h2>
                            <span class="text-sm text-gray-500">
                                <i class="fas fa-link mr-1"></i>
                                {{ $link->short_code }}
                            </span>
                        </div>
                    </div>

                    {{-- Тело --}}
                    <div class="p-6">
                        {{-- Информация о ссылке --}}
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Короткая ссылка</label>
                                <div class="flex items-center mt-1">
                                    <a href="{{ $link->short_url }}"
                                       target="_blank"
                                       class="text-blue-600 hover:text-blue-800 hover:underline font-medium">
                                        {{ $link->short_url }}
                                    </a>
                                    <button onclick="copyToClipboard('{{ $link->short_url }}')"
                                            class="ml-2 text-gray-400 hover:text-blue-600 transition">
                                        <i class="fas fa-copy"></i>
                                    </button>
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Оригинальный URL</label>
                                <a href="{{ $link->original_url }}"
                                   target="_blank"
                                   class="mt-1 text-blue-600 hover:text-blue-800 hover:underline break-all">
                                    {{ $link->original_url }}
                                </a>
                            </div>

                            @if($link->title)
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Название</label>
                                    <p class="mt-1 text-gray-800">{{ $link->title }}</p>
                                </div>
                            @endif

                            {{-- Статистика в карточках --}}
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                <div class="bg-gray-50 rounded-lg p-4 text-center">
                                    <div class="text-2xl font-bold text-blue-600">{{ $link->clicks_count }}</div>
                                    <div class="text-xs text-gray-500">Переходов</div>
                                </div>
                                <div class="bg-gray-50 rounded-lg p-4 text-center">
                                    <div class="text-2xl font-bold text-green-600">
                                        {{ $link->is_active ? '✅' : '❌' }}
                                    </div>
                                    <div class="text-xs text-gray-500">Статус</div>
                                </div>
                                <div class="bg-gray-50 rounded-lg p-4 text-center">
                                    <div class="text-sm font-bold text-gray-800">
                                        {{ $link->created_at->format('d.m.Y') }}
                                    </div>
                                    <div class="text-xs text-gray-500">Создана</div>
                                </div>
                                <div class="bg-gray-50 rounded-lg p-4 text-center">
                                    <div class="text-sm font-bold text-gray-800">
                                        {{ $link->expires_at ? $link->expires_at->format('d.m.Y') : '∞' }}
                                    </div>
                                    <div class="text-xs text-gray-500">Истекает</div>
                                </div>
                            </div>

                            {{-- История кликов --}}
                            @if($clicks && $clicks->count() > 0)
                                <div class="mt-6">
                                    <h3 class="text-lg font-semibold text-gray-800 mb-3">
                                        <i class="fas fa-history mr-2 text-blue-600"></i>
                                        История переходов
                                    </h3>
                                    <div class="bg-gray-50 rounded-lg overflow-hidden">
                                        <div class="max-h-60 overflow-y-auto">
                                            <table class="min-w-full divide-y divide-gray-200">
                                                <thead class="bg-gray-100 sticky top-0">
                                                <tr>
                                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                        IP-адрес
                                                    </th>
                                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                        User Agent
                                                    </th>
                                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                        Дата и время
                                                    </th>
                                                </tr>
                                                </thead>
                                                <tbody class="bg-white divide-y divide-gray-200">
                                                @foreach($clicks as $click)
                                                    <tr class="hover:bg-gray-50 transition">
                                                        <td class="px-4 py-2 text-sm text-gray-800 font-mono">
                                                            {{ $click->ip_address ?? '—' }}
                                                        </td>
                                                        <td class="px-4 py-2 text-sm text-gray-600 max-w-xs truncate" title="{{ $click->user_agent ?? '—' }}">
                                                            {{ $click->user_agent ? Str::limit($click->user_agent, 50) : '—' }}
                                                        </td>
                                                        <td class="px-4 py-2 text-sm text-gray-600 whitespace-nowrap">
                                                            {{ $click->clicked_at ? \Carbon\Carbon::parse($click->clicked_at)->format('d.m.Y H:i:s') : '—' }}
                                                        </td>
                                                    </tr>
                                                @endforeach
                                                {{ $clicks->links() }}
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div class="mt-6 text-center py-6 bg-gray-50 rounded-lg">
                                    <i class="fas fa-inbox text-gray-300 text-4xl mb-2"></i>
                                    <p class="text-gray-500">Нет данных о переходах</p>
                                </div>
                            @endif
                        </div>

                        <div class="flex flex-wrap items-center justify-between gap-4 pt-6 mt-6 border-t border-gray-200">
                            <a href="{{ route('links.index') }}"
                               class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition">
                                <i class="fas fa-arrow-left mr-2"></i>
                                К списку
                            </a>
                            <div class="flex flex-wrap gap-2">
                                <a href="{{ route('links.edit', $link) }}"
                                   class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition">
                                    <i class="fas fa-edit mr-2"></i>
                                    Редактировать
                                </a>
                                <form action="{{ route('links.destroy', $link) }}"
                                      method="POST"
                                      class="inline"
                                      onsubmit="return confirm('Удалить ссылку? Это действие нельзя отменить.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="inline-flex items-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-lg transition">
                                        <i class="fas fa-trash mr-2"></i>
                                        Удалить
                                    </button>
                                </form>
                            </div>
                        </div>
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
