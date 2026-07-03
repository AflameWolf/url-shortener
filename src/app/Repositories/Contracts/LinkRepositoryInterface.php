<?php

namespace App\Repositories\Contracts;

use App\Models\Link;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;

interface LinkRepositoryInterface
{
    public function getAll(array $filters = []): LengthAwarePaginator;
    public function getUserLinks(User $user, array $filters = []): LengthAwarePaginator;
    public function findByShortCode(string $shortCode): ?Link;
    public function findById(int $id): ?Link;
    public function create(array $data): Link;
    public function update(Link $link, array $data): Link;
    public function delete(Link $link): bool;
    public function incrementClicks(Link $link): void;
    public function getStats(Link $link): array;
}
