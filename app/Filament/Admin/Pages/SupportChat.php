<?php

namespace App\Filament\Admin\Pages;

use App\Models\ChatMessage;
use App\Models\Organization;
use BackedEnum;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Url;
use Livewire\Attributes\On;

class SupportChat extends Page
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-chat-bubble-left-right';

    protected string $view = 'filament.admin.pages.support-chat';

    #[Url]
    public ?int $organization_id = null;

    public string $message = '';

    public function mount(): void
    {
        if (! $this->organization_id) {
            $this->organization_id = Organization::first()?->id;
        }
    }

    public function getOrganizations()
    {
        return Organization::all();
    }

    public function getMessages()
    {
        if (! $this->organization_id) {
            return collect();
        }

        return ChatMessage::where('organization_id', $this->organization_id)
            ->where('is_support', true)
            ->with('user')
            ->latest()
            ->take(50)
            ->get()
            ->reverse();
    }

    public function sendMessage(): void
    {
        if (! $this->organization_id) {
            return;
        }

        $this->validate([
            'message' => 'required|string|max:1000',
        ]);

        ChatMessage::create([
            'organization_id' => $this->organization_id,
            'user_id' => Auth::id(),
            'message' => $this->message,
            'is_support' => true,
        ]);

        $this->message = '';
        $this->dispatch('messageSent');
    }

    #[On('messageSent')]
    public function refreshMessages(): void
    {
    }

    public static function getNavigationLabel(): string
    {
        return __('common.support_chat');
    }

    public function getTitle(): string
    {
        return __('common.support_chat');
    }
}
