<?php

namespace App\Filament\Resources\PostResource\Pages;

use App\Filament\Resources\PostResource;
use App\Enums\PostStatus;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Carbon;

class EditPost extends EditRecord
{
    protected static string $resource = PostResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['user_id'] ??= auth()->id();

        $data = $this->mergePublishedDateTime($data);

        return $data;
    }

    private function mergePublishedDateTime(array $data): array
    {
        $date = $data['published_date'] ?? null;
        $time = $data['published_time'] ?? null;

        if (in_array($data['status'] ?? null, [PostStatus::Published->value, PostStatus::Scheduled->value], true)) {
            if ($date && $time) {
                $data['published_at'] = Carbon::parse($date . ' ' . $time);
            } elseif (($data['status'] ?? null) === PostStatus::Published->value) {
                $data['published_at'] = now();
            }
        }

        unset($data['published_date'], $data['published_time']);

        return $data;
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('publishNow')
                ->label('Publish Now')
                ->icon('heroicon-o-paper-airplane')
                ->color('primary')
                ->visible(fn () => $this->record->status !== PostStatus::Published)
                ->action(function (): void {
                    $now = now();
                    $this->record->update([
                        'status' => PostStatus::Published,
                        'published_at' => $now,
                    ]);
                    $this->fillForm();
                }),
            Actions\DeleteAction::make(),
        ];
    }
}
