<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum ContactMessageStatus: string implements HasColor, HasLabel
{
    case New = 'new';
    case Read = 'read';
    case Replied = 'replied';
    case Archived = 'archived';

    public function getLabel(): string
    {
        return match ($this) {
            self::New => 'Yeni',
            self::Read => 'Okundu',
            self::Replied => 'Yanıtlandı',
            self::Archived => 'Arşivlendi',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::New => 'danger',
            self::Read => 'warning',
            self::Replied => 'success',
            self::Archived => 'gray',
        };
    }
}
