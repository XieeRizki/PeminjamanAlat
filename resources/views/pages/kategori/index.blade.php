@extends('layouts.app')

@section('title', 'Kategori Alat')

@section('content')

    {{-- ══ PAGE HEADER ══ --}}
    <div class="flex items-end justify-between mb-8">
        <div>
            <p class="font-sans text-[0.58rem] font-semibold tracking-[0.35em] uppercase text-label mb-1">
                Administrasi
            </p>
            <h2 class="font-serif text-ink text-3xl font-normal leading-none">
                Kategori Alat
            </h2>
            <div class="mt-3 h-px w-10 bg-rule"></div>
        </div>

        <button
            onclick="openModal()"
            class="relative overflow-hidden flex items-center gap-2 bg-espresso px-5 py-3
                   font-sans text-[0.62rem] font-semibold tracking-[0.2em] uppercase text-paper
                   transition-colors duration-200 hover:bg-ink active:scale-[0.99]
                   after:content-[''] after:absolute after:inset-0 after:bg-white/[0.06]
                   after:-translate-x-full after:transition-transform after:duration-300
                   hover:after:translate-x-0"
        >
            <i class="fas fa-plus text-xs"></i>
            <span>Tambah Kategori</span>
        </button>
    </div>

    {{-- ══ SUCCESS ALERT ══ --}}
    @if(session('success'))
        <div class="flex items-center justify-between border-l-2 border-espresso bg-cream px-4 py-3 mb-6">
            <span class="font-sans text-[0.75rem] tracking-wide text-ink">{{ session('success') }}</span>
            <button onclick="this.parentElement.remove()" class="text-label hover:text-ink transition-colors ml-4">
                <i class="fas fa-times text-xs"></i>
            </button>
        </div>
    @endif

    {{-- ══ CARD GRID ══ --}}
    @if(count($kategori) > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($kategori as $item)
                <div class="bg-paper border border-rule p-6 relative overflow-hidden hover:border-dim transition-colors duration-200 group">

                    {{-- Decorative corner --}}
                    <div class="pointer-events-none absolute top-3 right-3 h-6 w-6 border-t border-r border-rule group-hover:border-dim transition-colors duration-200"></div>

                    {{-- Icon --}}
                    <div class="w-9 h-9 bg-espresso flex items-center justify-center mb-4">
                        <i class="fas fa-folder text-paper text-[0.65rem]"></i>
                    </div>

                    {{-- Content --}}
                    <h3 class="font-serif text-ink text-xl font-normal leading-snug mb-2">
                        {{ $item->nama_kategori }}
                    </h3>
                    <div class="h-px w-6 bg-rule mb-3"></div>
                    <p class="font-sans text-[0.75rem] leading-relaxed tracking-wide text-label mb-5">
                        {{ $item->deskripsi }}
                    </p>

                    {{-- Actions --}}
                    <div class="flex items-center gap-2 pt-4 border-t border-rule">
                        <button
                            onclick="editKategori({{ $item->kategori_id }}, '{{ addslashes($item->nama_kategori) }}', '{{ addslashes($item->deskripsi) }}')"
                            class="flex items-center gap-1.5 border border-rule px-3 py-1.5
                                   font-sans text-[0.58rem] font-semibold tracking-[0.15em] uppercase text-label
                                   hover:border-espresso hover:text-espresso transition-all duration-150"
                        >
                            <i class="fas fa-edit text-[0.6rem]"></i>
                            <span>Edit</span>
                        </button>

                        <form action="{{ route('kategori.destroy', $item->kategori_id) }}" method="POST"
                              class="inline" onsubmit="return confirm('Yakin ingin menghapus kategori ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                class="w-7 h-7 flex items-center justify-center border border-rule text-ghost
                                       hover:border-espresso hover:text-espresso transition-all duration-150">
                                <i class="fas fa-trash text-[0.6rem]"></i>
                            </button>
                        </form>
                    </div>

                </div>
            @endforeach
        </div>

    @else
        {{-- Empty State --}}
        <div class="bg-paper border border-rule p-16 text-center relative overflow-hidden">
            <div class="pointer-events-none absolute top-5 right-5 h-9 w-9 border-t border-r border-rule"></div>
            <div class="pointer-events-none absolute bottom-5 left-5 h-9 w-9 border-b border-l border-rule"></div>

            <div class="w-12 h-12 bg-cream border border-rule flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-folder-open text-ghost text-base"></i>
            </div>
            <p class="font-serif text-ink text-lg font-normal mb-1">Belum ada kategori</p>
            <p class="font-sans text-[0.7rem] text-label tracking-wide">
                Klik tombol "Tambah Kategori" untuk menambahkan kategori baru.
            </p>
        </div>
    @endif

    {{-- ══ MODAL TAMBAH / EDIT KATEGORI ══ --}}
    <div id="kategoriModal" class="hidden fixed inset-0 z-50 flex items-center justify-center px-4 py-8"
         style="background:rgba(26,23,20,0.55)">
        <div class="relative w-full max-w-sm bg-paper border border-rule shadow-2xl flex flex-col animate-fade-up">

            {{-- Modal Header --}}
            <div class="flex items-end justify-between px-8 pt-7 pb-5 border-b border-rule">
                <div>
                    <p class="font-sans text-[0.5rem] font-semibold tracking-[0.35em] uppercase text-label mb-1">
                        Formulir
                    </p>
                    <h3 id="modalTitle" class="font-serif text-ink text-2xl font-normal leading-none">
                        Tambah Kategori
                    </h3>
                </div>
                <button onclick="closeModal()"
                    class="w-7 h-7 flex items-center justify-center border border-rule text-ghost
                           hover:border-espresso hover:text-ink transition-all duration-150 mb-0.5">
                    <i class="fas fa-times text-[0.6rem]"></i>
                </button>
            </div>

            {{-- Modal Body --}}
            <form id="kategoriForm" method="POST" action="{{ route('kategori.store') }}" class="px-8 py-6 space-y-6">
                @csrf
                <input type="hidden" id="methodField" name="_method" value="POST">

                {{-- Nama Kategori --}}
                <div class="relative">
                    <label class="block font-sans text-[0.55rem] font-semibold tracking-[0.28em] uppercase text-label mb-2.5">
                        Nama Kategori <span class="text-espresso">*</span>
                    </label>
                    <input
                        type="text" id="nama_kategori" name="nama_kategori" required
                        placeholder="Contoh: Elektronik"
                        class="peer w-full bg-transparent border-b border-rule pb-2.5 pt-1 font-sans text-[0.85rem] text-ink outline-none placeholder-ghost/60 transition-colors duration-200 focus:border-ink"
                    >
                    <span class="absolute bottom-0 left-0 h-px w-0 bg-ink transition-all duration-300 peer-focus:w-full"></span>
                </div>

                {{-- Deskripsi --}}
                <div>
                    <label class="block font-sans text-[0.55rem] font-semibold tracking-[0.28em] uppercase text-label mb-2.5">
                        Deskripsi <span class="text-espresso">*</span>
                    </label>
                    <textarea
                        id="deskripsi" name="deskripsi" rows="4" required
                        placeholder="Deskripsi kategori"
                        class="w-full bg-cream border border-rule px-3 py-2.5 font-sans text-[0.82rem] text-ink outline-none placeholder-ghost/60 focus:border-ink transition-colors duration-200 resize-none"
                    ></textarea>
                </div>

                {{-- Footer Buttons --}}
                <div class="flex gap-3 pt-2 border-t border-rule">
                    <button type="submit"
                        class="flex-1 bg-espresso text-paper font-sans text-[0.6rem] font-semibold tracking-[0.25em] uppercase py-3.5 hover:bg-ink transition-colors duration-200">
                        Simpan
                    </button>
                    <button type="button" onclick="closeModal()"
                        class="flex-1 border border-rule text-label font-sans text-[0.6rem] font-semibold tracking-[0.25em] uppercase py-3.5 hover:border-espresso hover:text-espresso transition-all duration-200">
                        Batal
                    </button>
                </div>
            </form>

        </div>
    </div>

    <script>
        function openModal() {
            document.getElementById('kategoriModal').classList.remove('hidden');
            document.getElementById('modalTitle').textContent = 'Tambah Kategori';
            document.getElementById('kategoriForm').action = '{{ route("kategori.store") }}';
            document.getElementById('methodField').value = 'POST';
            document.getElementById('nama_kategori').value = '';
            document.getElementById('deskripsi').value = '';
        }

        function closeModal() {
            document.getElementById('kategoriModal').classList.add('hidden');
        }

        function editKategori(id, nama, deskripsi) {
            document.getElementById('kategoriModal').classList.remove('hidden');
            document.getElementById('modalTitle').textContent = 'Edit Kategori';
            document.getElementById('kategoriForm').action = '/kategori/' + id;
            document.getElementById('methodField').value = 'PUT';
            document.getElementById('nama_kategori').value = nama;
            document.getElementById('deskripsi').value = deskripsi;
        }

        window.onclick = function(event) {
            const modal = document.getElementById('kategoriModal');
            if (event.target == modal) closeModal();
        }
    </script>

@endsection