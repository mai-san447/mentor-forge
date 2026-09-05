<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>@include('partials.head')</head>
<body class="min-h-screen bg-zinc-50">
<flux:sidebar sticky collapsible="mobile" class="border-e border-zinc-200 bg-white">
    <flux:sidebar.header><x-app-logo :sidebar="true" href="{{ route('dashboard') }}" wire:navigate /><flux:sidebar.collapse class="lg:hidden" /></flux:sidebar.header>
    <flux:sidebar.nav>
        <flux:sidebar.group :heading="__('Mentor Forge')" class="grid">
            <flux:sidebar.item icon="home" :href="route('dashboard')" :current="request()->routeIs('dashboard')" wire:navigate>ホーム</flux:sidebar.item>
            <flux:sidebar.item icon="book-open-text" :href="route('cases.index')" :current="request()->routeIs('cases.*')" wire:navigate>ケースドリル</flux:sidebar.item>
            <flux:sidebar.item icon="headphones" :href="route('solo.index')" :current="request()->routeIs('solo.*')" wire:navigate>ソロ練習</flux:sidebar.item>
            <flux:sidebar.item icon="users" :href="route('trio.index')" :current="request()->routeIs('trio.*')" wire:navigate>トリオ練習</flux:sidebar.item>
            <flux:sidebar.item icon="academic-cap" :href="route('quiz.index')" :current="request()->routeIs('quiz.*')" wire:navigate>クイズ</flux:sidebar.item>
        </flux:sidebar.group>
    </flux:sidebar.nav>
    <flux:spacer />
    <x-desktop-user-menu class="hidden lg:block" :name="auth()->user()->name" />
</flux:sidebar>
<flux:header class="lg:hidden"><flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" /><flux:spacer /></flux:header>
{{ $slot }}
@persist('toast')<flux:toast.group><flux:toast /></flux:toast.group>@endpersist
@fluxScripts
</body>
</html>
