<div class="flex h-screen bg-gray-900 text-white">
    <!-- Sidebar -->
    <div class="w-64 bg-gray-800 p-4 border-r border-gray-700">
        <h2 class="text-lg font-bold mb-4">Home OS</h2>
        <div class="space-y-2">
            <a href="/" class="block p-2 rounded hover:bg-gray-700">Chat</a>
            <a href="/mcp-servers" class="block p-2 rounded bg-gray-700">MCP Servers</a>
        </div>
    </div>

    <!-- Main Content -->
    <div class="flex-1 p-8 overflow-y-auto">
        <div class="max-w-4xl mx-auto">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-bold">MCP Servers</h1>
                <div class="flex gap-2">
                    <button wire:click="toggleAddForm" class="bg-blue-600 hover:bg-blue-700 px-4 py-2 rounded">
                        {{ $showAddForm ? 'Cancel' : 'Add Server' }}
                    </button>
                    <button wire:click="discoverServers" class="bg-green-600 hover:bg-green-700 px-4 py-2 rounded">
                        Auto-Discover
                    </button>
                </div>
            </div>

            <!-- Add Server Form -->
            @if($showAddForm)
                <div class="bg-gray-800 p-4 rounded-lg mb-6">
                    <h3 class="text-lg font-semibold mb-4">Add MCP Server</h3>
                    <form wire:submit.prevent="addServer" class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium mb-1">Name</label>
                            <input wire:model="newName" type="text" placeholder="e.g., Proxmox"
                                class="w-full bg-gray-700 border border-gray-600 rounded px-3 py-2" required />
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">Command</label>
                            <input wire:model="newCommand" type="text" placeholder="npx"
                                class="w-full bg-gray-700 border border-gray-600 rounded px-3 py-2" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">Arguments (space-separated)</label>
                            <input wire:model="newArgs" type="text" placeholder="-y mcp-proxmox"
                                class="w-full bg-gray-700 border border-gray-600 rounded px-3 py-2" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">Environment Variables (KEY=value, one per line)</label>
                            <textarea wire:model="newEnv" rows="3"
                                class="w-full bg-gray-700 border border-gray-600 rounded px-3 py-2"
                                placeholder="PROXMOX_URL=https://proxmox.local:8006&#10;PROXMOX_TOKEN_ID=your-token"></textarea>
                        </div>
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 px-4 py-2 rounded">
                            Add Server
                        </button>
                    </form>
                </div>
            @endif

            <!-- Discovered Servers -->
            @if(!empty($discoveredServers))
                <div class="bg-gray-800 p-4 rounded-lg mb-6">
                    <h3 class="text-lg font-semibold mb-4">Discovered Servers</h3>
                    <div class="space-y-2">
                        @foreach($discoveredServers as $index => $server)
                            <div class="flex justify-between items-center p-3 bg-gray-700 rounded">
                                <div>
                                    <p class="font-medium">{{ $server['name'] }}</p>
                                    <p class="text-sm text-gray-400">Source: {{ $server['source'] }}</p>
                                </div>
                                <button wire:click="installDiscovered({{ $index }})"
                                    class="bg-green-600 hover:bg-green-700 px-3 py-1 rounded text-sm">
                                    Install
                                </button>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Registry Search -->
            <div class="bg-gray-800 p-4 rounded-lg mb-6">
                <h3 class="text-lg font-semibold mb-4">Search MCP Registry</h3>
                <div class="flex gap-2 mb-4">
                    <input wire:model="registrySearch" type="text" placeholder="Search for MCP servers..."
                        class="flex-1 bg-gray-700 border border-gray-600 rounded px-3 py-2"
                        wire:keydown.enter="searchRegistry" />
                    <button wire:click="searchRegistry" class="bg-blue-600 hover:bg-blue-700 px-4 py-2 rounded">
                        Search
                    </button>
                </div>

                @if(!empty($registryResults))
                    <div class="space-y-2">
                        @foreach($registryResults as $result)
                            <div class="flex justify-between items-center p-3 bg-gray-700 rounded">
                                <div>
                                    <p class="font-medium">{{ $result['name'] ?? $result['package'] ?? 'Unknown' }}</p>
                                    <p class="text-sm text-gray-400">{{ $result['description'] ?? '' }}</p>
                                </div>
                                <button wire:click="installFromRegistry('{{ $result['package'] ?? $result['name'] ?? '' }}')"
                                    class="bg-green-600 hover:bg-green-700 px-3 py-1 rounded text-sm">
                                    Install
                                </button>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- Server List -->
            <div class="bg-gray-800 p-4 rounded-lg">
                <h3 class="text-lg font-semibold mb-4">Registered Servers</h3>

                @if(empty($servers))
                    <p class="text-gray-400">No MCP servers registered yet.</p>
                @else
                    <div class="space-y-2">
                        @foreach($servers as $server)
                            <div class="flex justify-between items-center p-3 bg-gray-700 rounded">
                                <div>
                                    <p class="font-medium">{{ $server['name'] }}</p>
                                    <p class="text-sm text-gray-400">
                                        {{ $server['command'] }} {{ implode(' ', $server['args'] ?? []) }}
                                    </p>
                                    <span class="text-xs px-2 py-1 rounded
                                        {{ $server['status'] === 'connected' ? 'bg-green-900 text-green-300' : 'bg-gray-600 text-gray-300' }}">
                                        {{ $server['status'] }}
                                    </span>
                                    <span class="text-xs px-2 py-1 rounded bg-gray-600 text-gray-300 ml-1">
                                        {{ $server['type'] }}
                                    </span>
                                </div>
                                <button wire:click="removeServer('{{ $server['id'] }}')"
                                    class="bg-red-600 hover:bg-red-700 px-3 py-1 rounded text-sm">
                                    Remove
                                </button>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
