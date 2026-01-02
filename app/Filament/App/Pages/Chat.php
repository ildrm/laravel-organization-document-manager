<?php

namespace App\Filament\App\Pages;

use App\Models\ChatMessage;
use App\Models\PrivateChat;
use App\Models\User;
use BackedEnum;
use Filament\Pages\Page;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;

class Chat extends Page
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-chat-bubble-left-right';

    protected string $view = 'filament.app.pages.chat';

    #[Url]
    public string $type = 'private'; // 'private' or 'general'

    #[Url]
    public ?int $recipient_id = null;

    public string $message = '';

    public string $searchQuery = '';

    public function mount(): void
    {
        // Initialize search
        $this->searchQuery = '';
    }

    /**
     * Get all users in the organization except the current user
     */
    public function getAvailableUsers(): Collection
    {
        return User::where('organization_id', Auth::user()->organization_id)
            ->where('id', '!=', Auth::id())
            ->when($this->searchQuery, function ($query) {
                $query->where('name', 'like', '%' . $this->searchQuery . '%')
                    ->orWhere('email', 'like', '%' . $this->searchQuery . '%');
            })
            ->orderBy('name')
            ->get();
    }

    /**
     * Get all users in the organization for general chat
     */
    public function getAllOrganizationUsers(): Collection
    {
        return User::where('organization_id', Auth::user()->organization_id)
            ->where('id', '!=', Auth::id())
            ->when($this->searchQuery, function ($query) {
                $query->where('name', 'like', '%' . $this->searchQuery . '%')
                    ->orWhere('email', 'like', '%' . $this->searchQuery . '%');
            })
            ->orderBy('name')
            ->get();
    }

    /**
     * Get conversations list for sidebar
     */
    public function getConversations(): Collection
    {
        return User::where('organization_id', Auth::user()->organization_id)
            ->where('id', '!=', Auth::id())
            ->whereHas('sentMessages', function ($query) {
                $query->where('sender_id', Auth::id())
                    ->orWhere('recipient_id', Auth::id());
            }, '>')
            ->with([
                'sentMessages' => function ($query) {
                    $query->where('organization_id', Auth::user()->organization_id)
                        ->where(function ($q) {
                            $q->where('sender_id', Auth::id())
                                ->orWhere('recipient_id', Auth::id());
                        })
                        ->latest()
                        ->limit(1);
                }
            ])
            ->get()
            ->sortByDesc(function ($user) {
                return $user->sentMessages->first()?->created_at;
            });
    }

    /**
     * Get private chat messages with a specific user
     */
    public function getPrivateMessages(): Collection
    {
        if (!$this->recipient_id) {
            return collect();
        }

        return PrivateChat::where('organization_id', Auth::user()->organization_id)
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

    /**
     * Get general organization messages
     */
    public function getGeneralMessages(): Collection
    {
        return ChatMessage::where('organization_id', Auth::user()->organization_id)
            ->where('is_support', false)
            ->with('user')
            ->latest()
            ->take(50)
            ->get()
            ->reverse();
    }

    /**
     * Get the recipient user object
     */
    public function getRecipient(): ?User
    {
        if (!$this->recipient_id) {
            return null;
        }

        return User::find($this->recipient_id);
    }

    /**
     * Send a private message
     */
    public function sendPrivateMessage(): void
    {
        if (!Auth::user()->hasPermission('chat.send')) {
            $this->addError('message', __('common.no_permission'));
            return;
        }

        if (!$this->recipient_id) {
            $this->addError('message', __('common.select_recipient'));
            return;
        }

        $this->validate([
            'message' => 'required|string|max:1000',
        ]);

        PrivateChat::create([
            'organization_id' => Auth::user()->organization_id,
            'sender_id' => Auth::id(),
            'recipient_id' => $this->recipient_id,
            'message' => $this->message,
        ]);

        $this->message = '';
        $this->dispatch('messageSent');
    }

    /**
     * Send a general message
     */
    public function sendGeneralMessage(): void
    {
        if (!Auth::user()->hasPermission('chat.send')) {
            $this->addError('message', __('common.no_permission'));
            return;
        }

        $this->validate([
            'message' => 'required|string|max:1000',
        ]);

        ChatMessage::create([
            'organization_id' => Auth::user()->organization_id,
            'user_id' => Auth::id(),
            'message' => $this->message,
            'is_support' => false,
        ]);

        $this->message = '';
        $this->dispatch('messageSent');
    }

    /**
     * Send message based on chat type
     */
    public function sendMessage(): void
    {
        if ($this->type === 'private') {
            $this->sendPrivateMessage();
        } else {
            $this->sendGeneralMessage();
        }
    }

    public function selectRecipient(int $userId): void
    {
        $this->recipient_id = $userId;
        $this->searchQuery = '';
        $this->message = '';
    }

    public function switchToGeneral(): void
    {
        $this->type = 'general';
        $this->recipient_id = null;
        $this->searchQuery = '';
        $this->message = '';
    }

    public function switchToPrivate(): void
    {
        $this->type = 'private';
        $this->recipient_id = null;
        $this->searchQuery = '';
        $this->message = '';
    }

    public static function getNavigationItems(): array
    {
        return [
            \Filament\Navigation\NavigationItem::make(__('common.messages'))
                ->icon(static::$navigationIcon)
                ->url(static::getUrl(['type' => 'private']))
                ->isActiveWhen(fn () => request()->routeIs(static::getRouteName()))
                ->sort(10),
        ];
    }

    #[On('messageSent')]
    public function refreshMessages(): void
    {
        // This will trigger a re-render
    }

    public static function canAccess(): bool
    {
        $user = Auth::user();

        // Allow access for General Admin, Organization Admin, or users with chat.view permission
        if ($user->isGeneralManager() || $user->isOrgAdmin()) {
            return true;
        }

        return $user->hasPermission('chat.view');
    }
}
