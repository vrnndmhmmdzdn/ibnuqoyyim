<div class="p-6">
    @include('kalender-didik::components.styles')

    <!-- Step Indicator -->
    <div class="step-indicator">
        <div class="step {{ $jadwal_date ? 'completed' : 'active' }}">
            <div class="step-circle">1</div>
            <div class="step-label">Pilih Tanggal</div>
        </div>
        <div class="step {{ $jadwal_date && !$duration ? 'active' : ($duration ? 'completed' : '') }}">
            <div class="step-circle">2</div>
            <div class="step-label">Durasi Main</div>
        </div>
        <div class="step {{ $duration && !$selected_kelas ? 'active' : ($selected_kelas ? 'completed' : '') }}">
            <div class="step-circle">3</div>
            <div class="step-label">Pilih Lapangan & Jam</div>
        </div>
        <div class="step {{ $selected_kelas && $selected_time_slot ? 'active' : '' }}">
            <div class="step-circle">4</div>
            <div class="step-label">Data Customer</div>
        </div>
    </div>

    <form wire:submit="createKaldik">
        <!-- Step 1: Pilih Tanggal -->
        <div class="mb-8">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
                Pilih Tanggal Kaldik
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Tanggal Main</label>
                    <input 
                        type="date" 
                        wire:model.live="jadwal_date"
                        min="{{ \Carbon\Carbon::today()->format('Y-m-d') }}"
                        max="{{ \Carbon\Carbon::today()->addMonths(3)->format('Y-m-d') }}"
                        class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:ring-2 focus:ring-gray-500 focus:border-transparent"
                    >
                </div>
                @if($jadwal_date)
                <div class="flex items-end">
                    <div class="bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 p-4 rounded-lg">
                        <p class="text-sm text-gray-700 dark:text-gray-300">
                            Tanggal dipilih: <strong>{{ \Carbon\Carbon::parse($jadwal_date)->translatedFormat('l, d F Y') }}</strong>
                        </p>
                        @if(\Carbon\Carbon::parse($jadwal_date)->isToday())
                        <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">
                            Kaldik hari ini: Hanya menampilkan waktu mulai dari {{ \Carbon\Carbon::now()->addMinutes(30)->format('H:i') }} ke atas
                        </p>
                        @endif
                    </div>
                </div>
                @endif
            </div>
        </div>

        <!-- Step 2: Pilih Durasi -->
        @if($jadwal_date)
        <div class="mb-8">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
                Berapa Jam Mau Main?
            </h3>
            <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
                @foreach([1, 2, 3, 4, 5] as $hours)
                <div 
                    class="duration-card {{ $duration == $hours ? 'selected' : '' }}"
                    wire:click="$set('duration', {{ $hours }})"
                >
                    <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ $hours }}</div>
                    <div class="text-sm text-gray-600 dark:text-gray-300">Jam</div>
                    <div class="text-xs text-gray-500 dark:text-gray-400 mt-2">
                        Rp {{ number_format($price * $hours, 0, ',', '.') }}
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Step 3: Pilih Lapangan & Jam -->
        @if($duration && count($available_slots) > 0)
        <div class="mb-8">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
                Pilih Lapangan & Jam Main
            </h3>
            
            <div class="bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 p-4 rounded-lg mb-4">
                <p class="text-sm text-gray-700 dark:text-gray-300">
                    Tersedia <strong>{{ count($available_slots) }} lapangan</strong> dengan total <strong>{{ collect($available_slots)->sum(fn($court) => count($court['slots'])) }} slot waktu</strong>
                </p>
            </div>

            @foreach($available_slots as $courtKey => $courtData)
            <div class="court-section">
                <div class="flex items-center justify-between mb-4">
                    <h4 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                        {{ $courtData['label'] }}
                    </h4>
                    <span class="text-sm text-gray-600 dark:text-gray-300">
                        {{ count($courtData['slots']) }} slot tersedia
                    </span>
                </div>
                
                <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-3">
                    @foreach($courtData['slots'] as $slot)
                    <button
                        type="button"
                        class="time-slot-btn {{ $selected_kelas === $courtKey && $selected_time_slot && $selected_time_slot['start'] === $slot['start'] ? 'selected' : '' }}"
                        wire:click="selectSlot('{{ $courtKey }}', '{{ $slot['start'] }}', '{{ $slot['end'] }}')"
                    >
                        <div class="font-semibold">{{ $slot['start'] }}</div>
                        <div class="text-xs opacity-75">s/d {{ $slot['end'] }}</div>
                    </button>
                    @endforeach
                </div>
            </div>
            @endforeach
        </div>
        @elseif($duration && count($available_slots) === 0)
        <div class="mb-8">
            <div class="bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 p-6 rounded-lg text-center">
                <p class="text-gray-700 dark:text-gray-300 font-semibold mb-2">Maaf, tidak ada lapangan tersedia</p>
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    Untuk tanggal {{ \Carbon\Carbon::parse($jadwal_date)->translatedFormat('d F Y') }} dengan durasi {{ $duration }} jam, semua lapangan sudah penuh.
                </p>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-2">
                    Coba pilih tanggal lain atau kurangi durasi main.
                </p>
            </div>
        </div>
        @endif

        <!-- Step 4: Data Customer -->
        @if($selected_kelas && $selected_time_slot)
        <div class="mb-8">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
                Data Customer
            </h3>
            
            <!-- Summary Box -->
            <div class="bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 p-6 rounded-lg mb-6">
                <h4 class="font-semibold mb-3 text-lg text-gray-900 dark:text-gray-100">Ringkasan Kaldik</h4>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                    <div>
                        <div class="text-gray-600 dark:text-gray-400">Tanggal</div>
                        <div class="font-semibold text-gray-900 dark:text-gray-100">{{ \Carbon\Carbon::parse($jadwal_date)->format('d/m/Y') }}</div>
                    </div>
                    <div>
                        <div class="text-gray-600 dark:text-gray-400">Lapangan</div>
                        <div class="font-semibold text-gray-900 dark:text-gray-100">{{ $selected_kelas }}</div>
                    </div>
                    <div>
                        <div class="text-gray-600 dark:text-gray-400">Waktu</div>
                        <div class="font-semibold text-gray-900 dark:text-gray-100">{{ $selected_time_slot['start'] }} - {{ $selected_time_slot['end'] }}</div>
                    </div>
                    <div>
                        <div class="text-gray-600 dark:text-gray-400">Total Biaya</div>
                        <div class="font-semibold text-lg text-gray-900 dark:text-gray-100">Rp {{ number_format($price * $duration, 0, ',', '.') }}</div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Nama Tim/Customer *</label>
                    <input 
                        type="text" 
                        wire:model="name"
                        placeholder="Contoh: Tim Arsenal Jakarta"
                        class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:ring-2 focus:ring-gray-500 focus:border-transparent"
                        required
                    >
                    @error('name') <span class="text-red-500 dark:text-red-400 text-sm">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Nomor Telepon *</label>
                    <input 
                        type="tel" 
                        wire:model="phone"
                        placeholder="081234567890"
                        class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:ring-2 focus:ring-gray-500 focus:border-transparent"
                        required
                    >
                    @error('phone') <span class="text-red-500 dark:text-red-400 text-sm">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Harga per Jam *</label>
                    <div class="relative">
                        <span class="absolute left-3 top-3 text-gray-500 dark:text-gray-400">Rp</span>
                        <input 
                            type="number" 
                            wire:model.live="price"
                            step="1000"
                            class="w-full pl-12 pr-4 py-3 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:ring-2 focus:ring-gray-500 focus:border-transparent"
                            required
                        >
                    </div>
                    @error('price') <span class="text-red-500 dark:text-red-400 text-sm">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Total yang Harus Dibayar</label>
                    <div class="px-4 py-3 bg-gray-100 dark:bg-gray-600 border border-gray-300 dark:border-gray-500 rounded-lg font-semibold text-lg text-gray-900 dark:text-white">
                        Rp {{ number_format($price * $duration, 0, ',', '.') }}
                    </div>
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Catatan Tambahan</label>
                    <textarea 
                        wire:model="notes"
                        rows="3"
                        placeholder="Catatan khusus untuk kaldik ini (opsional)..."
                        class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:ring-2 focus:ring-gray-500 focus:border-transparent"
                    ></textarea>
                </div>
            </div>

            <div class="mt-6 flex justify-end gap-4">
                <button 
                    type="button"
                    wire:click="$refresh"
                    class="px-6 py-3 bg-gray-200 dark:bg-gray-600 text-gray-700 dark:text-gray-200 font-semibold rounded-lg hover:bg-gray-300 dark:hover:bg-gray-500 transition-colors"
                >
                    Reset Form
                </button>
                <button 
                    type="submit"
                    class="px-8 py-3 bg-gray-800 dark:bg-gray-700 text-white font-semibold rounded-lg hover:bg-gray-900 dark:hover:bg-gray-600 transition-colors"
                >
                    Simpan Kaldik
                </button>
            </div>
        </div>
        @endif
    </form>
</div>