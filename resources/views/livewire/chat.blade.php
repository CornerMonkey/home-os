<div class="flex h-screen bg-gray-900 text-white">
    <!-- Sidebar -->
    <div class="w-64 bg-gray-800 p-4 border-r border-gray-700">
        <h2 class="text-lg font-bold mb-4">Home OS</h2>
        <div class="space-y-2">
            <a href="#" class="block p-2 rounded hover:bg-gray-700">New Chat</a>
        </div>
        <div class="mt-8">
            <h3 class="text-sm font-semibold text-gray-400 mb-2">MCP Servers</h3>
            <a href="/mcp-servers" class="block p-2 rounded hover:bg-gray-700 text-sm">
                Manage Servers
            </a>
        </div>
    </div>

    <!-- Chat Area -->
    <div class="flex-1 flex flex-col">
        <!-- Messages -->
        <div class="flex-1 overflow-y-auto p-4 space-y-4" id="messages">
            @foreach($messages as $msg)
                <div class="flex {{ $msg['role'] === 'user' ? 'justify-end' : 'justify-start' }}">
                    <div class="max-w-3xl p-3 rounded-lg {{ $msg['role'] === 'user' ? 'bg-blue-600' : 'bg-gray-700' }}">
                        <p class="text-sm whitespace-pre-wrap">{!! nl2br(e($msg['content'])) !!}</p>
                    </div>
                </div>
            @endforeach

            @if($isStreaming)
                <div class="flex justify-start">
                    <div class="bg-gray-700 p-3 rounded-lg max-w-3xl">
                        <p class="text-sm animate-pulse">Thinking...</p>
                    </div>
                </div>
            @endif
        </div>

        <!-- Tool Calls -->
        @if(!empty($activeToolCalls))
            <div class="px-4 py-2 bg-gray-800 border-t border-gray-700">
                <p class="text-xs text-gray-400 mb-1">Tool Calls:</p>
                @foreach($activeToolCalls as $call)
                    <div class="flex items-center gap-2 text-sm">
                        @if($call['status'] === 'running')
                            <span class="animate-spin">⚙️</span>
                        @else
                            <span>✅</span>
                        @endif
                        <span class="text-gray-300">{{ $call['server'] }} → {{ $call['tool'] }}</span>
                    </div>
                @endforeach
            </div>
        @endif

        <!-- Confirmation -->
        @if($awaitingConfirmation)
            <div class="px-4 py-3 bg-yellow-900/50 border-t border-yellow-700">
                <p class="text-sm text-yellow-200 mb-2">⚠️ This action requires confirmation:</p>
                <p class="text-sm text-white mb-2">{{ $pendingAction }}</p>
                <div class="flex gap-2">
                    <button wire:click="confirmAction" class="bg-red-600 hover:bg-red-700 px-3 py-1 rounded text-sm">
                        Confirm
                    </button>
                    <button wire:click="cancelAction" class="bg-gray-600 hover:bg-gray-700 px-3 py-1 rounded text-sm">
                        Cancel
                    </button>
                </div>
            </div>
        @endif

        <!-- Input -->
        <div class="p-4 border-t border-gray-700">
            <form wire:submit.prevent="sendMessage" class="flex gap-2">
                <input
                    wire:model="message"
                    type="text"
                    placeholder="Ask Home OS anything..."
                    class="flex-1 bg-gray-800 border border-gray-600 rounded-lg px-4 py-2 focus:outline-none focus:border-blue-500"
                    wire:keydown.enter="sendMessage"
                />
                <button
                    type="submit"
                    class="bg-blue-600 hover:bg-blue-700 px-4 py-2 rounded-lg"
                    wire:loading.attr="disabled"
                >
                    Send
                </button>
            </form>
        </div>
    </div>
</div>
