<x-filament-panels::page>
    <div class="flex h-[calc(100vh-12rem)] bg-white dark:bg-gray-900 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        {{-- Sidebar: Organizations List --}}
        <div class="w-1/4 border-r border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800/50 flex flex-col">
            <div class="p-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-sm font-bold text-gray-900 dark:text-white">{{ __('common.organizations') }}</h3>
            </div>
            <div class="flex-1 overflow-y-auto p-2 space-y-1">
                @foreach($this->getOrganizations() as $org)
                    <button 
                        wire:click="$set('organization_id', {{ $org->id }})"
                        class="w-full flex items-center p-3 rounded-lg transition-all duration-200 {{ $organization_id == $org->id ? 'bg-primary-50 dark:bg-primary-900/20 border-primary-500' : 'hover:bg-gray-100 dark:hover:bg-gray-700' }}"
                    >
                        <div class="w-8 h-8 rounded-full bg-primary-100 dark:bg-primary-900/50 flex items-center justify-center text-primary-600 dark:text-primary-400 text-xs font-bold mr-3 rtl:ml-3 rtl:mr-0">
                            {{ strtoupper(substr($org->name, 0, 1)) }}
                        </div>
                        <div class="flex-1 text-left rtl:text-right overflow-hidden">
                            <p class="text-xs font-semibold text-gray-900 dark:text-white truncate">{{ $org->name }}</p>
                        </div>
                    </button>
                @endforeach
            </div>
        </div>

        {{-- Main Chat Area --}}
        <div class="flex-1 flex flex-col min-w-0 bg-white dark:bg-gray-900">
            @if($organization_id)
                @php $currentOrg = \App\Models\Organization::find($organization_id); @endphp
                {{-- Chat Header --}}
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800/50 flex items-center justify-between">
                    <div class="flex items-center space-x-3 rtl:space-x-reverse">
                        <div>
                            <h2 class="text-sm font-semibold text-gray-900 dark:text-white">{{ $currentOrg?->name }}</h2>
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('common.support_chat_with_org') }}</p>
                        </div>
                    </div>
                </div>

                {{-- Chat Messages --}}
                <div id="chat-window" class="flex-1 overflow-y-auto p-6 space-y-6 bg-gray-50/30 dark:bg-gray-900/30" wire:poll.5s>
                    @php
                        $messages = $this->getMessages();
                        $lastDate = null;
                    @endphp
                    
                    @forelse($messages as $msg)
                        @php
                            $currentDate = $msg->created_at->translatedFormat('j F Y');
                            $isOwn = $msg->user_id === auth()->id();
                        @endphp

                        @if($lastDate !== $currentDate)
                            <div class="flex justify-center my-4">
                                <span class="px-3 py-1 text-[10px] font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400 bg-gray-100 dark:bg-gray-800 rounded-full shadow-sm">
                                    {{ $currentDate }}
                                </span>
                            </div>
                            @php $lastDate = $currentDate; @endphp
                        @endif

                        <div class="flex {{ $isOwn ? 'justify-end' : 'justify-start' }} animate-fade-in">
                            <div class="flex max-w-[80%] {{ $isOwn ? 'flex-row-reverse' : 'flex-row' }} items-end gap-2">
                                <div class="flex-shrink-0 mb-1">
                                    <div class="w-8 h-8 rounded-full bg-gray-200 dark:bg-gray-700 flex items-center justify-center text-xs font-bold text-gray-500 border-2 border-white dark:border-gray-800 shadow-sm">
                                        {{ strtoupper(substr($msg->user->name, 0, 1)) }}
                                    </div>
                                </div>
                                <div class="flex flex-col {{ $isOwn ? 'items-end' : 'items-start' }}">
                                    <div class="flex items-center mb-1 gap-2 {{ $isOwn ? 'flex-row-reverse' : '' }}">
                                        <span class="text-[11px] font-semibold text-gray-600 dark:text-gray-300">{{ $msg->user->name }}</span>
                                        <span class="text-[10px] text-gray-400 dark:text-gray-500">{{ $msg->created_at->format('H:i') }}</span>
                                    </div>
                                    <div class="px-4 py-2.5 rounded-2xl shadow-sm text-sm {{ $isOwn ? 'bg-primary-600 text-white rounded-tr-none' : 'bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-200 border border-gray-100 dark:border-gray-700 rounded-tl-none' }}">
                                        {{ $msg->message }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="flex flex-col items-center justify-center h-full opacity-60">
                            <x-heroicon-o-chat-bubble-left-right class="w-8 h-8 text-gray-400 mb-2" />
                            <p class="text-sm text-gray-500">{{ __('common.no_messages_yet') }}</p>
                        </div>
                    @endforelse
                </div>

                {{-- Message Input --}}
                <div class="p-4 border-t border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800">
                    <form wire:submit.prevent="sendMessage" class="flex items-center gap-3">
                        <div class="flex-1">
                            <x-filament::input.wrapper class="rounded-full">
                                <x-filament::input
                                    type="text"
                                    wire:model="message"
                                    placeholder="{{ __('common.type_your_message') }}"
                                    autocomplete="off"
                                    class="py-3 px-6"
                                    required
                                />
                            </x-filament::input.wrapper>
                        </div>
                        <button type="submit" class="p-3 bg-primary-600 text-white rounded-full shadow-md hover:bg-primary-700 transition-all">
                            <x-heroicon-m-paper-airplane class="w-5 h-5" />
                        </button>
                    </form>
                </div>
            @else
                <div class="flex-1 flex flex-col items-center justify-center bg-gray-50/30 dark:bg-gray-900/30">
                    <div class="p-6 bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 text-center max-w-sm">
                        <div class="w-16 h-16 bg-primary-100 dark:bg-primary-900/50 rounded-full flex items-center justify-center mx-auto mb-4">
                            <x-heroicon-o-building-office-2 class="w-8 h-8 text-primary-600 dark:text-primary-400" />
                        </div>
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">{{ __('common.select_organization') }}</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('common.select_org_to_start_chat') }}</p>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <style>
        @keyframes fade-in { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
        .animate-fade-in { animation: fade-in 0.3s ease-out forwards; }
    </style>

    <script>
        document.addEventListener('livewire:initialized', () => {
            const chatWindow = document.getElementById('chat-window');
            const scrollToBottom = () => { if(chatWindow) chatWindow.scrollTop = chatWindow.scrollHeight; };
            scrollToBottom();
            Livewire.on('messageSent', () => { setTimeout(scrollToBottom, 50); });
            const observer = new MutationObserver(() => {
                if(!chatWindow) return;
                const isNearBottom = chatWindow.scrollHeight - chatWindow.scrollTop - chatWindow.clientHeight < 100;
                if (isNearBottom) scrollToBottom();
            });
            if(chatWindow) observer.observe(chatWindow, { childList: true });
        });
    </script>
</x-filament-panels::page>
