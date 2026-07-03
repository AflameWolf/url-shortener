<?php

namespace Database\Factories;

use App\Models\Link;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class LinkFactory extends Factory
{
    protected $model = Link::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'original_url' => $this->faker->url(),
            'short_code' => $this->faker->unique()->regexify('[A-Za-z0-9]{6}'),
            'title' => $this->faker->sentence(3),
            'expires_at' => null,
            'is_active' => true,
            'clicks_count' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    /**
     * Установить ссылку как неактивную
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }

    /**
     * Установить ссылку с истекающим сроком
     */
    public function expired(): static
    {
        return $this->state(fn (array $attributes) => [
            'expires_at' => now()->subDay(),
        ]);
    }

    /**
     * Установить ссылку с определённым количеством кликов
     */
    public function withClicks(int $count): static
    {
        return $this->state(fn (array $attributes) => [
            'clicks_count' => $count,
        ]);
    }
}
