@extends('layouts.app')

@section('title', 'Edit Sponsor - ' . $sponsor->name)
@section('page-title', 'Edit Sponsor')
@section('page-description', 'Update sponsor information')

@section('content')
<div class="max-w-6xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('sponsors.index') }}" class="text-gray-400 hover:text-white transition flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Kembali ke Daftar Sponsor
        </a>
    </div>

    <div class="bg-gradient-to-br from-white/5 to-white/10 rounded-xl border border-white/10 p-8">
        <div class="flex items-center gap-3 mb-6">
            <div class="w-12 h-12 bg-gradient-to-br from-yellow-400 to-orange-500 rounded-xl flex items-center justify-center">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
            </div>
            <div>
                <h2 class="text-2xl font-bold text-white">Edit Sponsor</h2>
                <p class="text-sm text-gray-400">Update data sponsor: {{ $sponsor->name }}</p>
            </div>
        </div>

        <form action="{{ route('sponsors.update', $sponsor) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Left Column -->
                <div class="space-y-6">
                    <div>
                        <label class="block text-gray-300 mb-2 font-semibold">Nama Sponsor *</label>
                        <input type="text" 
                               name="name" 
                               value="{{ old('name', $sponsor->name) }}"
                               class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-green-500 @error('name') border-red-500 @enderror"
                               required>
                        @error('name')
                            <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-gray-300 mb-2 font-semibold">Default Tier *</label>
                        <select name="tier" class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-green-500" required>
                            <option value="">Pilih Tier Default</option>
                            <option value="platinum" {{ old('tier', $sponsor->tier) == 'platinum' ? 'selected' : '' }}>Platinum</option>
                            <option value="gold" {{ old('tier', $sponsor->tier) == 'gold' ? 'selected' : '' }}>Gold</option>
                            <option value="silver" {{ old('tier', $sponsor->tier) == 'silver' ? 'selected' : '' }}>Silver</option>
                            <option value="bronze" {{ old('tier', $sponsor->tier) == 'bronze' ? 'selected' : '' }}>Bronze</option>
                            <option value="partner" {{ old('tier', $sponsor->tier) == 'partner' ? 'selected' : '' }}>Partner</option>
                        </select>
                        <p class="text-xs text-gray-400 mt-1">Tier default jika tidak ditentukan khusus per event</p>
                        @error('tier')
                            <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-gray-300 mb-2 font-semibold">Logo Sponsor</label>
                        
                        @if($sponsor->logo)
                        <div class="mb-3">
                            <p class="text-xs text-gray-400 mb-2">Logo Saat Ini:</p>
                            <img src="{{ Storage::url($sponsor->logo) }}" class="w-24 h-24 rounded-lg object-cover border border-white/10">
                        </div>
                        @endif
                        
                        <input type="file" 
                               name="logo" 
                               accept="image/*"
                               class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-green-500 cursor-pointer file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-green-500 file:text-white file:cursor-pointer hover:file:bg-green-600">
                        <p class="text-xs text-gray-400 mt-1">Kosongkan jika tidak ingin mengganti logo</p>
                        @error('logo')
                            <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-gray-300 mb-2 font-semibold">Website</label>
                        <input type="url" 
                               name="website" 
                               value="{{ old('website', $sponsor->website) }}"
                               class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-green-500"
                               placeholder="https://example.com">
                        @error('website')
                            <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Right Column -->
                <div class="space-y-6">
                    <div>
                        <label class="block text-gray-300 mb-2 font-semibold">Email</label>
                        <input type="email" 
                               name="email" 
                               value="{{ old('email', $sponsor->email) }}"
                               class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-green-500">
                        @error('email')
                            <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-gray-300 mb-2 font-semibold">No. Telepon</label>
                        <input type="text" 
                               name="phone" 
                               value="{{ old('phone', $sponsor->phone) }}"
                               class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-green-500">
                        @error('phone')
                            <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-gray-300 mb-2 font-semibold">Sort Order</label>
                        <input type="number" 
                               name="sort_order" 
                               value="{{ old('sort_order', $sponsor->sort_order) }}"
                               class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-green-500">
                        <p class="text-xs text-gray-400 mt-1">Semakin kecil angka, semakin atas tampilannya</p>
                    </div>

                    <div class="flex items-center gap-2">
                        <input type="checkbox" 
                               name="is_active" 
                               id="is_active" 
                               value="1"
                               {{ old('is_active', $sponsor->is_active) ? 'checked' : '' }}
                               class="w-4 h-4 rounded bg-white/5 border-white/10">
                        <label for="is_active" class="text-gray-300">Aktif</label>
                    </div>
                </div>
            </div>

            <!-- Description -->
            <div class="mt-6">
                <label class="block text-gray-300 mb-2 font-semibold">Deskripsi</label>
                <textarea name="description" 
                          rows="4"
                          class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-green-500"
                          placeholder="Deskripsi tentang sponsor...">{{ old('description', $sponsor->description) }}</textarea>
                @error('description')
                    <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Event-Specific Tier Settings -->
            <div class="mt-6">
                <label class="block text-gray-300 mb-2 font-semibold">Pengaturan Tier per Event</label>
                <p class="text-xs text-gray-400 mb-3">Atur tier berbeda untuk setiap event (kosongkan untuk menggunakan default tier)</p>
                
                <div id="eventSponsorsContainer">
                    @php $eventIndex = 0; @endphp
                    @foreach($eventSponsors as $eventSponsor)
                    <div class="event-sponsor-item bg-white/5 border border-white/10 rounded-lg p-4 mb-3">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-gray-400 text-sm mb-1">Event</label>
                                <select name="event_sponsors[{{ $eventIndex }}][event_id]" class="event-select w-full bg-black/30 border border-white/10 rounded-lg px-3 py-2 text-white focus:outline-none focus:border-green-500">
                                    <option class="text-black" value="">Pilih Event</option>
                                    @foreach($events as $event)
                                        <option class="text-black" value="{{ $event->id }}" {{ $eventSponsor['event_id'] == $event->id ? 'selected' : '' }}>
                                            {{ $event->title }} ({{ $event->start_date->format('d M Y') }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-gray-400 text-sm mb-1">Tier untuk Event Ini</label>
                                <select name="event_sponsors[{{ $eventIndex }}][tier]" class="w-full bg-black/30 border border-white/10 rounded-lg px-3 py-2 text-white focus:outline-none focus:border-green-500">
                                    <option class="text-black" value="">Gunakan Default Tier</option>
                                    <option class="text-black" value="platinum" {{ $eventSponsor['tier'] == 'platinum' ? 'selected' : '' }}>Platinum</option>
                                    <option class="text-black" value="gold" {{ $eventSponsor['tier'] == 'gold' ? 'selected' : '' }}>Gold</option>
                                    <option class="text-black" value="silver" {{ $eventSponsor['tier'] == 'silver' ? 'selected' : '' }}>Silver</option>
                                    <option class="text-black" value="bronze" {{ $eventSponsor['tier'] == 'bronze' ? 'selected' : '' }}>Bronze</option>
                                    <option class="text-black" value="partner" {{ $eventSponsor['tier'] == 'partner' ? 'selected' : '' }}>Partner</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-gray-400 text-sm mb-1">Kontribusi (Rp)</label>
                                <input type="number" name="event_sponsors[{{ $eventIndex }}][contribution_amount]" 
                                       value="{{ $eventSponsor['contribution_amount'] ?? '' }}"
                                       class="w-full bg-black/30 border border-white/10 rounded-lg px-3 py-2 text-white focus:outline-none focus:border-green-500"
                                       placeholder="Nominal kontribusi">
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-gray-400 text-sm mb-1">Benefits</label>
                                <textarea name="event_sponsors[{{ $eventIndex }}][benefits]" rows="2"
                                          class="w-full bg-black/30 border border-white/10 rounded-lg px-3 py-2 text-white focus:outline-none focus:border-green-500"
                                          placeholder="Benefit yang didapat sponsor untuk event ini">{{ $eventSponsor['benefits'] ?? '' }}</textarea>
                            </div>
                            <div class="flex items-end">
                                <button type="button" class="remove-event-sponsor w-full bg-red-500/20 hover:bg-red-500/40 text-red-300 px-3 py-2 rounded-lg text-sm transition">
                                    Hapus
                                </button>
                            </div>
                        </div>
                    </div>
                    @php $eventIndex++; @endphp
                    @endforeach
                </div>
                
                <button type="button" id="addEventSponsor" class="mt-2 text-green-400 hover:text-green-300 text-sm flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                    </svg>
                    + Tambah Event Lain
                </button>
            </div>

            <!-- Logo Preview New -->
            <div id="logoPreview" class="mt-4 hidden">
                <label class="block text-gray-300 mb-2 font-semibold">Preview Logo Baru:</label>
                <img id="preview" class="w-24 h-24 rounded-lg object-cover border border-green-500">
            </div>

            <!-- Info Card -->
            <div class="bg-white/5 rounded-lg p-4 mt-6">
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <p class="text-gray-400">Sponsor ID</p>
                        <p class="text-white font-semibold">#{{ $sponsor->id }}</p>
                    </div>
                    <div>
                        <p class="text-gray-400">Dibuat Pada</p>
                        <p class="text-white">{{ $sponsor->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                    <div>
                        <p class="text-gray-400">Last Update</p>
                        <p class="text-white">{{ $sponsor->updated_at->format('d/m/Y H:i') }}</p>
                    </div>
                </div>
            </div>

            <div class="flex gap-3 mt-6">
                <button type="submit" class="flex-1 bg-green-500 hover:bg-green-600 text-white px-6 py-3 rounded-lg transition font-semibold">
                    Update Sponsor
                </button>
                <a href="{{ route('sponsors.index') }}" class="flex-1 bg-gray-600 hover:bg-gray-700 text-white px-6 py-3 rounded-lg transition text-center">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    let eventSponsorCount = {{ count($eventSponsors) }};
    
    // Add new event sponsor form
    document.getElementById('addEventSponsor').addEventListener('click', function() {
        const container = document.getElementById('eventSponsorsContainer');
        const template = document.createElement('div');
        template.className = 'event-sponsor-item bg-white/5 border border-white/10 rounded-lg p-4 mb-3';
        template.innerHTML = `
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <div>
                    <label class="block text-gray-400 text-sm mb-1">Event</label>
                    <select name="event_sponsors[${eventSponsorCount}][event_id]" class="event-select w-full bg-black/30 border border-white/10 rounded-lg px-3 py-2 text-white focus:outline-none focus:border-green-500">
                        <option class="text-black" value="">Pilih Event</option>
                        @foreach($events as $event)
                            <option class="text-black" value="{{ $event->id }}">{{ $event->title }} ({{ $event->start_date->format('d M Y') }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-gray-400 text-sm mb-1">Tier untuk Event Ini</label>
                    <select name="event_sponsors[${eventSponsorCount}][tier]" class="w-full bg-black/30 border border-white/10 rounded-lg px-3 py-2 text-white focus:outline-none focus:border-green-500">
                        <option class="text-black" value="">Gunakan Default Tier</option>
                        <option class="text-black" value="platinum">Platinum</option>
                        <option class="text-black" value="gold">Gold</option>
                        <option class="text-black" value="silver">Silver</option>
                        <option class="text-black" value="bronze">Bronze</option>
                        <option class="text-black" value="partner">Partner</option>
                    </select>
                </div>
                <div>
                    <label class="block text-gray-400 text-sm mb-1">Kontribusi (Rp)</label>
                    <input type="number" name="event_sponsors[${eventSponsorCount}][contribution_amount]" 
                           class="w-full bg-black/30 border border-white/10 rounded-lg px-3 py-2 text-white focus:outline-none focus:border-green-500"
                           placeholder="Nominal kontribusi">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-gray-400 text-sm mb-1">Benefits</label>
                    <textarea name="event_sponsors[${eventSponsorCount}][benefits]" rows="2"
                              class="w-full bg-black/30 border border-white/10 rounded-lg px-3 py-2 text-white focus:outline-none focus:border-green-500"
                              placeholder="Benefit yang didapat sponsor untuk event ini"></textarea>
                </div>
                <div class="flex items-end">
                    <button type="button" class="remove-event-sponsor w-full bg-red-500/20 hover:bg-red-500/40 text-red-300 px-3 py-2 rounded-lg text-sm transition">
                        Hapus
                    </button>
                </div>
            </div>
        `;
        
        container.appendChild(template);
        eventSponsorCount++;
        
        // Add remove functionality to new item
        const removeBtn = template.querySelector('.remove-event-sponsor');
        if (removeBtn) {
            removeBtn.addEventListener('click', function() {
                template.remove();
            });
        }
    });
    
    // Add remove functionality to existing remove buttons
    document.querySelectorAll('.remove-event-sponsor').forEach(btn => {
        btn.addEventListener('click', function() {
            btn.closest('.event-sponsor-item').remove();
        });
    });
    
    // Logo preview
    document.querySelector('input[name="logo"]').addEventListener('change', function(e) {
        const preview = document.getElementById('logoPreview');
        const img = document.getElementById('preview');
        
        if (e.target.files && e.target.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                img.src = e.target.result;
                preview.classList.remove('hidden');
            }
            reader.readAsDataURL(e.target.files[0]);
        } else {
            preview.classList.add('hidden');
        }
    });
</script>
@endpush
@endsection