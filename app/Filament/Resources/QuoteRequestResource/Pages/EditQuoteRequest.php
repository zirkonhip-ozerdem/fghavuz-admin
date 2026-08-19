<?php

namespace App\Filament\Resources\QuoteRequestResource\Pages;

use App\Enums\QuoteRequestStatus;
use App\Filament\Resources\QuoteRequestResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditQuoteRequest extends EditRecord
{
    protected static string $resource = QuoteRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make()];
    }

    /**
     * Admin teklif detayına girdiğinde 'Yeni' durumunu otomatik olarak
     * mevcut ilk 'Okundu/İncelendi' durumuna veya enum değerine çeker.
     */
    protected function mutateFormDataBeforeFill(array $data): array
    {
        if (isset($data['status']) && ($data['status'] === 'new' || $data['status'] === QuoteRequestStatus::New->value)) {
            
            $cases = QuoteRequestStatus::cases();
            
            $nextStatus = $cases[1] ?? $cases[0];

            $data['status'] = $nextStatus->value;

            $this->getRecord()->update([
                'status' => $nextStatus,
            ]);
        }

        return $data;
    }
}