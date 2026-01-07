<?php

namespace App\Filament\Resources\PostResource\Pages;

use App\Filament\Resources\PostResource;
use App\Enums\PostStatus;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Carbon;

class CreatePost extends CreateRecord
{
    protected static string $resource = PostResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('publishNow')
                ->label('Publish Now')
                ->icon('heroicon-o-paper-airplane')
                ->color('primary')
                ->action(function (): void {
                    $state = $this->form->getState();
                    $now = now();
                    $state['status'] = PostStatus::Published->value;
                    $state['published_date'] = $now->toDateString();
                    $state['published_time'] = $now->format('H:i');
                    $this->form->fill($state);
                    $this->create();
                }),
        ];
    }

    protected function mutateFormDataBeforeCreate(array $data): array
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
}
