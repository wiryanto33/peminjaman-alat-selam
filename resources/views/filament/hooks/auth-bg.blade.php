@php
    // tampilkan hanya di halaman AUTH panel "admin"
    $isAuth = request()->routeIs('filament.admin.auth.*');
@endphp

@if ($isAuth)
    <div class="fixed inset-0 -z-10">
        <img src="{{ asset('images/selam.jpg') }}" alt="" class="w-full h-full object-cover">
        <div class="absolute inset-0 bg-black/50"></div> {{-- overlay gelap agar kontras --}}
    </div>
@endif
