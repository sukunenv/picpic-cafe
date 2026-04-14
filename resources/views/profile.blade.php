<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil - PicPic Cafe</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body className="bg-[#F8F7FF] min-h-screen pb-24" x-data="{ loaded: false }" x-init="setTimeout(() => loaded = true, 100)">

    <!-- SECTION 1: HEADER -->
    <header class="bg-gradient-to-br from-[#6367FF] to-[#8494FF] pt-14 pb-32 px-6 relative overflow-hidden transition-opacity duration-1000" :class="loaded ? 'opacity-100' : 'opacity-0'">
        <!-- Grid Pattern Overlay -->
        <div class="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNjAiIGhlaWdodD0iNjAiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PGRlZnM+PHBhdHRlcm4gaWQ9ImdyaWQiIHdpZHRoPSI2MCIgaGVpZ2h0PSI2MCIgcGF0dGVyblVuaXRzPSJ1c2VyU3BhY2VPblVzZSI+PHBhdGggZD0iTSAxMCAwIEwgMCAwIDAgMTAiIGZpbGw9Im5vbmUiIHN0cm9rZT0id2hpdGUiIHN0cm9rZS13aWR0aD0iMC41IiBvcGFjaXR5PSIwLjEiLz48L3BhdHRlcm4+PC9kZWZzPjxyZWN0IHdpZHRoPSIxMDAlIiBoZWlnaHQ9IjEwMCUiIGZpbGw9InVybCgjZ3JpZCkiLz48L3N2Zz4=')] opacity-20"></div>

        <div class="relative z-10 max-w-md mx-auto">
            <h1 class="text-white font-bold text-2xl mb-6">Profil</h1>
            
            <!-- User Info -->
            <div class="flex items-center gap-4 mb-8">
                <div class="w-16 h-16 bg-white/20 backdrop-blur-sm rounded-full flex items-center justify-center border-2 border-white/30 overflow-hidden shadow-lg">
                    <!-- Icon placeholder -->
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                </div>
                <div>
                    <h2 class="text-white font-bold text-xl leading-tight">Budi PicPic</h2>
                    <p class="text-white/80 text-sm">budi.san@email.com</p>
                </div>
            </div>

            <!-- x-member-card Component -->
            <x-member-card tier="Silver" :points="450" memberId="8821" since="MARET 2024" />

            <!-- SECTION 3: PROGRESS BAR -->
            <div class="bg-white rounded-[24px] p-5 mt-5 shadow-xl shadow-black/5" x-data="{ width: 0 }" x-init="setTimeout(() => width = 55, 500)">
                <div class="flex justify-between items-center mb-3 px-1">
                    <span class="text-[#2D2B55] font-bold text-sm">Progress ke Gold</span>
                    <span class="text-[#6367FF] font-extrabold text-sm uppercase tracking-tighter">550 pts lagi</span>
                </div>
                <div class="h-2.5 bg-[#F8F7FF] rounded-full overflow-hidden border border-gray-100 p-[2px]">
                    <div class="h-full bg-gradient-to-r from-[#6367FF] to-[#8494FF] rounded-full transition-all duration-1000 ease-out" :style="'width: ' + width + '%'"></div>
                </div>
                <div class="flex justify-between mt-2.5 px-1 text-[9px] font-black text-gray-400 uppercase tracking-widest">
                    <span>Silver</span>
                    <span>Gold</span>
                </div>
            </div>
        </div>
    </header>

    <!-- MAIN CONTENT (OVERLAP SECTION) -->
    <main class="max-w-md mx-auto px-6 -mt-20 relative z-30 space-y-8">
        
        <!-- SECTION 4: RIWAYAT PESANAN -->
        <section class="space-y-4">
            <div class="flex items-center gap-2 mb-2 px-1">
                <svg class="w-5 h-5 text-[#2D2B55]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <h2 class="text-[#2D2B55] font-bold text-lg">Riwayat Pesanan</h2>
            </div>

            @foreach([['id' => 'PC-8821', 'date' => '12 Apr 2024', 'items' => 3, 'price' => 75000], ['id' => 'PC-8819', 'date' => '10 Apr 2024', 'items' => 1, 'price' => 25000]] as $index => $order)
                <div class="bg-white rounded-2xl p-4 flex gap-3 shadow-md border border-gray-100 transition-all duration-700 transform" 
                     :class="loaded ? 'translate-x-0 opacity-100' : '-translate-x-5 opacity-0'"
                     style="transition-delay: {{ 0.3 + ($index * 0.1) }}s">
                    <div class="w-16 h-16 bg-[#F8F7FF] rounded-xl flex items-center justify-center p-2 flex-shrink-0">
                        <img src="https://res.cloudinary.com/dkcl8wzdc/image/upload/q_auto/f_auto/v1776032977/logo_apuccy.png" alt="Pic" class="w-10 h-10 object-contain opacity-20">
                    </div>
                    <div class="flex-1">
                        <div class="flex items-start justify-between mb-1">
                            <div>
                                <h3 class="text-[#2D2B55] font-semibold text-sm">{{ $order['id'] }}</h3>
                                <p class="text-xs text-[#2D2B55]/60">{{ $order['date'] }}</p>
                            </div>
                            <span class="text-[#6367FF] bg-[#6367FF]/10 px-2 py-1 rounded-full text-[10px] font-bold uppercase">Selesai</span>
                        </div>
                        <div class="flex items-center justify-between mt-2">
                             <span class="text-xs text-[#2D2B55]/60">{{ $order['items'] }} Item</span>
                             <span class="text-[#2D2B55] font-bold text-sm">Rp {{ number_format($order['price']) }}</span>
                        </div>
                    </div>
                </div>
            @endforeach
        </section>

        <!-- SECTION 5: LAINNYA -->
        <section class="space-y-4">
            <h2 class="text-[#2D2B55] font-bold text-lg px-1">Lainnya</h2>
            <div class="space-y-2">
                @foreach([['icon' => '⭐', 'label' => 'Tier Member Cards'], ['icon' => '🌐', 'label' => 'kedaipicpic.com'], ['icon' => 'ℹ️', 'label' => 'Tentang Aplikasi']] as $index => $menu)
                    <div class="flex items-center gap-3 px-4 py-4 bg-white rounded-xl shadow-sm border border-gray-100 transition-all duration-700"
                         :class="loaded ? 'opacity-100' : 'opacity-0'"
                         style="transition-delay: {{ 0.7 + ($index * 0.1) }}s">
                        <div class="w-10 h-10 bg-[#F8F7FF] rounded-lg flex items-center justify-center text-lg">
                            {{ $menu['icon'] }}
                        </div>
                        <span class="text-[#2D2B55] font-semibold flex-1 text-sm">{{ $menu['label'] }}</span>
                        <svg class="w-5 h-5 text-[#2D2B55]/40" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                    </div>
                @endforeach
            </div>
        </section>

        <!-- SECTION 6: FOOTER + LOGOUT -->
        <footer class="pt-8 pb-12 text-center" :class="loaded ? 'opacity-100' : 'opacity-0'" style="transition: opacity 1s; transition-delay: 1.1s">
            <button class="w-full flex items-center justify-center gap-2 border-2 border-red-500/10 text-red-500 py-3.5 rounded-full font-bold mb-8 active:scale-95 transition-all">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                Logout Pesanan
            </button>

            <p class="text-[10px] text-[#2D2B55]/40 font-bold uppercase tracking-widest">Powered by Kalify.dev</p>
            <p class="text-[10px] text-[#2D2B55]/30 font-medium mt-1">v1.0.0 (PicPic Stable Build)</p>
        </footer>

    </main>

    <!-- Navigation Placeholder (Bottom Nav) -->
    <div class="fixed bottom-0 inset-x-0 h-20 bg-white/80 backdrop-blur-xl border-t border-gray-100 z-50"></div>

</body>
</html>
