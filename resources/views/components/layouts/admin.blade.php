{{-- resources/views/components/layouts/admin.blade.php --}}
@props(['header' => null])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<meta name="csrf-token" content="{{ csrf_token() }}">

		<title>Admin | Biblioteca</title>
		<link rel="icon" type="image/png" sizes="64x64" href="{{ asset('favicon.png') }}">

		<link rel="preconnect" href="https://fonts.bunny.net">
		<link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

		@vite(['resources/css/app.css', 'resources/js/app.js'])
		@livewireStyles
	</head>
	<body class="font-sans antialiased h-screen overflow-hidden bg-gray-100 text-gray-900">
		<x-banner />

		<div x-data="{ sidebarOpen: false }" class="h-screen">
			<div x-show="sidebarOpen" x-transition.opacity class="fixed inset-0 z-30 bg-gray-950/40 lg:hidden" @click="sidebarOpen = false"></div>

			<aside class="fixed inset-y-0 left-0 z-10 bg-gray-900 text-gray-100 border-r border-gray-800/70 transform transition-transform duration-200 lg:translate-x-0 flex flex-col w-72 pt-20">
			<div class="flex flex-col h-full">
					@php
						$menuItems = [
							[
								'label' => 'Dashboard',
								'href' => route('admin.dashboard'),
								'active' => request()->routeIs('admin.dashboard'),
							],
							[
								'label' => 'Requisições',
								'href' => route('admin.requisicoes'),
								'active' => request()->routeIs('admin.requisicoes'),
							],
							[
								'label' => 'Livros',
								'href' => route('admin.livros'),
								'active' => request()->routeIs('admin.livros'),
							],
							[
								'label' => 'Administradores',
								'href' => route('admin.admin-users.index'),
								'active' => request()->routeIs('admin.admin-users.*'),
							],
						];
					@endphp
					<nav class="flex-1 overflow-y-auto p-4 space-y-1 text-sm">
						@foreach ($menuItems as $item)
							<x-admin.sidebar-link :href="$item['href']" :active="$item['active']">
								{{ $item['label'] }}
							</x-admin.sidebar-link>
						@endforeach
					</nav>
					<x-admin.sidebar-footer />
				</div>
			</aside>

			<div class="lg:pl-72 h-screen flex flex-col overflow-hidden transition-all duration-200">
				<div class="fixed top-0 left-0 right-0 z-50">
					<header class="h-20 shrink-0 border-b border-gray-200 bg-white/95 backdrop-blur flex items-center justify-between px-8" style="min-width:0;">
						<div class="flex items-center gap-4">
							<x-application-mark class="block h-9 w-auto" />
							<div>
								<p class="text-xs uppercase tracking-[0.18em] text-gray-400 leading-none">Painel</p>
								<p class="text-base font-semibold text-gray-900 leading-tight">Biblioteca</p>
							</div>
						</div>
						<div class="flex items-center gap-3">
							<button type="button" class="lg:hidden rounded-md p-2 text-gray-600 hover:bg-gray-100" @click="sidebarOpen = true" aria-label="Abrir menu">
								<svg class="size-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true">
									<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 6h16M4 12h16M4 18h16" />
								</svg>
							</button>
							<div>
								<p class="text-xs uppercase tracking-[0.16em] text-gray-500">Área reservada</p>
								<p class="text-sm font-semibold text-gray-900">Administração</p>
							</div>
						</div>
					</header>
				</div>
				   <div class="flex-1 overflow-y-auto pt-20">
					   @if (isset($header))
						   <div class="px-4 sm:px-6 lg:px-8 py-6">
							   {{ $header }}
						   </div>
					   @endif
					   <main class="px-4 sm:px-6 lg:px-8 pb-8">
						   {{ $slot }}
					   </main>
				   </div>
			</div>
		</div>

		@stack('modals')
		@livewireScripts
	</body>
</html>
