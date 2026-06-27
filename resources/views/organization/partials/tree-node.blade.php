<div class="tree-node ml-{{ $level * 6 }} relative">
    <div class="flex items-center gap-3 p-3 hover:bg-white/5 rounded-lg transition border-l-4 
        @if($node->level == 1) border-purple-500
        @elseif($node->level == 2) border-blue-500
        @elseif($node->level == 3) border-green-500
        @elseif($node->level == 4) border-yellow-500
        @else border-gray-500 @endif">
        
        <div class="flex-1 flex flex-wrap items-center gap-3">
            <span class="text-xs px-2 py-0.5 rounded-full level-badge-{{ $node->level }}">
                Level {{ $node->level }}
            </span>
            <span class="font-semibold text-white">{{ $node->position_name }}</span>
            @if($node->level_name)
                <span class="text-xs text-gray-400">({{ $node->level_name }})</span>
            @endif
            @if($node->position_code)
                <span class="text-xs text-gray-500 font-mono">{{ $node->position_code }}</span>
            @endif
            @if($node->holders->count() > 0)
                <span class="text-xs text-blue-400">
                    👤 {{ $node->holders->pluck('name')->implode(', ') }}
                </span>
            @endif
        </div>
        
        <div class="flex items-center gap-2 flex-shrink-0">
            @if($node->description)
                <span class="text-xs text-gray-500 cursor-help" title="{{ $node->description }}">ℹ️</span>
            @endif
            <a href="{{ route('organization.show', $node) }}" class="text-blue-400 hover:text-blue-300 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                </svg>
            </a>
            <a href="{{ route('organization.edit', $node) }}" class="text-yellow-400 hover:text-yellow-300 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
            </a>
            <a href="{{ route('organization.duplicate', $node) }}" 
               onclick="return confirm('Duplikasi jabatan {{ $node->position_name }}?')"
               class="text-purple-400 hover:text-purple-300 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                </svg>
            </a>
            <form action="{{ route('organization.destroy', $node) }}" method="POST" class="inline">
                @csrf
                @method('DELETE')
                <button type="submit" 
                        onclick="return confirm('Hapus jabatan {{ $node->position_name }}?')"
                        class="text-red-400 hover:text-red-300 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                </button>
            </form>
        </div>
    </div>
    
    @if(isset($node->children_list) && count($node->children_list) > 0)
        <div class="pl-4 border-l border-white/10 ml-2">
            @foreach($node->children_list as $child)
                @include('organization.partials.tree-node', ['node' => $child, 'level' => $level + 1])
            @endforeach
        </div>
    @endif
</div>