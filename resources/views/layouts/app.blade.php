<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>Biblioteca</title>
        <link rel="icon" type="image/png" sizes="64x64" href="{{ asset('favicon.png') }}">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <!-- Styles -->
        @livewireStyles
    </head>
    <body class="font-sans antialiased min-h-screen bg-gray-100 flex flex-col">
        <x-banner />

        <div class="flex-1">
            @livewire('navigation-menu')

            <!-- Page Heading -->
            @if (isset($header))
                <header class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endif

            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>
        </div>

        @include('components.site-footer')

        @stack('modals')

        @livewireScripts
        <!-- Popup de confirmação de eliminação -->
        <div id="delete-popup" style="display:none; position:fixed; top:0; left:0; width:100vw; height:100vh; z-index:9999; background:rgba(0,0,0,0.15); align-items:center; justify-content:center;">
            <div style="background:#fff; border:1px solid #e53e3e; box-shadow:0 2px 8px rgba(0,0,0,0.15); padding:24px 32px; border-radius:10px; color:#b91c1c; font-size:1rem; text-align:center; min-width:300px; max-width:90vw; margin:auto;">
                <div style="display:flex;align-items:center;justify-content:center;margin-bottom:16px;">
                    <svg style="width:32px;height:32px;color:#e53e3e;margin-right:8px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    <span style="font-weight:600;font-size:1.1em;">Confirmação</span>
                </div>
                <div style="margin-bottom:24px;">Tens a certeza que queres eliminar este item?</div>
                <div style="display:flex;gap:12px;justify-content:center;">
                    <button onclick="confirmDeletePopup()" style="background:#e53e3e;color:#fff;font-weight:500;padding:8px 24px;border:none;border-radius:6px;cursor:pointer;">Confirmar</button>
                    <button onclick="closeDeletePopup()" style="background:#f3f4f6;color:#b91c1c;font-weight:500;padding:8px 24px;border:none;border-radius:6px;cursor:pointer;">Cancelar</button>
                </div>
            </div>
        </div>

        <!-- Popup de aviso para limite de livros requisitados -->
        <div id="limit-popup" style="display:none; position:fixed; top:0; left:0; width:100vw; height:100vh; z-index:9999; background:rgba(0,0,0,0.15); align-items:center; justify-content:center;">
            <div style="background:#fff; border:1px solid #facc15; box-shadow:0 2px 8px rgba(0,0,0,0.15); padding:24px 32px; border-radius:10px; color:#b45309; font-size:1rem; text-align:center; min-width:300px; max-width:90vw; margin:auto;">
                <div style="display:flex;align-items:center;justify-content:center;margin-bottom:16px;">
                    <svg style="width:32px;height:32px;color:#facc15;margin-right:8px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    <span style="font-weight:600;font-size:1.1em;">Aviso</span>
                </div>
                <div style="margin-bottom:24px;">Não é possível requisitar mais de 3 livros ao mesmo tempo.</div>
                <button onclick="closeLimitPopup()" style="background:#facc15;color:#fff;font-weight:500;padding:8px 24px;border:none;border-radius:6px;cursor:pointer;">OK</button>
            </div>
        </div>
        <script>
            // Popup de confirmação de eliminação
            let deleteForm = null;
            function showDeletePopup(form) {
                deleteForm = form;
                document.getElementById('delete-popup').style.display = 'flex';
            }
            function closeDeletePopup() {
                document.getElementById('delete-popup').style.display = 'none';
                deleteForm = null;
            }
            function confirmDeletePopup() {
                document.getElementById('delete-popup').style.display = 'none';
                if (deleteForm) deleteForm.submit();
            }
            function showLimitPopup() {
                document.getElementById('limit-popup').style.display = 'flex';
            }
            function closeLimitPopup() {
                document.getElementById('limit-popup').style.display = 'none';
            }
            document.addEventListener('livewire:load', function () {
                Livewire.on('showLimitPopup', showLimitPopup);
                Livewire.on('notify', data => {
                    showPopup(data.message);
                });
            });
            function showPopup(message) {
                let popup = document.getElementById('popup-message');
                if (!popup) {
                    popup = document.createElement('div');
                    popup.id = 'popup-message';
                    popup.style.cssText = 'display:none; position:fixed; top:30px; left:50%; transform:translateX(-50%); z-index:9999; min-width:300px; max-width:90vw; background:#fff; border:1px solid #e53e3e; box-shadow:0 2px 8px rgba(0,0,0,0.15); padding:16px 24px; border-radius:8px; color:#e53e3e; font-size:1rem; text-align:center;';
                    document.body.appendChild(popup);
                }
                popup.innerText = message;
                popup.style.display = 'block';
                setTimeout(() => {
                    popup.style.display = 'none';
                }, 3000);
            }
        </script>
    </body>
</html>
