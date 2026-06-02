<div class="p-6">
    @include('jadwal-pelajaran::components.styles')

    <!-- Step Indicator -->
    <div class="step-indicator">
        <div class="step {{ $jadwal_date ? 'completed' : 'active' }}">
            <div class="step-circle">1</div>
            <div class="step-label">Pilih Hari</div>
        </div>
        <div class="step {{ $jadwal_date && !$duration ? 'active' : ($duration ? 'completed' : '') }}">
            <div class="step-circle">2</div>
            <div class="step-label">Durasi</div>
        </div>
        <div class="step {{ $duration && !$selected_kelas ? 'active' : ($selected_kelas ? 'completed' : '') }}">
            <div class="step-circle">3</div>
            <div class="step-label">Pilih Kelas & Jam</div>
        </div>
        <div class="step {{ $selected_kelas && $selected_time_slot ? 'active' : '' }}">
            <div class="step-circle">4</div>
            <div class="step-label">Detail Jadwal</div>
        </div>
    </div>

    <form wire:submit="createJadwalPelajaran">

        <!-- Step 1: Pilih Hari -->
        <div class="mb-8">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
                Pilih Hari Jadwal Pelajaran
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Tanggal</label>
                    <input
                        type="date"
                        wire:model.live="jadwal_date"
                        min="{{ \Carbon\Carbon::today()->format('Y-m-d') }}"
                        class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:ring-2 focus:ring-gray-500 focus:border-transparent"
                    >
                </div>
                @if($jadwal_date)
                <div class="flex items-end">
                    <div class="bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 p-4 rounded-lg">
                        <p class="text-sm text-gray-700 dark:text-gray-300">
                            Hari dipilih: <strong>{{ \Carbon\Carbon::parse($jadwal_date)->locale('id')->isoFormat('dddd, D MMMM Y') }}</strong>
                        </p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                            Sistem akan mengecek ketersediaan slot kelas untuk hari tersebut.
                        </p>
                    </div>
                </div>
                @endif
            </div>
        </div>

        <!-- Step 2: Pilih Durasi -->
        @if($jadwal_date)
        <div class="mb-8">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
                Berapa Jam Durasi Pelajaran?
            </h3>
            <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
                @foreach([1, 2, 3, 4, 5] as $hours)
                <div
                    class="duration-card {{ $duration == $hours ? 'selected' : '' }}"
                    wire:click="$set('duration', {{ $hours }})"
                >
                    <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ $hours }}</div>
                    <div class="text-sm text-gray-600 dark:text-gray-300">Jam</div>
                    <div class="text-xs text-gray-500 dark:text-gray-400 mt-2">{{ $hours * 60 }} menit</div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Step 3: Pilih Kelas & Jam -->
        @if($duration && count($available_slots) > 0)
        <div class="mb-8">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
                Pilih Kelas & Jam Pelajaran
            </h3>

            <div class="bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 p-4 rounded-lg mb-4">
                <p class="text-sm text-gray-700 dark:text-gray-300">
                    Tersedia <strong>{{ count($available_slots) }} kelas</strong> dengan total
                    <strong>{{ collect($available_slots)->sum(fn($k) => count($k['slots'])) }} slot waktu</strong>
                </p>
            </div>

            @foreach($available_slots as $kelasId => $kelasData)
            <div class="court-section mb-6">
                <div class="flex items-center justify-between mb-4">
                    <h4 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                        {{ $kelasData['label'] }}
                    </h4>
                    <span class="text-sm text-gray-600 dark:text-gray-300">
                        {{ count($kelasData['slots']) }} slot tersedia
                    </span>
                </div>

                <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-3">
                    @foreach($kelasData['slots'] as $slot)
                    <button
                        type="button"
                        class="time-slot-btn {{ $selected_kelas == $kelasId && $selected_time_slot && $selected_time_slot['start'] === $slot['start'] ? 'selected' : '' }}"
                        wire:click="selectSlot('{{ $kelasId }}', '{{ $slot['start'] }}', '{{ $slot['end'] }}')"
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
                <p class="text-gray-700 dark:text-gray-300 font-semibold mb-2">Tidak ada slot tersedia</p>
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    Untuk hari <strong>{{ \Carbon\Carbon::parse($jadwal_date)->locale('id')->isoFormat('dddd') }}</strong>
                    dengan durasi {{ $duration }} jam, semua kelas sudah penuh.
                </p>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-2">Coba pilih hari lain atau kurangi durasi.</p>
            </div>
        </div>
        @endif

        <!-- Step 4: Detail Jadwal -->
        @if($selected_kelas && $selected_time_slot)
        <div class="mb-8">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
                Detail Jadwal Pelajaran
            </h3>

            <!-- Summary Box -->
            <div class="bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 p-6 rounded-lg mb-6">
                <h4 class="font-semibold mb-3 text-lg text-gray-900 dark:text-gray-100">Ringkasan Jadwal</h4>
                <div class="grid grid-cols-2 md:grid-cols-3 gap-4 text-sm">
                    <div>
                        <div class="text-gray-600 dark:text-gray-400">Hari</div>
                        <div class="font-semibold text-gray-900 dark:text-gray-100">
                            {{ \Carbon\Carbon::parse($jadwal_date)->locale('id')->isoFormat('dddd') }}
                        </div>
                    </div>
                    <div>
                        <div class="text-gray-600 dark:text-gray-400">Kelas</div>
                        <div class="font-semibold text-gray-900 dark:text-gray-100">
                            {{ collect($available_slots)->get($selected_kelas)['label'] ?? '-' }}
                        </div>
                    </div>
                    <div>
                        <div class="text-gray-600 dark:text-gray-400">Jam</div>
                        <div class="font-semibold text-gray-900 dark:text-gray-100">
                            {{ $selected_time_slot['start'] }} - {{ $selected_time_slot['end'] }}
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Mata Pelajaran -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Mata Pelajaran *</label>
                    <select
                        wire:model="mata_pelajaran_id"
                        class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:ring-2 focus:ring-gray-500 focus:border-transparent"
                    >
                        <option value="">-- Pilih Mata Pelajaran --</option>
                        @foreach($listMataPelajaran as $mp)
                            <option value="{{ $mp->id }}">{{ $mp->pelajaran }}</option>
                        @endforeach
                    </select>
                    @error('mata_pelajaran_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <!-- Guru -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Guru *</label>
                    <select
                        wire:model="guru_id"
                        class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:ring-2 focus:ring-gray-500 focus:border-transparent"
                    >
                        <option value="">-- Pilih Guru --</option>
                        @foreach($listGuru as $guru)
                            <option value="{{ $guru->id }}">{{ $guru->name }}</option>
                        @endforeach
                    </select>
                    @error('guru_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <!-- Tahun Ajaran -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Tahun Ajaran *</label>
                    <select
                        wire:model="tahun_ajaran_id"
                        class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:ring-2 focus:ring-gray-500 focus:border-transparent"
                    >
                        <option value="">-- Pilih Tahun Ajaran --</option>
                        @foreach($listTahunAjaran as $ta)
                            <option value="{{ $ta->id }}">{{ $ta->tahun_ajaran }}</option>
                        @endforeach
                    </select>
                    @error('tahun_ajaran_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
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
                    Simpan Jadwal
                </button>
            </div>
        </div>
        @endif

    </form>
</div>