<x-filament-panels::page>
<div class="max-w-md mx-auto space-y-4 pb-8">

    {{-- Header info waktu --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-5 text-center">
        <p id="jam-sekarang" class="text-4xl font-bold text-gray-800 dark:text-white tabular-nums"></p>
        <p id="tanggal-sekarang" class="text-sm text-gray-500 dark:text-gray-400 mt-1"></p>

        @if($this->isHariLibur)
        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300 mt-2">
            Hari Libur
        </span>
        @endif
    </div>

    {{-- Info guru --}}
    @if($this->guru)
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-4 flex items-center gap-3">
        <div class="w-12 h-12 rounded-full bg-primary-100 dark:bg-primary-900/30 flex items-center justify-center flex-shrink-0">
            <span class="text-primary-600 dark:text-primary-400 font-bold text-lg">
                {{ strtoupper(substr($this->guru->name, 0, 1)) }}
            </span>
        </div>
        <div>
            <p class="font-semibold text-gray-800 dark:text-white">{{ $this->guru->name }}</p>
            <p class="text-xs text-gray-400 dark:text-gray-500">
                Jam masuk: {{ \Modules\AbsensiStaf\Models\AbsensiStaf::JAM_MASUK }} ·
                Jam pulang: {{ $this->jamPulang }}
            </p>
        </div>
    </div>
    @else
    <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-2xl p-4 text-center">
        <p class="text-sm text-red-600 dark:text-red-400 font-medium">
            Akun kamu belum terhubung ke data guru. Hubungi admin.
        </p>
    </div>
    @endif

    {{-- Status hari ini --}}
    @if($this->absensiHariIni)
    @php $absensi = $this->absensiHariIni; @endphp
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-4">
        <p class="text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase mb-3">Status Hari Ini</p>
        <div class="grid grid-cols-2 gap-3">
            <div class="bg-green-50 dark:bg-green-900/20 rounded-xl p-3 text-center">
                <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Clock In</p>
                <p class="text-lg font-bold text-green-600 dark:text-green-400">
                    {{ $absensi->clock_in_at?->format('H:i') ?? '-' }}
                </p>
                @if($absensi->telat > 0)
                <p class="text-xs text-red-500 mt-0.5">Terlambat {{ $absensi->telat }} menit</p>
                @endif
            </div>
            <div class="bg-blue-50 dark:bg-blue-900/20 rounded-xl p-3 text-center">
                <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Clock Out</p>
                <p class="text-lg font-bold text-blue-600 dark:text-blue-400">
                    {{ $absensi->clock_out_at?->format('H:i') ?? '-' }}
                </p>
                @if($absensi->durasi)
                <p class="text-xs text-gray-400 mt-0.5">{{ $absensi->durasi }}</p>
                @endif
            </div>
        </div>

        @php
            $badgeColor = match($absensi->status) {
                'hadir'     => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300',
                'terlambat' => 'bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-300',
                default     => 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300',
            };
        @endphp
        <div class="mt-3 text-center">
            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium {{ $badgeColor }}">
                {{ \Modules\AbsensiStaf\Models\AbsensiStaf::STATUS[$absensi->status] ?? '-' }}
            </span>
        </div>
    </div>
    @endif

    {{-- Form Clock In / Clock Out --}}
    @if($this->guru && !$this->isHariLibur && $this->statusHariIni !== 'sudah_clock_out')

        {{-- Area Kamera --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 overflow-hidden">

            @if(!$foto_base64)
            {{-- Preview kamera --}}
            <div class="relative bg-black" style="min-height: 280px;">
                <video id="kamera-video" autoplay playsinline muted
                    class="w-full object-cover" style="min-height: 280px; display: none;"></video>
                <canvas id="kamera-canvas" class="hidden"></canvas>

                {{-- Placeholder sebelum kamera aktif --}}
                <div id="kamera-placeholder" class="absolute inset-0 flex flex-col items-center justify-center">
                    <svg class="w-16 h-16 text-gray-600 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    <p class="text-gray-500 text-sm">Kamera belum aktif</p>
                    <button onclick="aktifkanKamera()"
                        class="mt-3 px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium rounded-lg transition-colors">
                        Aktifkan Kamera
                    </button>
                </div>

                {{-- Tombol ambil foto --}}
                <button id="tombol-foto" onclick="ambilFoto()"
                    class="hidden absolute bottom-4 left-1/2 -translate-x-1/2 w-14 h-14 rounded-full bg-white shadow-lg flex items-center justify-center border-4 border-primary-500">
                    <div class="w-10 h-10 rounded-full bg-primary-500"></div>
                </button>
            </div>
            @else
            {{-- Preview foto yang sudah diambil --}}
            <div class="relative">
                <img id="foto-preview" src="{{ $foto_base64 }}" alt="Foto"
                    class="w-full object-cover" style="max-height: 280px;">
                <button wire:click="$set('foto_base64', null)"
                    onclick="resetKamera()"
                    class="absolute top-3 right-3 p-2 bg-black/50 hover:bg-black/70 text-white rounded-full transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4l16 16M4 20L20 4"/>
                    </svg>
                </button>
                <div class="absolute bottom-3 left-3 bg-green-500 text-white text-xs font-medium px-2 py-1 rounded-lg flex items-center gap-1">
                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                    </svg>
                    Foto diambil
                </div>
            </div>
            @endif

            {{-- Info lokasi --}}
            <div class="px-4 py-3 border-t border-gray-100 dark:border-gray-700 flex items-center gap-2">
                <div id="lokasi-status" class="flex items-center gap-2 flex-1">
                    <div class="w-2 h-2 rounded-full bg-gray-400 animate-pulse"></div>
                    <span class="text-xs text-gray-400">Mendeteksi lokasi...</span>
                </div>
            </div>
        </div>

        {{-- Tombol Clock In / Clock Out --}}
        <div class="space-y-3">
            @if($this->statusHariIni === 'belum_clock_in')
            <button wire:click="clockIn" wire:loading.attr="disabled"
                class="w-full py-4 bg-green-500 hover:bg-green-600 active:bg-green-700 text-white font-bold text-lg rounded-2xl transition-colors shadow-lg shadow-green-500/30 disabled:opacity-60">
                <span wire:loading.remove wire:target="clockIn">
                    ✓ Clock In Sekarang
                </span>
                <span wire:loading wire:target="clockIn">
                    Menyimpan...
                </span>
            </button>
            @elseif($this->statusHariIni === 'sudah_clock_in')
            <button wire:click="clockOut" wire:loading.attr="disabled"
                class="w-full py-4 bg-blue-500 hover:bg-blue-600 active:bg-blue-700 text-white font-bold text-lg rounded-2xl transition-colors shadow-lg shadow-blue-500/30 disabled:opacity-60">
                <span wire:loading.remove wire:target="clockOut">
                    → Clock Out Sekarang
                </span>
                <span wire:loading wire:target="clockOut">
                    Menyimpan...
                </span>
            </button>
            @endif
        </div>

    @elseif($this->statusHariIni === 'sudah_clock_out')
    {{-- Selesai hari ini --}}
    <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-2xl p-6 text-center">
        <svg class="w-12 h-12 text-green-500 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <p class="font-semibold text-green-700 dark:text-green-300 text-lg">Absensi Selesai</p>
        <p class="text-sm text-green-600 dark:text-green-400 mt-1">
            Durasi kerja: {{ $this->absensiHariIni?->durasi ?? '-' }}
        </p>
    </div>
    @endif

</div>

@push('scripts')
<script>
    let stream = null;

    // Update jam real-time
    function updateJam() {
        const now = new Date();
        const jam = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
        const tgl = now.toLocaleDateString('id-ID', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' });
        const elJam = document.getElementById('jam-sekarang');
        const elTgl = document.getElementById('tanggal-sekarang');
        if (elJam) elJam.textContent = jam;
        if (elTgl) elTgl.textContent = tgl;
    }
    setInterval(updateJam, 1000);
    updateJam();

    // Aktifkan kamera
    async function aktifkanKamera() {
        try {
            stream = await navigator.mediaDevices.getUserMedia({
                video: { facingMode: 'user', width: { ideal: 1280 }, height: { ideal: 720 } }
            });
            const video = document.getElementById('kamera-video');
            const placeholder = document.getElementById('kamera-placeholder');
            const tombolFoto = document.getElementById('tombol-foto');

            if (video) {
                video.srcObject = stream;
                video.style.display = 'block';
            }
            if (placeholder) placeholder.style.display = 'none';
            if (tombolFoto) tombolFoto.classList.remove('hidden');
        } catch (err) {
            alert('Tidak bisa mengakses kamera: ' + err.message);
        }
    }

    // Ambil foto
    function ambilFoto() {
        const video   = document.getElementById('kamera-video');
        const canvas  = document.getElementById('kamera-canvas');
        if (!video || !canvas) return;

        canvas.width  = video.videoWidth;
        canvas.height = video.videoHeight;
        canvas.getContext('2d').drawImage(video, 0, 0);

        const base64 = canvas.toDataURL('image/jpeg', 0.8);

        // Kirim ke Livewire
        @this.set('foto_base64', base64);

        // Stop kamera
        if (stream) {
            stream.getTracks().forEach(t => t.stop());
            stream = null;
        }
    }

    // Reset kamera
    function resetKamera() {
        if (stream) {
            stream.getTracks().forEach(t => t.stop());
            stream = null;
        }
    }

    // Deteksi lokasi GPS
    // function deteksiLokasi() {
    //     if (!navigator.geolocation) {
    //         updateLokasiStatus(false, 'Browser tidak mendukung GPS');
    //         return;
    //     }

    //     navigator.geolocation.watchPosition(
    //         (pos) => {
    //             const lat = pos.coords.latitude;
    //             const lng = pos.coords.longitude;
    //             @this.set('lat', lat);
    //             @this.set('lng', lng);
    //             updateLokasiStatus(true, `${lat.toFixed(5)}, ${lng.toFixed(5)}`);
    //         },
    //         (err) => {
    //             updateLokasiStatus(false, 'Gagal deteksi lokasi — izinkan akses GPS');
    //         },
    //         { enableHighAccuracy: true, timeout: 10000 }
    //     );
    // }
    // Deteksi lokasi GPS
    function deteksiLokasi() {
        if (!navigator.geolocation) {
            updateLokasiStatus(false, 'Browser tidak mendukung GPS');
            return;
        }

        // Menggunakan getCurrentPosition agar tidak membebani koneksi secara berkala
        navigator.geolocation.getCurrentPosition(
            (pos) => {
                const lat = pos.coords.latitude;
                const lng = pos.coords.longitude;
                @this.set('lat', lat);
                @this.set('lng', lng);
                updateLokasiStatus(true, `${lat.toFixed(5)}, ${lng.toFixed(5)}`);
            },
            (err) => {
                // Jika gagal dengan High Accuracy, coba dengan akurasi rendah (IP berbasis Wi-Fi)
                if (err.code === err.TIMEOUT) {
                    updateLokasiStatus(false, 'Mencoba mendeteksi ulang...');
                     AmbilLokasiCepat();
                } else {
                    updateLokasiStatus(false, 'Gagal deteksi lokasi — izinkan akses GPS');
                }
            },
            { 
                enableHighAccuracy: false, // Set false saat dev di laptop agar memakai IP/Wifi yang lebih cepat
                timeout: 5000,             // Batasi tunggu maksimal 5 detik
                maximumAge: 60000          // Boleh menggunakan data lokasi yang sudah tersimpan di cache 1 menit lalu
            }
        );
    }

    function AmbilLokasiCepat() {
        navigator.geolocation.getCurrentPosition(
            (pos) => {
                const lat = pos.coords.latitude;
                const lng = pos.coords.longitude;
                @this.set('lat', lat);
                @this.set('lng', lng);
                updateLokasiStatus(true, `${lat.toFixed(5)}, ${lng.toFixed(5)} (IP Base)`);
            },
            (err) => {
                updateLokasiStatus(false, 'Gagal mendeteksi lokasi. Pastikan GPS aktif.');
            },
            { enableHighAccuracy: false, timeout: 10000, maximumAge: Infinity }
        );
    }

    function updateLokasiStatus(sukses, pesan) {
        const el = document.getElementById('lokasi-status');
        if (!el) return;
        el.innerHTML = sukses
            ? `<div class="w-2 h-2 rounded-full bg-green-500"></div><span class="text-xs text-green-600 dark:text-green-400">${pesan}</span>`
            : `<div class="w-2 h-2 rounded-full bg-red-500"></div><span class="text-xs text-red-500">${pesan}</span>`;
    }

    // Jalankan deteksi lokasi otomatis
    document.addEventListener('DOMContentLoaded', deteksiLokasi);
    document.addEventListener('livewire:navigated', deteksiLokasi);
</script>
@endpush

</x-filament-panels::page>