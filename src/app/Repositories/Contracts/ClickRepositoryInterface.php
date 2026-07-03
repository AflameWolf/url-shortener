<?php

namespace App\Repositories\Contracts;

use App\Models\Click;
use App\Models\Link;
use Illuminate\Pagination\LengthAwarePaginator;

interface ClickRepositoryInterface
{
    public function create(Link $link, array $data): Click;
    public function getClicksByLink(Link $link, array $filters = []): LengthAwarePaginator;
    public function countByLink(Link $link): int;
    public function getDailyStats(Link $link, int $days = 7): array;
}
