<?php

namespace App\Services;

use App\Models\Link;
use App\Models\User;
use App\Repositories\Contracts\LinkRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class LinkService
{
    protected LinkRepositoryInterface $repository;
    protected UrlShortenerService $urlShortener;

    public function __construct(
        LinkRepositoryInterface $repository,
        UrlShortenerService $urlShortener
    ) {
        $this->repository = $repository;
        $this->urlShortener = $urlShortener;
    }

    public function getUserLinks(User $user, array $filters = []): LengthAwarePaginator
    {
        return $this->repository->getUserLinks($user, $filters);
    }

    public function findByShortCode(string $shortCode): ?Link
    {
        return $this->repository->findByShortCode($shortCode);
    }

    public function findById(int $id): ?Link
    {
        return $this->repository->findById($id);
    }

    public function createLink(User $user, array $data): Link
    {
        $this->validateUrl($data['original_url']);

        return DB::transaction(function () use ($user, $data) {
            $shortCode = $this->urlShortener->generateUniqueCode();

            $linkData = [
                'user_id' => $user->id,
                'original_url' => $this->normalizeUrl($data['original_url']),
                'short_code' => $shortCode,
                'title' => $data['title'] ?? null,
                'expires_at' => $data['expires_at'] ?? null,
                'is_active' => true,
            ];

            return $this->repository->create($linkData);
        });
    }

    public function updateLink(Link $link, array $data): Link
    {
        $this->validateOwnership($link);

        return DB::transaction(function () use ($link, $data) {
            if (isset($data['original_url'])) {
                $data['original_url'] = $this->normalizeUrl($data['original_url']);
            }

            return $this->repository->update($link, $data);
        });
    }

    public function deleteLink(Link $link): bool
    {
        $this->validateOwnership($link);

        return DB::transaction(function () use ($link) {
            $link->clicks()->delete();
            return $this->repository->delete($link);
        });
    }

    public function toggleActive(Link $link): Link
    {
        $this->validateOwnership($link);

        return $this->repository->update($link, [
            'is_active' => !$link->is_active
        ]);
    }

    public function getLinkStats(Link $link): array
    {
        $this->validateOwnership($link);

        return $this->repository->getStats($link);
    }

    public function checkLinkAvailability(Link $link): bool
    {
        if (!$link->is_active) {
            return false;
        }

        if ($link->expires_at && $link->expires_at->isPast()) {
            return false;
        }

        return true;
    }

    public function incrementClicks(Link $link): void
    {
        $this->repository->incrementClicks($link);
    }

    protected function validateUrl(string $url): void
    {
        $validator = Validator::make(['url' => $url], [
            'url' => 'required|url|max:2048'
        ]);

        if ($validator->fails()) {
            throw new \InvalidArgumentException('Invalid URL provided');
        }
    }

    protected function normalizeUrl(string $url): string
    {
        // Убираем пробелы и лишние символы
        $url = trim($url);

        // Добавляем https:// если нет протокола
        if (!preg_match('~^https?://~i', $url)) {
            $url = 'https://' . $url;
        }

        return $url;
    }

    protected function validateOwnership(Link $link): void
    {
        if (auth()->id() !== $link->user_id) {
            throw new \DomainException('Нет доступа');
        }
    }
}
