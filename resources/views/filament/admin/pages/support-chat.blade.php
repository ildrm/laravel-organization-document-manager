<x-filament-panels::page class="!p-0">
    <div style="display: flex; height: calc(100vh - 7rem); background-color: #ffffff; gap: 0;" class="dark:bg-gray-950">
        
        {{-- SIDEBAR --}}
        <div style="width: 320px; display: flex; flex-direction: column; border-right: 1px solid #e5e7eb; background-color: #ffffff;" class="dark:bg-gray-900 dark:border-gray-800">
            
            {{-- Sidebar Header --}}
            <div style="padding: 1rem; border-bottom: 1px solid #e5e7eb; background-color: #ffffff;" class="dark:bg-gray-900 dark:border-gray-800">
                <div style="margin-bottom: 1rem;">
                    <h1 style="font-size: 1.25rem; font-weight: bold; color: #111827;" class="dark:text-white">{{ __('common.support_chat') }}</h1>
                </div>
                
                {{-- Tabs --}}
                <div style="display: flex; background-color: #f3f4f6; border-radius: 0.5rem; padding: 0.25rem; margin-bottom: 1rem;" class="dark:bg-gray-800">
                    <button 
                        wire:click="$set('activeTab', 'organizations')"
                        style="flex: 1; padding: 0.375rem 0; border-radius: 0.375rem; font-size: 0.75rem; font-weight: 500; border: none; cursor: pointer; transition: all 0.2s; background-color: {{ $activeTab === 'organizations' ? '#ffffff' : 'transparent' }}; color: {{ $activeTab === 'organizations' ? '#111827' : '#6b7280' }}; box-shadow: {{ $activeTab === 'organizations' ? '0 1px 2px 0 rgba(0, 0, 0, 0.05)' : 'none' }};"
                        class="{{ $activeTab === 'organizations' ? 'dark:bg-gray-700 dark:text-white' : 'dark:text-gray-400' }}"
                    >
                        {{ __('common.organizations') }}
                    </button>
                    <button 
                        wire:click="$set('activeTab', 'users')"
                        style="flex: 1; padding: 0.375rem 0; border-radius: 0.375rem; font-size: 0.75rem; font-weight: 500; border: none; cursor: pointer; transition: all 0.2s; background-color: {{ $activeTab === 'users' ? '#ffffff' : 'transparent' }}; color: {{ $activeTab === 'users' ? '#111827' : '#6b7280' }}; box-shadow: {{ $activeTab === 'users' ? '0 1px 2px 0 rgba(0, 0, 0, 0.05)' : 'none' }};"
                        class="{{ $activeTab === 'users' ? 'dark:bg-gray-700 dark:text-white' : 'dark:text-gray-400' }}"
                    >
                        {{ __('common.users') }}
                    </button>
                </div>

                {{-- Search --}}
                <div style="position: relative;">
                    <input
                        type="text"
                        wire:model.live="searchQuery"
                        placeholder="{{ $activeTab === 'organizations' ? __('common.search_organizations') : __('common.search_users') }}"
                        style="width: 100%; padding: 0.5rem 0.75rem; border-radius: 9999px; background-color: #f3f4f6; border: none; font-size: 0.875rem; color: #111827; box-sizing: border-box;"
                        class="dark:bg-gray-800 dark:text-white dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500"
                    />
                </div>
            </div>

            {{-- Sidebar List --}}
            <div style="flex: 1; overflow-y: auto;">
                @if($activeTab === 'organizations')
                    @php $orgs = $this->getOrganizations(); @endphp
                    @if($orgs->count() > 0)
                        @foreach($orgs as $org)
                            @php
                                $lastMessage = $org->chatMessages()->where('is_support', true)->latest()->first();
                                $isSelected = $organization_id === $org->id;
                            @endphp
                            <button
                                wire:click="selectOrganization({{ $org->id }})"
                                style="width: 100%; padding: 0.75rem 1rem; text-align: left; border: none; background-color: {{ $isSelected ? '#f3f4f6' : 'transparent' }}; cursor: pointer; transition: all 0.2s;"
                                class="hover:bg-gray-50 dark:hover:bg-gray-800/50 {{ $isSelected ? 'dark:bg-gray-800/50' : '' }}"
                            >
                                <div style="display: flex; gap: 0.75rem; align-items: center;">
                                    <div style="width: 40px; height: 40px; border-radius: 9999px; background: linear-gradient(135deg, #ef4444, #b91c1c); display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; font-size: 0.875rem; flex-shrink: 0;">
                                        {{ strtoupper(substr($org->name, 0, 1)) }}
                                    </div>
                                    <div style="flex: 1; min-width: 0;">
                                        <div style="display: flex; align-items: center; justify-content: space-between; gap: 0.5rem; margin-bottom: 0.25rem;">
                                            <p style="font-size: 0.875rem; font-weight: 500; color: #111827; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" class="dark:text-white">{{ $org->name }}</p>
                                            @if($lastMessage)
                                                <span style="font-size: 0.75rem; color: #6b7280; flex-shrink: 0;" class="dark:text-gray-400">{{ $lastMessage->created_at->format('H:i') }}</span>
                                            @endif
                                        </div>
                                        @if($lastMessage)
                                            <p style="font-size: 0.75rem; color: #6b7280; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" class="dark:text-gray-400">
                                                @if($lastMessage->user_id === auth()->id())
                                                    {{ __('common.you') }}: 
                                                @else
                                                    {{ $lastMessage->user->name }}:
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
                    @else
                        <div style="display: flex; flex-direction: column; align-items: center; justify-content: center; height: 100%; gap: 0.75rem; text-align: center; padding: 1.5rem;">
                            <p style="font-size: 0.875rem; color: #6b7280;" class="dark:text-gray-400">{{ __('common.no_organizations_found') }}</p>
                        </div>
                    @endif
                @else
                    @php $users = $this->getUsers(); @endphp
                    @if($users->count() > 0)
                        @foreach($users as $user)
                            @php
                                $lastMessage = \App\Models\PrivateChat::query()
                                    ->where(function ($q) use ($user) {
                                        $q->where('sender_id', auth()->id())->where('recipient_id', $user->id)
                                          ->orWhere('sender_id', $user->id)->where('recipient_id', auth()->id());
                                    })
                                    ->latest()
                                    ->first();
                                $isSelected = $recipient_id === $user->id;
                            @endphp
                            <button
                                wire:click="selectUser({{ $user->id }})"
                                style="width: 100%; padding: 0.75rem 1rem; text-align: left; border: none; background-color: {{ $isSelected ? '#f3f4f6' : 'transparent' }}; cursor: pointer; transition: all 0.2s;"
                                class="hover:bg-gray-50 dark:hover:bg-gray-800/50 {{ $isSelected ? 'dark:bg-gray-800/50' : '' }}"
                            >
                                <div style="display: flex; gap: 0.75rem; align-items: center;">
                                    <div style="width: 40px; height: 40px; border-radius: 9999px; background: linear-gradient(135deg, #3b82f6, #1d4ed8); display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; font-size: 0.875rem; flex-shrink: 0;">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </div>
                                    <div style="flex: 1; min-width: 0;">
                                        <div style="display: flex; align-items: center; justify-content: space-between; gap: 0.5rem; margin-bottom: 0.25rem;">
                                            <p style="font-size: 0.875rem; font-weight: 500; color: #111827; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" class="dark:text-white">
                                                {{ $user->name }}
                                                @if($user->organization)
                                                    <span style="font-size: 0.7rem; font-weight: normal; color: #6b7280;">({{ $user->organization->name }})</span>
                                                @endif
                                            </p>
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
                    @else
                        <div style="display: flex; flex-direction: column; align-items: center; justify-content: center; height: 100%; gap: 0.75rem; text-align: center; padding: 1.5rem;">
                            <p style="font-size: 0.875rem; color: #6b7280;" class="dark:text-gray-400">{{ __('common.no_users_found') }}</p>
                        </div>
                    @endif
                @endif
            </div>
        </div>

        {{-- MAIN CHAT AREA --}}
        <div style="flex: 1; display: flex; flex-direction: column; min-width: 0;">
            @if($organization_id || $recipient_id)
                @php 
                    $targetName = '';
                    $targetInitial = '';
                    $targetSubtext = '';
                    $headerGradient = 'linear-gradient(135deg, #ef4444, #b91c1c)';

                    if ($activeTab === 'organizations' && $organization_id) {
                        $currentOrg = \App\Models\Organization::find($organization_id);
                        $targetName = $currentOrg->name;
                        $targetInitial = strtoupper(substr($targetName, 0, 1));
                        $targetSubtext = __('common.support_chat_with_org');
                    } elseif ($activeTab === 'users' && $recipient_id) {
                        $currentUser = \App\Models\User::find($recipient_id);
                        $targetName = $currentUser->name;
                        $targetInitial = strtoupper(substr($targetName, 0, 1));
                        $targetSubtext = $currentUser->organization ? $currentUser->organization->name : __('common.user');
                        $headerGradient = 'linear-gradient(135deg, #3b82f6, #1d4ed8)';
                    }

                    $messages = $this->getMessages();
                    $lastDate = null;
                @endphp
                {{-- Chat Header --}}
                <div style="padding: 0.75rem 1.5rem; border-bottom: 1px solid #e5e7eb; background-color: #ffffff; display: flex; align-items: center; justify-content: space-between;" class="dark:bg-gray-900 dark:border-gray-800">
                    <div style="display: flex; gap: 0.75rem; align-items: center;">
                        <div style="width: 40px; height: 40px; border-radius: 9999px; background: {{ $headerGradient }}; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; font-size: 0.875rem;">
                            {{ $targetInitial }}
                        </div>
                        <div>
                            <h2 style="font-size: 0.875rem; font-weight: 600; color: #111827;" class="dark:text-white">{{ $targetName }}</h2>
                            <p style="font-size: 0.75rem; color: #6b7280;" class="dark:text-gray-400">{{ $targetSubtext }}</p>
                        </div>
                    </div>
                </div>

                {{-- Messages Area --}}
                <div id="support-chat-window" style="flex: 1; overflow-y: auto; padding: 1.5rem; background-color: #f9fafb; display: flex; flex-direction: column; gap: 1rem;" class="dark:bg-gray-950" wire:poll.5s>
                    @forelse($messages as $msg)
                        @php
                            $currentDate = $msg->created_at->translatedFormat('j F Y');
                            if ($activeTab === 'organizations') {
                                $isOwn = $msg->user_id === auth()->id();
                                $senderName = $msg->user->name;
                            } else {
                                $isOwn = $msg->sender_id === auth()->id();
                                $senderName = $msg->sender->name;
                            }
                        @endphp

                        @if($lastDate !== $currentDate)
                            <div style="display: flex; justify-content: center; margin: 1rem 0;">
                                <span style="padding: 0.25rem 0.75rem; background-color: #e5e7eb; border-radius: 9999px; font-size: 0.75rem; color: #4b5563; font-weight: 500;" class="dark:bg-gray-800 dark:text-gray-400">
                                    {{ $currentDate }}
                                </span>
                            </div>
                            @php $lastDate = $currentDate; @endphp
                        @endif

                        <div style="display: flex; {{ $isOwn ? 'justify-content: flex-end;' : 'justify-content: flex-start;' }}">
                            <div style="display: flex; flex-direction: column; max-width: 70%; {{ $isOwn ? 'align-items: flex-end;' : 'align-items: flex-start;' }}">
                                <div style="display: flex; gap: 0.5rem; align-items: center; margin-bottom: 0.25rem; {{ $isOwn ? 'flex-direction: row-reverse;' : '' }}">
                                    <span style="font-size: 0.75rem; font-weight: 600; color: #4b5563;" class="dark:text-gray-300">{{ $senderName }}</span>
                                    <span style="font-size: 0.625rem; color: #9ca3af;">{{ $msg->created_at->format('H:i') }}</span>
                                </div>
                                <div style="padding: 0.625rem 1rem; border-radius: 1rem; font-size: 0.875rem; line-height: 1.25rem; position: relative; {{ $isOwn ? 'background-color: #2563eb; color: #ffffff; border-bottom-right-radius: 0.25rem;' : 'background-color: #ffffff; color: #1f2937; border-bottom-left-radius: 0.25rem; border: 1px solid #e5e7eb;' }}" class="dark:{{ $isOwn ? 'bg-blue-600' : 'bg-gray-800 text-gray-200 border-gray-700' }}">
                                    {{ $msg->message }}
                                </div>
                            </div>
                        </div>
                    @empty
                        <div style="display: flex; flex-direction: column; align-items: center; justify-content: center; height: 100%; opacity: 0.5;">
                            <p style="font-size: 0.875rem; color: #6b7280;">{{ __('common.no_messages_yet') }}</p>
                        </div>
                    @endforelse
                </div>

                {{-- Input Area --}}
                <div style="padding: 1rem 1.5rem; background-color: #ffffff; border-top: 1px solid #e5e7eb;" class="dark:bg-gray-900 dark:border-gray-800">
                    <form wire:submit.prevent="sendMessage" style="display: flex; gap: 0.75rem; align-items: center;">
                        <input
                            type="text"
                            wire:model="message"
                            placeholder="{{ __('common.type_your_message') }}"
                            style="flex: 1; padding: 0.625rem 1rem; border-radius: 9999px; background-color: #f3f4f6; border: 1px solid #e5e7eb; font-size: 0.875rem; color: #111827;"
                            class="dark:bg-gray-800 dark:border-gray-700 dark:text-white dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500"
                            required
                        />
                        <button
                            type="submit"
                            style="width: 40px; height: 40px; border-radius: 9999px; background-color: #2563eb; color: #ffffff; border: none; display: flex; align-items: center; justify-content: center; cursor: pointer; transition: background-color 0.2s;"
                            onmouseover="this.style.backgroundColor='#1d4ed8'"
                            onmouseout="this.style.backgroundColor='#2563eb'"
                        >
                            <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                            </svg>
                        </button>
                    </form>
                </div>
            @else
                {{-- Empty State --}}
                <div style="flex: 1; display: flex; flex-direction: column; align-items: center; justify-content: center; background-color: #f9fafb;" class="dark:bg-gray-950">
                    <div style="text-align: center; max-width: 320px;">
                        <div style="width: 64px; height: 64px; background-color: #fee2e2; border-radius: 9999px; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem;" class="dark:bg-red-900/20">
                            <svg style="width: 32px; height: 32px; color: #ef4444;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                        </div>
                        <h3 style="font-size: 1.125rem; font-weight: bold; color: #111827; margin-bottom: 0.5rem;" class="dark:text-white">
                            {{ $activeTab === 'organizations' ? __('common.select_organization') : __('common.select_recipient') }}
                        </h3>
                        <p style="font-size: 0.875rem; color: #6b7280;" class="dark:text-gray-400">
                            {{ $activeTab === 'organizations' ? __('common.select_org_to_start_chat') : __('common.select_user_from_list') }}
                        </p>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <script>
        document.addEventListener('livewire:initialized', () => {
            const chatWindow = document.getElementById('support-chat-window');
            const scrollToBottom = () => {
                if (chatWindow) {
                    chatWindow.scrollTop = chatWindow.scrollHeight;
                }
            };
            
            // Scroll on initial load
            scrollToBottom();
            
            // Scroll when a message is sent
            Livewire.on('messageSent', () => {
                setTimeout(scrollToBottom, 50);
            });
            
            // Watch for new messages (polling or others)
            const observer = new MutationObserver(() => {
                if (!chatWindow) return;
                const isNearBottom = chatWindow.scrollHeight - chatWindow.scrollTop - chatWindow.clientHeight < 150;
                if (isNearBottom) {
                    scrollToBottom();
                }
            });
            
            if (chatWindow) {
                observer.observe(chatWindow, { childList: true, subtree: true });
            }
        });
    </script>
</x-filament-panels::page>
