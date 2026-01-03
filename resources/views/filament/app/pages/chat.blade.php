@php
    $conversations = ($type === 'private') ? $this->getConversations() : collect();
    $available = ($type === 'private') ? $this->getAvailableUsers() : collect();
    $organizationUsers = ($type === 'general') ? $this->getAllOrganizationUsers() : collect();
    
    if ($type === 'private') {
        $messages = $this->getPrivateMessages();
    } elseif ($type === 'support') {
        $messages = $this->getSupportMessages();
    } else {
        $messages = $this->getGeneralMessages();
    }
    
    $recipient = ($type === 'private') ? $this->getRecipient() : null;
@endphp

<x-filament-panels::page class="!p-0">
    <div style="display: flex; height: calc(100vh - 7rem); background-color: #ffffff; gap: 0;" class="dark:bg-gray-950">
        
        {{-- SIDEBAR --}}
        <div style="width: 320px; display: flex; flex-direction: column; border-right: 1px solid #e5e7eb; background-color: #ffffff;" class="dark:bg-gray-900 dark:border-gray-800">
            
            {{-- Sidebar Header --}}
            <div style="padding: 1rem; border-bottom: 1px solid #e5e7eb; background-color: #ffffff;" class="dark:bg-gray-900 dark:border-gray-800">
                <div style="margin-bottom: 1rem;">
                    <h1 style="font-size: 1.25rem; font-weight: bold; color: #111827;">{{ __('common.messages') }}</h1>
                </div>
                
                {{-- Search --}}
                <div style="position: relative;">
                    <input
                        type="text"
                        wire:model.live="searchQuery"
                        placeholder="{{ __('common.search_users') }}"
                        style="width: 100%; padding: 0.5rem 0.75rem; border-radius: 9999px; background-color: #f3f4f6; border: none; font-size: 0.875rem; color: #111827; box-sizing: border-box;"
                        class="dark:bg-gray-800 dark:text-white dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500"
                    />
                </div>
            </div>

            {{-- Tabs --}}
            <div style="display: flex; padding: 0.5rem; gap: 0.5rem; border-bottom: 1px solid #e5e7eb; background-color: #ffffff;" class="dark:bg-gray-900 dark:border-gray-800">
                <button
                    wire:click="switchToPrivate"
                    style="flex: 1; padding: 0.5rem 0.75rem; font-size: 0.875rem; font-weight: 500; border-radius: 0.375rem; cursor: pointer; transition: all 0.3s; border: none; {{ $type === 'private' ? 'background-color: #e0f2fe; color: #0369a1;' : 'background-color: transparent; color: #4b5563;' }}"
                    class="dark:{{ $type === 'private' ? 'bg-blue-900/30 text-blue-300' : 'text-gray-400 hover:text-gray-200' }}"
                >
                    {{ __('common.direct') }}
                </button>
                <button
                    wire:click="switchToGeneral"
                    style="flex: 1; padding: 0.5rem 0.75rem; font-size: 0.875rem; font-weight: 500; border-radius: 0.375rem; cursor: pointer; transition: all 0.3s; border: none; {{ $type === 'general' ? 'background-color: #e0f2fe; color: #0369a1;' : 'background-color: transparent; color: #4b5563;' }}"
                    class="dark:{{ $type === 'general' ? 'bg-blue-900/30 text-blue-300' : 'text-gray-400 hover:text-gray-200' }}"
                >
                    {{ __('common.general') }}
                </button>
                <button
                    wire:click="switchToSupport"
                    style="flex: 1; padding: 0.5rem 0.75rem; font-size: 0.875rem; font-weight: 500; border-radius: 0.375rem; cursor: pointer; transition: all 0.3s; border: none; {{ $type === 'support' ? 'background-color: #e0f2fe; color: #0369a1;' : 'background-color: transparent; color: #4b5563;' }}"
                    class="dark:{{ $type === 'support' ? 'bg-blue-900/30 text-blue-300' : 'text-gray-400 hover:text-gray-200' }}"
                >
                    {{ __('common.support') }}
                </button>
            </div>

            {{-- Users/Conversations List --}}
            <div style="flex: 1; overflow-y: auto;">
                @if($type === 'private')
                    {{-- Private Conversations --}}
                    @if($conversations->count() > 0)
                        <div>
                            @foreach($conversations as $user)
                                @php
                                    $lastMessage = $user->sentMessages()
                                        ->where('organization_id', auth()->user()->organization_id)
                                        ->where(function ($q) {
                                            $q->where('sender_id', auth()->id())
                                                ->orWhere('recipient_id', auth()->id());
                                        })
                                        ->latest()
                                        ->first();
                                    $isSelected = $recipient_id === $user->id;
                                @endphp
                                <button
                                    wire:click="selectRecipient({{ $user->id }})"
                                    style="width: 100%; padding: 0.625rem 1rem; text-align: left; border: none; background-color: {{ $isSelected ? '#f3f4f6' : 'transparent' }}; cursor: pointer; transition: all 0.2s;"
                                    class="hover:bg-gray-50 dark:hover:bg-gray-800/50 {{ $isSelected ? 'dark:bg-gray-800/50' : '' }}"
                                >
                                    <div style="display: flex; gap: 0.75rem; align-items: center;">
                                        <div style="width: 40px; height: 40px; border-radius: 9999px; background: linear-gradient(135deg, #2563eb, #1d4ed8); display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; font-size: 0.875rem; flex-shrink: 0;">
                                            {{ strtoupper(substr($user->name, 0, 1)) }}
                                        </div>
                                        <div style="flex: 1; min-width: 0;">
                                            <div style="display: flex; align-items: center; justify-content: space-between; gap: 0.5rem; margin-bottom: 0.25rem;">
                                                <p style="font-size: 0.875rem; font-weight: 500; color: #111827; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" class="dark:text-white">{{ $user->name }}</p>
                                                @if($lastMessage)
                                                    <span style="font-size: 0.75rem; color: #6b7280; flex-shrink: 0;" class="dark:text-gray-400">{{ $lastMessage->created_at->format('H:i') }}</span>
                                                @endif
                                            </div>
                                            @if($lastMessage)
                                                <p style="font-size: 0.75rem; color: #6b7280; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" class="dark:text-gray-400">
                                                    @if($lastMessage->sender_id === auth()->id())
                                                        {{ __('common.you') }}: 
                                                    @endif
                                                    {{ Str::limit($lastMessage->message, 30) }}
                                                </p>
                                            @else
                                                <p style="font-size: 0.75rem; color: #9ca3af;" class="dark:text-gray-500">{{ __('common.no_messages_yet') }}</p>
                                            @endif
                                        </div>
                                    </div>
                                </button>
                            @endforeach
                        </div>
                    @endif

                    {{-- Search Results --}}
                    @if($available->count() > 0 && $searchQuery)
                        <div style="border-top: 1px solid #e5e7eb;" class="dark:border-gray-800">
                            @foreach($available as $user)
                                @if(!$conversations->contains('id', $user->id))
                                    <button
                                        wire:click="selectRecipient({{ $user->id }})"
                                        style="width: 100%; padding: 0.625rem 1rem; text-align: left; border: none; background-color: transparent; cursor: pointer; transition: all 0.2s; border-bottom: 1px solid #e5e7eb;"
                                        class="hover:bg-gray-50 dark:hover:bg-gray-800/50 dark:border-gray-800"
                                    >
                                        <div style="display: flex; gap: 0.75rem; align-items: center;">
                                            <div style="width: 40px; height: 40px; border-radius: 9999px; background-color: #d1d5db; display: flex; align-items: center; justify-content: center; color: #374151; font-weight: bold; font-size: 0.875rem; flex-shrink: 0;" class="dark:bg-gray-700 dark:text-gray-300">
                                                {{ strtoupper(substr($user->name, 0, 1)) }}
                                            </div>
                                            <div style="flex: 1; min-width: 0;">
                                                <p style="font-size: 0.875rem; font-weight: 500; color: #111827; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" class="dark:text-white">{{ $user->name }}</p>
                                                <p style="font-size: 0.75rem; color: #6b7280; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" class="dark:text-gray-400">{{ $user->email }}</p>
                                            </div>
                                        </div>
                                    </button>
                                @endif
                            @endforeach
                        </div>
                    @endif

                    {{-- Empty State --}}
                    @if($conversations->count() === 0 && !$searchQuery)
                        <div style="display: flex; flex-direction: column; align-items: center; justify-content: center; height: 100%; gap: 0.75rem; text-align: center; padding: 1.5rem;">
                            <div style="color: #9ca3af; width: 48px; height: 48px; margin: 0 auto;">
                                <svg style="width: 48px; height: 48px; display: block; margin: 0 auto;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                </svg>
                            </div>
                            <p style="font-size: 0.875rem; color: #6b7280;" class="dark:text-gray-400">{{ __('common.no_conversations_yet') }}</p>
                        </div>
                    @endif

                @else
                    {{-- General Chat - Show all organization users --}}
                    @if($organizationUsers->count() > 0)
                        <div style="padding: 0.5rem;">
                            <p style="padding: 0.5rem 0.75rem; font-size: 0.75rem; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em;" class="dark:text-gray-400">
                                {{ __('common.organization_members') }} ({{ $organizationUsers->count() }})
                            </p>
                            <div>
                                @foreach($organizationUsers as $user)
                                    <button
                                        wire:click="selectRecipient({{ $user->id }})"
                                        style="width: 100%; padding: 0.625rem 0.75rem; text-align: left; border: none; background-color: transparent; cursor: pointer; transition: all 0.2s; border-radius: 0.375rem; margin-bottom: 0.25rem;"
                                        class="hover:bg-gray-100 dark:hover:bg-gray-800/50"
                                    >
                                        <div style="display: flex; gap: 0.75rem; align-items: center;">
                                            <div style="width: 36px; height: 36px; border-radius: 9999px; background: linear-gradient(135deg, #2563eb, #1d4ed8); display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; font-size: 0.875rem; flex-shrink: 0;">
                                                {{ strtoupper(substr($user->name, 0, 1)) }}
                                            </div>
                                            <div style="flex: 1; min-width: 0;">
                                                <p style="font-size: 0.875rem; font-weight: 500; color: #111827; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" class="dark:text-white">{{ $user->name }}</p>
                                                <p style="font-size: 0.75rem; color: #6b7280; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" class="dark:text-gray-400">{{ $user->email }}</p>
                                            </div>
                                        </div>
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    @else
                        <div style="display: flex; flex-direction: column; align-items: center; justify-content: center; height: 100%; gap: 0.75rem; text-align: center; padding: 1.5rem;">
                            <div style="color: #9ca3af; width: 48px; height: 48px; margin: 0 auto;">
                                <svg style="width: 48px; height: 48px; display: block; margin: 0 auto;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.856-1.487M15 10a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                            </div>
                            <p style="font-size: 0.875rem; color: #6b7280;" class="dark:text-gray-400">{{ __('common.no_users_found') }}</p>
                        </div>
                    @endif
                @endif
            </div>
        </div>

        {{-- MAIN CHAT AREA --}}
        <div style="flex: 1; display: flex; flex-direction: column; min-width: 0;">
            
            @if(($type === 'private' && $recipient_id) || $type === 'general' || $type === 'support')
                {{-- Chat Header --}}
                <div style="padding: 0.75rem 1.5rem; border-bottom: 1px solid #e5e7eb; background-color: #ffffff; display: flex; align-items: center; justify-content: space-between;" class="dark:bg-gray-900 dark:border-gray-800">
                    <div style="display: flex; gap: 0.75rem; align-items: center;">
                        @if($type === 'private' && $recipient)
                            <div style="width: 40px; height: 40px; border-radius: 9999px; background: linear-gradient(135deg, #2563eb, #1d4ed8); display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; font-size: 0.875rem;">
                                {{ strtoupper(substr($recipient->name, 0, 1)) }}
                            </div>
                            <div>
                                <h2 style="font-size: 0.875rem; font-weight: 600; color: #111827;" class="dark:text-white">{{ $recipient->name }}</h2>
                                <p style="font-size: 0.75rem; color: #6b7280;" class="dark:text-gray-400">Active now</p>
                            </div>
                        @elseif($type === 'support')
                            <div>
                                <h2 style="font-size: 0.875rem; font-weight: 600; color: #111827;" class="dark:text-white">{{ __('common.support_chat') }}</h2>
                                <p style="font-size: 0.75rem; color: #6b7280;" class="dark:text-gray-400">{{ __('common.system_administrators') }}</p>
                            </div>
                        @else
                            <div>
                                <h2 style="font-size: 0.875rem; font-weight: 600; color: #111827;" class="dark:text-white">{{ __('common.general_chat') }}</h2>
                                <p style="font-size: 0.75rem; color: #6b7280;" class="dark:text-gray-400">{{ __('common.organization_members') }}</p>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Messages Area --}}
                <div id="chat-window" style="flex: 1; overflow-y: auto; background-color: #ffffff; padding: 1rem; display: flex; flex-direction: column;" class="dark:bg-gray-950" wire:poll.5s="$refresh">
                    @if($messages->count() > 0)
                        <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                            @php $lastDate = null; @endphp
                            @foreach($messages as $msg)
                                @php
                                    if ($type === 'private') {
                                        $currentDate = $msg->created_at->translatedFormat('j F Y');
                                        $isOwn = $msg->sender_id === auth()->id();
                                        $msgUser = $isOwn ? $msg->sender : $msg->recipient;
                                    } else {
                                        $currentDate = $msg->created_at->translatedFormat('j F Y');
                                        $isOwn = $msg->user_id === auth()->id();
                                        $msgUser = $msg->user;
                                    }
                                @endphp

                                {{-- Date Separator --}}
                                @if($lastDate !== $currentDate)
                                    <div style="display: flex; justify-content: center; margin: 0.5rem 0;">
                                        <span style="font-size: 0.75rem; color: #6b7280; background-color: #f3f4f6; padding: 0.25rem 0.75rem; border-radius: 9999px;" class="dark:bg-gray-800 dark:text-gray-400">
                                            {{ $currentDate }}
                                        </span>
                                    </div>
                                    @php $lastDate = $currentDate; @endphp
                                @endif

                                {{-- Message --}}
                                <div style="display: flex; {{ $isOwn ? 'justify-content: flex-end;' : 'justify-content: flex-start;' }}">
                                    <div style="display: flex; align-items: flex-end; gap: 0.5rem; {{ $isOwn ? 'flex-direction: row-reverse;' : 'flex-direction: row;' }} max-width: 28rem;">
                                        @if(!$isOwn)
                                            <div style="width: 32px; height: 32px; border-radius: 9999px; background-color: #e5e7eb; display: flex; align-items: center; justify-content: center; color: #374151; font-weight: bold; font-size: 0.75rem; flex-shrink: 0;" class="dark:bg-gray-700 dark:text-gray-300">
                                                {{ strtoupper(substr($msgUser?->name ?? '', 0, 1)) }}
                                            </div>
                                        @endif
                                        
                                        <div style="display: flex; flex-direction: column; {{ $isOwn ? 'align-items: flex-end;' : 'align-items: flex-start;' }}">
                                            @if(!$isOwn && $type === 'general')
                                                <span style="font-size: 0.75rem; font-weight: 500; color: #4b5563; padding: 0 0.75rem; margin-bottom: 0.125rem;" class="dark:text-gray-400">{{ $msgUser?->name }}</span>
                                            @endif
                                            
                                            <div style="padding: 0.5rem 1rem; border-radius: 0.5rem; {{ $isOwn ? 'background-color: #2563eb; color: white;' : 'background-color: #f3f4f6; color: #111827;' }} word-wrap: break-word; max-width: 20rem;" class="dark:{{ $isOwn ? 'bg-blue-600' : 'bg-gray-800 text-gray-100' }}">
                                                <p style="font-size: 0.875rem;">{{ $msg->message }}</p>
                                            </div>
                                            
                                            <span style="font-size: 0.75rem; {{ $isOwn ? 'color: #6b7280;' : 'color: #6b7280;' }} margin-top: 0.125rem; padding: 0 0.75rem;" class="dark:text-gray-400">{{ $msg->created_at->format('H:i') }}</span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div style="display: flex; flex-direction: column; align-items: center; justify-content: center; height: 100%; gap: 0.75rem; text-align: center;">
                            <div style="color: #d1d5db; width: 64px; height: 64px; margin: 0 auto;" class="dark:text-gray-700">
                                <svg style="width: 64px; height: 64px; display: block; margin: 0 auto;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                </svg>
                            </div>
                            <p style="font-size: 0.875rem; color: #6b7280;" class="dark:text-gray-400">
                                @if($type === 'private')
                                    {{ __('common.no_messages_in_conversation') }}
                                @else
                                    {{ __('common.no_messages_yet') }}
                                @endif
                            </p>
                        </div>
                    @endif
                </div>

                {{-- Input Area --}}
                <div style="padding: 0.75rem 1rem; border-top: 1px solid #e5e7eb; background-color: #ffffff;" class="dark:bg-gray-900 dark:border-gray-800">
                    <form wire:submit.prevent="sendMessage" style="display: flex; align-items: center; gap: 0.5rem;">
                        <input
                            type="text"
                            wire:model="message"
                            placeholder="{{ __('common.type_your_message') }}"
                            style="flex: 1; padding: 0.5rem 1rem; border-radius: 9999px; background-color: #f3f4f6; border: none; font-size: 0.875rem; color: #111827; box-sizing: border-box;"
                            class="dark:bg-gray-800 dark:text-white dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500"
                            autocomplete="off"
                        />
                        <button
                            type="submit"
                            style="padding: 0.5rem; border-radius: 9999px; background-color: #2563eb; color: white; cursor: pointer; border: none; transition: all 0.2s; height: 36px; width: 36px; display: flex; align-items: center; justify-content: center;"
                            class="hover:bg-blue-700 dark:hover:bg-blue-700 disabled:opacity-50"
                            wire:loading.attr="disabled"
                        >
                            <svg style="width: 20px; height: 20px;" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M10.894 2.553a1 1 0 00-1.788 0l-7 14a1 1 0 001.169 1.409l5.951-1.429 5.951 1.429a1 1 0 001.169-1.409l-7-14z"></path>
                            </svg>
                        </button>
                    </form>
                </div>

            @else
                {{-- No Recipient Selected --}}
                <div style="flex: 1; display: flex; flex-direction: column; align-items: center; justify-content: center; background-color: #f9fafb;" class="dark:bg-gray-950">
                    <div style="text-align: center;">
                        <div style="color: #d1d5db; margin-bottom: 1rem; width: 64px; height: 64px; margin-left: auto; margin-right: auto;" class="dark:text-gray-700">
                            <svg style="width: 64px; height: 64px; display: block; margin: 0 auto;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                            </svg>
                        </div>
                        <h3 style="font-size: 1.125rem; font-weight: 600; color: #111827; margin-bottom: 0.5rem;" class="dark:text-white">{{ __('common.select_recipient') }}</h3>
                        <p style="font-size: 0.875rem; color: #6b7280;" class="dark:text-gray-400">{{ __('common.select_user_from_list') }}</p>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <style>
        #chat-window::-webkit-scrollbar {
            width: 8px;
        }
        #chat-window::-webkit-scrollbar-track {
            background: transparent;
        }
        #chat-window::-webkit-scrollbar-thumb {
            background: #d1d5db;
            border-radius: 4px;
        }
        #chat-window::-webkit-scrollbar-thumb:hover {
            background: #9ca3af;
        }
        .dark #chat-window::-webkit-scrollbar-thumb {
            background: #4b5563;
        }
        .dark #chat-window::-webkit-scrollbar-thumb:hover {
            background: #6b7280;
        }
    </style>

    <script>
        document.addEventListener('livewire:initialized', () => {
            const chatWindow = document.getElementById('chat-window');
            
            const scrollToBottom = () => {
                if (chatWindow) {
                    setTimeout(() => {
                        chatWindow.scrollTop = chatWindow.scrollHeight;
                    }, 50);
                }
            };

            scrollToBottom();

            Livewire.on('messageSent', () => {
                scrollToBottom();
            });
            
            const observer = new MutationObserver(() => {
                if (!chatWindow) return;
                const threshold = 50;
                if (chatWindow.scrollHeight - chatWindow.scrollTop - chatWindow.clientHeight < threshold) {
                    scrollToBottom();
                }
            });
            
            if (chatWindow) {
                observer.observe(chatWindow, { childList: true, subtree: true });
            }
        });
    </script>
</x-filament-panels::page>
