<?php

namespace App\Http\Controllers;

use App\Http\Requests\LinkStoreRequest;
use App\Http\Requests\LinkUpdateRequest;
use App\Models\Link;
use App\Services\LinkService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class LinkController extends Controller
{
    protected LinkService $linkService;

    public function __construct(LinkService $linkService)
    {
        $this->linkService = $linkService;
    }

    /**
     * Страница со списком ссылок пользователя
     */
    public function index(Request $request): View
    {
        $links = $this->linkService->getUserLinks(
            Auth::user(),
            $request->all()
        );

        return view('links.index', compact('links'));
    }

    /**
     * Страница создания ссылки
     */
    public function create(): View
    {
        return view('links.create');
    }

    /**
     * Сохранение новой ссылки
     */
    public function store(LinkStoreRequest $request)
    {
        $link = $this->linkService->createLink(
            Auth::user(),
            $request->validated()
        );

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Ссылка создана',
                'link' => $link->load('user'),
                'short_url' => $link->short_url,
            ]);
        }

        return redirect()->route('links.index')
            ->with('success', 'Ссылка создана');
    }

    /**
     * Просмотр ссылки и статистики
     */
    public function show(Link $link): View
    {
        $this->authorize('view', $link);

        $stats = $this->linkService->getLinkStats($link);
        $clicks = $link->clicks()->latest('clicked_at')->paginate(10);

        return view('links.show', compact('link', 'stats', 'clicks'));
    }

    /**
     * Страница редактирования ссылки
     */
    public function edit(Link $link): View
    {
        $this->authorize('update', $link);

        return view('links.edit', compact('link'));
    }

    /**
     * Обновление ссылки
     */
    public function update(LinkUpdateRequest $request, Link $link)
    {
        $this->authorize('update', $link);

        $updatedLink = $this->linkService->updateLink($link, $request->validated());

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Ссылка обновлена',
                'link' => $updatedLink,
            ]);
        }

        return redirect()->route('links.show', $link)
            ->with('success', 'Ссылка обновлена');
    }

    /**
     * Удаление ссылки
     */
    public function destroy(Link $link, Request $request)
    {
        $this->authorize('delete', $link);

        $this->linkService->deleteLink($link);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Ссылка удалена',
            ]);
        }

        return redirect()->route('links.index')
            ->with('success', 'Ссылка удалена');
    }

    /**
     * Переключение активности ссылки
     */
    public function toggleActive(Link $link, Request $request)
    {
        $this->authorize('update', $link);

        $updatedLink = $this->linkService->toggleActive($link);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Статус обновлен',
                'is_active' => $updatedLink->is_active,
            ]);
        }

        return redirect()->route('links.index')
            ->with('success', 'Статус обновлен');
    }

    /**
     * API: Получение статистики ссылки
     */
    public function stats(Link $link): JsonResponse
    {
        $this->authorize('view', $link);

        $stats = $this->linkService->getLinkStats($link);

        return response()->json([
            'success' => true,
            'stats' => $stats,
        ]);
    }

    /**
     * Редирект по короткой ссылке
     */
    public function redirect(string $shortCode, Request $request)
    {
        $link = $this->linkService->findByShortCode($shortCode);

        if (!$link) {
            abort(404, 'Ссылка не найдена');
        }

        if (!$this->linkService->checkLinkAvailability($link)) {
            abort(410, 'Ссылка неактивна или истекла');
        }

        // Регистрируем переход
        app(\App\Services\ClickService::class)->trackClick($link, $request);

        // Увеличиваем счетчик кликов
        $this->linkService->incrementClicks($link);

        return redirect($link->original_url, 302);
    }
}
