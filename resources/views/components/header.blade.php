<header class="fixed top-0 left-0 right-0 z-40 bg-espresso border-b border-white/[0.07]">

    {{-- Subtle top accent line --}}
    <div class="h-[1px] w-full bg-gradient-to-r from-transparent via-rule/30 to-transparent"></div>

    <div class="mx-auto px-6 lg:px-8 flex items-stretch h-[70px]">

        {{-- ── BRAND ── --}}
        <div class="flex items-center gap-4 flex-shrink-0">
            <div class="w-7 h-7 border border-white/20 flex items-center justify-center flex-shrink-0">
                <i class="fas fa-wrench text-paper text-[0.55rem]"></i>
            </div>
            <div>
                <h1 class="font-serif text-paper text-[1rem] font-normal leading-none tracking-[0.15em] uppercase">
                    Sistem Peminjaman
                </h1>
                <p class="font-sans text-[0.43rem] tracking-[0.38em] uppercase text-paper/35 mt-[3px]">
                    Platform Manajemen
                </p>
            </div>
        </div>

        {{-- ── SPACER ── --}}
        <div class="flex-1"></div>

        {{-- ── RIGHT SECTION ── --}}
        <div class="flex items-stretch gap-0">

            @php
                $level = auth()->user()->level ?? 'admin';
                $icon  = match(strtolower($level)) {
                    'admin'    => 'user-shield',
                    'petugas'  => 'user-cog',
                    'peminjam' => 'user',
                    default    => 'user',
                };
                $levelBadge = match(strtolower($level)) {
                    'admin'    => 'Admin',
                    'petugas'  => 'Petugas',
                    'peminjam' => 'Peminjam',
                    default    => ucfirst($level),
                };
            @endphp

            {{-- ── USER IDENTITY ── --}}
            <div class="flex items-center gap-3.5 px-6 border-l border-white/[0.08]">
                <div class="relative flex-shrink-0">
                    <div class="w-8 h-8 bg-white/[0.08] border border-white/15 flex items-center justify-center">
                        <i class="fas fa-{{ $icon }} text-paper/70 text-[0.58rem]"></i>
                    </div>
                    <span class="absolute -bottom-0.5 -right-0.5 w-[7px] h-[7px] bg-emerald-400 border border-espresso rounded-full"></span>
                </div>
                <div class="hidden sm:block">
                    <p class="font-sans text-[0.75rem] font-semibold text-paper leading-none">
                        {{ auth()->user()->username ?? 'Pengguna' }}
                    </p>
                    <div class="flex items-center gap-1.5 mt-[4px]">
                        <span class="inline-block w-1 h-1 rounded-full
                            {{ strtolower($level) === 'admin'   ? 'bg-amber-400'  :
                               (strtolower($level) === 'petugas' ? 'bg-sky-400' : 'bg-emerald-400') }}">
                        </span>
                        <p class="font-sans text-[0.48rem] tracking-[0.22em] uppercase text-paper/40 leading-none">
                            {{ $levelBadge }}
                        </p>
                    </div>
                </div>
            </div>

            {{-- ── DIVIDER ── --}}
            <div class="w-px bg-white/[0.08] my-3.5 ml-4"></div>

            {{-- ── LOGOUT BUTTON ── --}}
            <div class="flex items-center pl-4">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button
                        type="submit"
                        class="flex items-center gap-2.5 border border-white/20 px-4 py-2
                               font-sans text-[0.58rem] font-semibold tracking-[0.22em] uppercase text-paper/70
                               hover:bg-white/[0.08] hover:text-paper hover:border-white/35
                               active:scale-[0.98] transition-all duration-150 group"
                    >
                        <i class="fas fa-right-from-bracket text-[0.6rem] group-hover:translate-x-0.5 transition-transform duration-200"></i>
                        <span>Keluar</span>
                    </button>
                </form>
            </div>

        </div>

    </div>

</header>