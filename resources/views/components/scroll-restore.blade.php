@props([
    'storageKey' => 'app:scroll-restore',
])

{{-- 
    Componente para restaurar a posição exata do scroll ao voltar a uma página anterior.
--}}
<script>
    (function () {
        var key = '{{ $storageKey }}';

        function restorePreviousScroll() {
            var raw = sessionStorage.getItem(key);

            if (!raw) {
                return;
            }

            try {
                var saved = JSON.parse(raw);
                var currentPath = window.location.pathname + window.location.search;

                if (!saved || saved.path !== currentPath || typeof saved.y !== 'number') {
                    return;
                }

                sessionStorage.removeItem(key);

                requestAnimationFrame(function () {
                    window.scrollTo({ top: Math.max(0, saved.y), behavior: 'auto' });
                });
            } catch (error) {
                sessionStorage.removeItem(key);
            }
        }

        window.addEventListener('load', restorePreviousScroll);
    })();
</script>
