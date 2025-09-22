{{-- File: resources/views/filament/widgets/simple-recent-activity.blade.php --}}

<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            Aktivitas Terbaru
        </x-slot>

        <div class="space-y-6">
            {{-- Peminjaman Terbaru --}}
            <div>
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-3">
                    Peminjaman Terbaru
                </h3>
                <div class="space-y-2">
                    @forelse($peminjaman as $item)
                        <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-800 rounded-lg">
                            <div class="flex-1">
                                <div class="flex items-center space-x-2">
                                    <span class="text-sm font-medium text-gray-900 dark:text-white">
                                        @if ($isAdmin && $item->user)
                                            {{ trim(($item->user->pangkat ?? '') . ' ' . ($item->user->name ?? '')) }}
                                        @else
                                            Peminjaman Alat
                                        @endif
                                    </span>
                                    <span
                                        class="px-2 py-1 text-xs rounded-full
                                        @if ($item->approval === 'pending') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200
                                        @elseif($item->approval === 'approved') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                                        @else bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200 @endif">
                                        {{ ucfirst($item->approval) }}
                                    </span>
                                </div>
                                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                    {{ $item->equipment_names ?? 'Beberapa alat' }} •
                                    {{ $item->tanggal_pinjam ? \Carbon\Carbon::parse($item->tanggal_pinjam)->format('d M Y') : '-' }}
                                </p>
                            </div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">
                                {{ $item->created_at->diffForHumans() }}
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500 dark:text-gray-400">Belum ada peminjaman.</p>
                    @endforelse
                </div>
            </div>

            {{-- Pengembalian Terbaru --}}
            <div>
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-3">
                    Pengembalian Terbaru
                </h3>
                <div class="space-y-2">
                    @forelse($pengembalian as $item)
                        <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-800 rounded-lg">
                            <div class="flex-1">
                                <div class="flex items-center space-x-2">
                                    <span class="text-sm font-medium text-gray-900 dark:text-white">
                                        @if ($isAdmin && $item->user)
                                            {{ trim(($item->user->pangkat ?? '') . ' ' . ($item->user->name ?? '')) }}
                                        @else
                                            Pengembalian Alat
                                        @endif
                                    </span>
                                    <span
                                        class="px-2 py-1 text-xs rounded-full
                                        @if ($item->approval === 'pending') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200
                                        @elseif($item->approval === 'approved') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                                        @else bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200 @endif">
                                        {{ ucfirst($item->approval) }}
                                    </span>
                                </div>
                                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                    {{ $item->peminjamanItem?->peralatan?->name ?? 'Alat' }}
                                    ({{ $item->jumlah_dikembalikan }} unit)
                                    • {{ $item->tanggal_kembali?->format('d M Y') }}
                                </p>
                            </div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">
                                {{ $item->created_at->diffForHumans() }}
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500 dark:text-gray-400">Belum ada pengembalian.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
