<?php

namespace App\Filament\Admin\Pages;

use App\Models\ChatMessage;
use App\Models\Organization;
use App\Models\PrivateChat;
use App\Models\User;
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

    #[Url]
    public ?int $recipient_id = null;

    #[Url]
    public string $activeTab = 'organizations'; // 'organizations' or 'users'

    public string $message = '';

    public string $searchQuery = '';

    public function mount(): void
    {
        if (!$this->organization_id && !$this->recipient_id) {
            // Select the organization with the most recent support message
            $this->organization_id = Organization::query()
                ->withMax(['chatMessages' => function ($query) {
                    $query->where('is_support', true);
                }], 'created_at')
                ->orderByDesc('chat_messages_max_created_at')
                ->first()?->id;
        }

        if ($this->recipient_id) {
            $this->activeTab = 'users';
        }
    }

    public function getOrganizations()
    {
        return Organization::query()
            ->when($this->searchQuery, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%' . $this->searchQuery . '%')
                        ->orWhere('slug', 'like', '%' . $this->searchQuery . '%');
                });
            })
            ->withMax(['chatMessages as last_support_message_at' => function ($query) {
                $query->where('is_support', true);
            }], 'created_at')
            ->get()
            ->sortByDesc('last_support_message_at');
    }

    public function getUsers()
    {
        return User::query()
            ->where('id', '!=', Auth::id())
            ->when($this->searchQuery, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%' . $this->searchQuery . '%')
                        ->orWhere('email', 'like', '%' . $this->searchQuery . '%');
                });
            })
            ->withMax(['receivedMessages as last_received_at' => function ($query) {
                $query->where('sender_id', Auth::id());
            }], 'created_at')
            ->withMax(['sentMessages as last_sent_at' => function ($query) {
                $query->where('recipient_id', Auth::id());
            }], 'created_at')
            ->get()
            ->sortByDesc(fn($user) => max($user->last_received_at, $user->last_sent_at));
    }

    public function getMessages()
    {
        if ($this->activeTab === 'organizations') {
            if (! $this->organization_id) {
                return collect();
            }

            return ChatMessage::where('organization_id', $this->organization_id)
                ->where('is_support', true)
                ->with('user')
                ->latest()
                ->take(100)
                ->get()
                ->reverse();
        } else {
            if (!$this->recipient_id) {
                return collect();
            }

            return PrivateChat::query()
                ->where(function ($query) {
                    $query->where('sender_id', Auth::id())
                        ->where('recipient_id', $this->recipient_id)
                        ->orWhere(function ($q) {
                            $q->where('sender_id', $this->recipient_id)
                                ->where('recipient_id', Auth::id());
                        });
                })
                ->with(['sender', 'recipient'])
                ->latest()
                ->take(100)
                ->get()
                ->reverse();
        }
    }

    public function sendMessage(): void
    {
        if ($this->activeTab === 'organizations') {
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
        } else {
            if (!$this->recipient_id) {
                return;
            }

            $this->validate([
                'message' => 'required|string|max:1000',
            ]);

            $recipient = User::find($this->recipient_id);

            PrivateChat::create([
                'organization_id' => $recipient->organization_id ?? Auth::user()->organization_id ?? Organization::first()?->id,
                'sender_id' => Auth::id(),
                'recipient_id' => $this->recipient_id,
                'message' => $this->message,
            ]);
        }

        $this->message = '';
        $this->dispatch('messageSent');
    }

    public function selectOrganization($id)
    {
        $this->organization_id = $id;
        $this->recipient_id = null;
        $this->activeTab = 'organizations';
        $this->message = '';
    }

    public function selectUser($id)
    {
        $this->recipient_id = $id;
        $this->organization_id = null;
        $this->activeTab = 'users';
        $this->message = '';
    }

    #[On('messageSent')]
    public function refreshMessages(): void
    {
        // Re-renders the component
    }

    public static function canAccess(): bool
    {
        return Auth::user()?->isGeneralManager() ?? false;
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
