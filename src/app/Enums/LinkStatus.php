<?php

namespace App\Enums;

enum LinkStatus: string
{
    case ACTIVE = 'active';
    case EXPIRED = 'expired';
    case INACTIVE = 'inactive';
    case BLOCKED = 'blocked';

    public function label(): string
    {
        return match($this) {
            self::ACTIVE => 'Активна',
            self::EXPIRED => 'Истекла',
            self::INACTIVE => 'Неактивна',
            self::BLOCKED => 'Заблокирована',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::ACTIVE => 'green',
            self::EXPIRED => 'gray',
            self::INACTIVE => 'yellow',
            self::BLOCKED => 'red',
        };
    }
}
