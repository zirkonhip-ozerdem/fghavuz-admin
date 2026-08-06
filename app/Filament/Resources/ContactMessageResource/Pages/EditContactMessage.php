<?php

namespace App\Filament\Resources\ContactMessageResource\Pages;

use App\Filament\Resources\ContactMessageResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditContactMessage extends EditRecord
{
    protected static string $resource = ContactMessageResource::class;

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make()];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        if ($this->record->status?->value === \App\Enums\ContactMessageStatus::New->value) {
            $this->record->update(['status' => \App\Enums\ContactMessageStatus::Read]);
        }

        return $data;
    }
}
