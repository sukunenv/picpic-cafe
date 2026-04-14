@props([
    'tier' => 'Bronze',
    'points' => 0,
    'memberId' => '0000',
    'since' => 'April 2024'
])

@php
    $config = [
        'Bronze' => [
            'bg' => 'bg-gradient-to-br from-[#8B4513] via-[#CD7F32] to-[#A0522D]',
            'badge' => '🥉 Bronze',
            'shadow' => 'shadow-[#CD7F32]/30'
        ],
        'Silver' => [
            'bg' => 'bg-gradient-to-br from-[#2D2B55] via-[#4A4E69] to-[#22223B]',
            'badge' => '⭐ Silver',
            'shadow' => 'shadow-gray-500/30'
        ],
        'Gold' => [
            'bg' => 'bg-gradient-to-br from-[#B8860B] via-[#FFD700] to-[#DAA520]',
            'badge' => '👑 Gold',
            'shadow' => 'shadow-yellow-500/30'
        ],
    ];

    $tierCfg = $config[$tier] ?? $config['Bronze'];
@endphp

<div 
    x-data="{ 
        tiltX: 0, 
        tiltY: 0,
        handleMove(e) {
            const card = this.$el;
            const rect = card.getBoundingClientRect();
            const x = e.clientX - rect.left;
            const y = e.clientY - rect.top;
            const centerX = rect.width / 2;
            const centerY = rect.height / 2;
            this.tiltX = (y - centerY) / 10;
            this.tiltY = (centerX - x) / 10;
        },
        handleLeave() {
            this.tiltX = 0;
            this.tiltY = 0;
        }
    }"
    @mousemove="handleMove"
    @mouseleave="handleLeave"
    :style="`transform: perspective(1000px) rotateX(${tiltX}deg) rotateY(${tiltY}deg); transition: transform 0.1s ease;`"
    class="relative w-full h-52 {{ $tierCfg['bg'] }} {{ $tierCfg['shadow'] }} rounded-[32px] p-6 text-white overflow-hidden shadow-2xl cursor-pointer select-none z-20"
>
    <!-- Shimmer Effect -->
    <div class="absolute inset-0 pointer-events-none overflow-hidden">
        <div class="shimmer-sweep absolute inset-0 w-full h-full bg-gradient-to-r from-transparent via-white/20 to-transparent skew-x-[-20deg]"></div>
    </div>

    <!-- Interior Texture -->
    <div class="absolute inset-0 opacity-[0.05] pointer-events-none bg-[url('https://www.transparenttextures.com/patterns/carbon-fibre.png')]"></div>

    <div class="relative z-10 h-full flex flex-col justify-between">
        <!-- Top Row -->
        <div class="flex justify-between items-start">
            <div class="flex flex-col">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 bg-white rounded-lg flex items-center justify-center shadow-sm">
                        <img src="https://res.cloudinary.com/dkcl8wzdc/image/upload/q_auto/f_auto/v1776032977/logo_apuccy.png" class="w-6 h-6 object-contain" alt="Logo">
                    </div>
                    <span class="font-black tracking-tighter text-lg">PICPIC</span>
                </div>
                <span class="text-[10px] font-bold uppercase tracking-[0.3em] opacity-60 mt-1">Member Card</span>
            </div>
            <div class="px-3 py-1 bg-white/20 backdrop-blur-md rounded-full text-[10px] font-black uppercase tracking-widest border border-white/10">
                {{ $tierCfg['badge'] }}
            </div>
        </div>

        <!-- Middle Row: Points -->
        <div class="flex items-baseline gap-2">
            <div class="flex flex-col">
                <span class="text-[9px] font-black uppercase tracking-widest opacity-40 leading-none mb-1">Total Points</span>
                <div class="flex items-baseline gap-1">
                    <span class="text-4xl font-black tracking-tighter">{{ number_format($points) }}</span>
                    <span class="text-xs font-bold opacity-60">pts</span>
                </div>
            </div>
        </div>

        <!-- Bottom Row -->
        <div class="flex justify-between items-end">
            <div class="flex flex-col gap-0.5">
                <span class="text-[8px] font-bold uppercase opacity-30 tracking-widest">Card Number</span>
                <span class="font-mono text-[10px] tracking-[0.3em] font-bold opacity-80">
                    4421 • 7702 • {{ $memberId }} • 9012
                </span>
            </div>
            <div class="flex flex-col items-end gap-0.5">
                <span class="text-[8px] font-bold uppercase opacity-30 tracking-widest">Member Since</span>
                <span class="text-[10px] font-bold opacity-80 uppercase">{{ $since }}</span>
            </div>
        </div>
    </div>

    <style>
        @keyframes shimmer {
            0% { transform: translateX(-150%) skewX(-20deg); }
            30% { transform: translateX(150%) skewX(-20deg); }
            100% { transform: translateX(150%) skewX(-20deg); }
        }
        .shimmer-sweep {
            animation: shimmer 4s infinite linear;
        }
    </style>
</div>
