<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum QuoteRequestStatus: string implements HasColor, HasLabel
{
    case New = 'new';
    case Reviewing = 'reviewing';
    case Quoted = 'quoted';
    case Closed = 'closed';
    case Archived = 'archived';

    public function getLabel(): string
    {
        return match ($this) {
            self::New => 'Yeni',
            self::Reviewing => 'İnceleniyor',
            self::Quoted => 'Teklif Verildi',
            self::Closed => 'Kapatıldı',
            self::Archived => 'Arşivlendi',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::New => 'danger',
            self::Reviewing => 'warning',
            self::Quoted => 'info',
            self::Closed => 'success',
            self::Archived => 'gray',
        };
    }
}
