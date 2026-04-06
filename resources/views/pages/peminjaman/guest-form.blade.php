@extends('layouts.app')

@section('title', 'Peminjaman Guest')

@section('content')

    {{-- ══ PAGE HEADER ══ --}}
    <div class="mb-8">
        <p class="font-sans text-[0.58rem] font-semibold tracking-[0.35em] uppercase text-label mb-1">
            Transaksi
        </p>
        <h2 class="font-serif text-ink text-3xl font-normal leading-none">
            Ajukan Peminjaman Alat
        </h2>
        <div class="mt-3 h-px w-10 bg-rule"></div>
    </div>

    {{-- ══ SUCCESS ALERT ══ --}}
    @if(session('success'))
        <div class="border-l-2 border-ink bg-cream px-6 py-5 mb-6">
            <div class="flex items-start justify-between gap-4">
                <div class="flex-1">
                    <h3 class="font-serif text-ink text-lg font-normal mb-2">Berhasil Diajukan! ✓</h3>
                    <p class="font-sans text-[0.75rem] tracking-wide text-ink mb-4">
                        {!! str_replace('<strong>', '<strong class="font-semibold">', session('success')) !!}
                    </p>
                    <div class="bg-ink/5 border border-ink/20 p-4">
                        <p class="font-sans text-[0.65rem] text-label mb-2">Kode Peminjaman Anda:</p>
                        <p class="font-mono text-xl font-bold text-ink tracking-widest">{{ session('kode_peminjaman') }}</p>
                        <p class="font-sans text-[0.65rem] text-label mt-2">Simpan kode ini untuk referensi</p>
                    </div>
                </div>
                <button onclick="this.closest('div').remove()" class="text-label hover:text-ink transition-colors">
                    <i class="fas fa-times text-sm"></i>
                </button>
            </div>
        </div>
    @endif

    {{-- ══ ERROR ALERT ══ --}}
    @if($errors->any())
        <div class="border-l-2 border-espresso bg-espresso/5 px-6 py-5 mb-6">
            <div class="flex items-start justify-between gap-4">
                <div class="flex-1">
                    <h3 class="font-serif text-espresso text-lg font-normal mb-3">Terjadi Kesalahan</h3>
                    <ul class="font-sans text-[0.75rem] text-espresso space-y-2">
                        @foreach ($errors->all() as $error)
                            <li class="flex items-start gap-2">
                                <span class="text-espresso mt-0.5">•</span>
                                <span>{{ $error }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
                <button onclick="this.closest('div').remove()" class="text-espresso/60 hover:text-espresso transition-colors flex-shrink-0">
                    <i class="fas fa-times text-sm"></i>
                </button>
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-5 gap-6">

        {{-- ══ KIRI: FORM PEMINJAMAN ══ --}}
        <div class="lg:col-span-2">
            <div class="bg-paper border border-rule">

                {{-- Form Header --}}
                <div class="px-6 py-5 border-b border-rule">
                    <p class="font-sans text-[0.52rem] font-semibold tracking-[0.3em] uppercase text-label mb-1">
                        Formulir
                    </p>
                    <h3 class="font-serif text-ink text-xl font-normal leading-none">
                        Data Peminjaman
                    </h3>
                </div>

                {{-- Form Body --}}
                <form action="{{ route('peminjaman.guest.store') }}" method="POST" id="peminjamanForm" class="px-6 py-6 space-y-6">
                    @csrf

                    {{-- Nama Lengkap --}}
                    <div class="relative">
                        <label class="block font-sans text-[0.55rem] font-semibold tracking-[0.28em] uppercase text-label mb-2.5">
                            Nama Lengkap <span class="text-espresso">*</span>
                        </label>
                        <input
                            type="text" 
                            name="nama_peminjam_guest" 
                            value="{{ old('nama_peminjam_guest') }}" 
                            required
                            placeholder="Masukkan nama lengkap"
                            class="peer w-full bg-transparent border-b border-rule pb-2.5 pt-1 font-sans text-[0.85rem] text-ink outline-none placeholder-ghost/60 transition-colors duration-200 focus:border-ink"
                        >
                        <span class="absolute bottom-0 left-0 h-px w-0 bg-ink transition-all duration-300 peer-focus:w-full"></span>
                        @error('nama_peminjam_guest')
                            <p class="font-sans text-[0.65rem] text-espresso mt-1.5">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Telepon --}}
                    <div class="relative">
                        <label class="block font-sans text-[0.55rem] font-semibold tracking-[0.28em] uppercase text-label mb-2.5">
                            Nomor Telepon <span class="text-espresso">*</span>
                        </label>
                        <input
                            type="tel" 
                            name="telepon_peminjam_guest" 
                            value="{{ old('telepon_peminjam_guest') }}" 
                            required
                            placeholder="+62 8xx xxx xxxx"
                            class="peer w-full bg-transparent border-b border-rule pb-2.5 pt-1 font-sans text-[0.85rem] text-ink outline-none placeholder-ghost/60 transition-colors duration-200 focus:border-ink"
                        >
                        <span class="absolute bottom-0 left-0 h-px w-0 bg-ink transition-all duration-300 peer-focus:w-full"></span>
                        @error('telepon_peminjam_guest')
                            <p class="font-sans text-[0.65rem] text-espresso mt-1.5">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Pilih Alat --}}
                    <div>
                        <label class="block font-sans text-[0.55rem] font-semibold tracking-[0.28em] uppercase text-label mb-2.5">
                            Alat <span class="text-espresso">*</span>
                        </label>
                        <div class="relative">
                            <select
                                name="alat_id" id="alat_select" required
                                class="w-full appearance-none bg-cream border border-rule px-3 py-2.5 font-sans text-[0.82rem] text-ink outline-none focus:border-ink transition-colors duration-200"
                            >
                                <option value="">Pilih Alat</option>
                                @foreach($alats as $alat)
                                    <option value="{{ $alat->alat_id }}"
                                        data-max="{{ $alat->stok_tersedia }}"
                                        {{ old('alat_id') == $alat->alat_id ? 'selected' : '' }}>
                                        {{ $alat->nama_alat }} (Tersedia: {{ $alat->stok_tersedia }})
                                    </option>
                                @endforeach
                            </select>
                            <i class="fas fa-chevron-down absolute right-3 top-1/2 -translate-y-1/2 text-ghost text-[0.55rem] pointer-events-none"></i>
                        </div>
                        @error('alat_id')
                            <p class="font-sans text-[0.65rem] text-espresso mt-1.5">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Jumlah --}}
                    <div class="relative">
                        <label class="block font-sans text-[0.55rem] font-semibold tracking-[0.28em] uppercase text-label mb-2.5">
                            Jumlah <span class="text-espresso">*</span>
                        </label>
                        <input
                            type="number" id="jumlah_input" name="jumlah" min="1" required value="{{ old('jumlah', 1) }}"
                            placeholder="Jumlah unit yang dipinjam"
                            class="peer w-full bg-transparent border-b border-rule pb-2.5 pt-1 font-sans text-[0.85rem] text-ink outline-none placeholder-ghost/60 transition-colors duration-200 focus:border-ink"
                        >
                        <span class="absolute bottom-0 left-0 h-px w-0 bg-ink transition-all duration-300 peer-focus:w-full"></span>
                        <p id="stok_info" class="font-sans text-[0.62rem] text-label mt-1.5"></p>
                        @error('jumlah')
                            <p class="font-sans text-[0.65rem] text-espresso mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Tanggal Peminjaman & Kembali --}}
                    <div class="grid grid-cols-2 gap-4">
                        <div class="relative">
                            <label class="block font-sans text-[0.55rem] font-semibold tracking-[0.28em] uppercase text-label mb-2.5">
                                Tgl. Pinjam <span class="text-espresso">*</span>
                            </label>
                            <input
                                type="date" name="tanggal_peminjaman" required value="{{ old('tanggal_peminjaman') }}"
                                min="{{ now()->format('Y-m-d') }}"
                                class="peer w-full bg-transparent border-b border-rule pb-2.5 pt-1 font-sans text-[0.82rem] text-ink outline-none transition-colors duration-200 focus:border-ink"
                            >
                            <span class="absolute bottom-0 left-0 h-px w-0 bg-ink transition-all duration-300 peer-focus:w-full"></span>
                            @error('tanggal_peminjaman')
                                <p class="font-sans text-[0.65rem] text-espresso mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="relative">
                            <label class="block font-sans text-[0.55rem] font-semibold tracking-[0.28em] uppercase text-label mb-2.5">
                                Tgl. Kembali <span class="text-espresso">*</span>
                            </label>
                            <input
                                type="date" name="tanggal_kembali_rencana" required value="{{ old('tanggal_kembali_rencana') }}"
                                class="peer w-full bg-transparent border-b border-rule pb-2.5 pt-1 font-sans text-[0.82rem] text-ink outline-none transition-colors duration-200 focus:border-ink"
                            >
                            <span class="absolute bottom-0 left-0 h-px w-0 bg-ink transition-all duration-300 peer-focus:w-full"></span>
                            @error('tanggal_kembali_rencana')
                                <p class="font-sans text-[0.65rem] text-espresso mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    {{-- Tujuan --}}
                    <div>
                        <label class="block font-sans text-[0.55rem] font-semibold tracking-[0.28em] uppercase text-label mb-2.5">
                            Tujuan Peminjaman
                        </label>
                        <textarea
                            name="tujuan_peminjaman" rows="3"
                            placeholder="Untuk keperluan..."
                            class="w-full bg-cream border border-rule px-3 py-2.5 font-sans text-[0.82rem] text-ink outline-none placeholder-ghost/60 focus:border-ink transition-colors duration-200"
                        >{{ old('tujuan_peminjaman') }}</textarea>
                    </div>

                    {{-- Submit --}}
                    <button
                        type="submit"
                        class="relative w-full overflow-hidden bg-espresso px-6 py-3.5
                               font-sans text-[0.6rem] font-semibold tracking-[0.25em] uppercase text-paper
                               flex items-center justify-center gap-2
                               transition-colors duration-200 hover:bg-ink active:scale-[0.99]
                               after:content-[''] after:absolute after:inset-0 after:bg-white/[0.06]
                               after:-translate-x-full after:transition-transform after:duration-300
                               hover:after:translate-x-0"
                    >
                        <i class="fas fa-paper-plane text-xs"></i>
                        <span>Ajukan Peminjaman</span>
                    </button>

                </form>
            </div>
        </div>

        {{-- ══ KANAN: INFO BOX ══ --}}
        <div class="lg:col-span-3">
            <div class="space-y-6">
                {{-- Info Card 1 --}}
                <div class="bg-paper border border-rule p-6">
                    <div class="flex items-start gap-3 mb-4">
                        <div class="w-8 h-8 bg-espresso text-paper flex items-center justify-center flex-shrink-0 rounded-full">
                            <i class="fas fa-info text-xs"></i>
                        </div>
                        <div>
                            <h4 class="font-serif text-ink text-lg font-normal">Informasi Penting</h4>
                            <p class="font-sans text-[0.65rem] text-label mt-0.5">Sebelum Mengajukan Peminjaman</p>
                        </div>
                    </div>
                    <ul class="font-sans text-[0.75rem] text-label space-y-3 ml-11">
                        <li class="flex items-start gap-2">
                            <span class="text-espresso font-semibold mt-0.5">✓</span>
                            <span>Data Anda akan diverifikasi oleh admin sebelum disetujui</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="text-espresso font-semibold mt-0.5">✓</span>
                            <span>Anda akan menerima kode peminjaman unik untuk tracking</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="text-espresso font-semibold mt-0.5">✓</span>
                            <span>Admin akan menghubungi melalui nomor telepon yang Anda berikan</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="text-espresso font-semibold mt-0.5">✓</span>
                            <span>Perhatikan tanggal kembali untuk menghindari denda keterlambatan</span>
                        </li>
                    </ul>
                </div>

                {{-- Timeline Card --}}
                <div class="bg-paper border border-rule p-6">
                    <h4 class="font-serif text-ink text-lg font-normal mb-4">Alur Proses Peminjaman</h4>
                    <div class="space-y-4">
                        <div class="flex gap-3">
                            <div class="flex flex-col items-center">
                                <div class="w-8 h-8 bg-cream border-2 border-espresso text-espresso flex items-center justify-center font-sans font-bold text-[0.65rem]">1</div>
                                <div class="w-0.5 h-8 bg-rule"></div>
                            </div>
                            <div class="pb-4">
                                <p class="font-sans text-[0.7rem] font-semibold text-ink">Isi Formulir</p>
                                <p class="font-sans text-[0.65rem] text-label mt-1">Lengkapi semua data dengan benar</p>
                            </div>
                        </div>
                        <div class="flex gap-3">
                            <div class="flex flex-col items-center">
                                <div class="w-8 h-8 bg-cream border-2 border-espresso text-espresso flex items-center justify-center font-sans font-bold text-[0.65rem]">2</div>
                                <div class="w-0.5 h-8 bg-rule"></div>
                            </div>
                            <div class="pb-4">
                                <p class="font-sans text-[0.7rem] font-semibold text-ink">Terima Kode Peminjaman</p>
                                <p class="font-sans text-[0.65rem] text-label mt-1">Dapatkan kode unik untuk tracking</p>
                            </div>
                        </div>
                        <div class="flex gap-3">
                            <div class="flex flex-col items-center">
                                <div class="w-8 h-8 bg-cream border-2 border-espresso text-espresso flex items-center justify-center font-sans font-bold text-[0.65rem]">3</div>
                                <div class="w-0.5 h-8 bg-rule"></div>
                            </div>
                            <div class="pb-4">
                                <p class="font-sans text-[0.7rem] font-semibold text-ink">Verifikasi Admin</p>
                                <p class="font-sans text-[0.65rem] text-label mt-1">Admin akan menghubungi dalam 1-2 hari</p>
                            </div>
                        </div>
                        <div class="flex gap-3">
                            <div class="flex flex-col items-center">
                                <div class="w-8 h-8 bg-cream border-2 border-espresso text-espresso flex items-center justify-center font-sans font-bold text-[0.65rem]">4</div>
                            </div>
                            <div>
                                <p class="font-sans text-[0.7rem] font-semibold text-ink">Peminjaman Disetujui</p>
                                <p class="font-sans text-[0.65rem] text-label mt-1">Alat siap diambil sesuai jadwal</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Tip Card --}}
                <div class="bg-cream border border-rule p-6">
                    <div class="flex items-start gap-3">
                        <div class="text-espresso text-xl flex-shrink-0">💡</div>
                        <div>
                            <h4 class="font-serif text-ink text-base font-normal mb-2">Tips & Trik</h4>
                            <p class="font-sans text-[0.75rem] text-label leading-relaxed">
                                Gunakan email dan nomor telepon yang aktif agar admin dapat menghubungi Anda dengan mudah. Simpan kode peminjaman Anda untuk referensi di masa depan.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <script>
        document.getElementById('alat_select').addEventListener('change', function() {
            const selected = this.options[this.selectedIndex];
            const maxStok = selected.getAttribute('data-max');
            const jumlahInput = document.getElementById('jumlah_input');
            const stokInfo = document.getElementById('stok_info');

            if (maxStok) {
                jumlahInput.max = maxStok;
                stokInfo.textContent = 'Maksimal: ' + maxStok + ' unit tersedia';
            } else {
                jumlahInput.max = '';
                stokInfo.textContent = '';
            }
        });

        // Validasi tanggal kembali
        const tanggalPeminjamanInput = document.querySelector('input[name="tanggal_peminjaman"]');
        const tanggalKembaliInput = document.querySelector('input[name="tanggal_kembali_rencana"]');

        tanggalPeminjamanInput.addEventListener('change', function() {
            tanggalKembaliInput.min = this.value;
        });
    </script>

@endsection