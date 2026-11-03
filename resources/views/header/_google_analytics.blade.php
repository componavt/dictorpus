@if (config('analytics.enabled'))
    @if (config('analytics.google_id'))
        <script async src="https://www.googletagmanager.com/gtag/js?id={{ config('analytics.google_id') }}"></script>
        <script>
            window.dataLayer = window.dataLayer || [];
            function gtag() { dataLayer.push(arguments); }
            gtag('js', new Date());
            gtag('config', {!! json_encode(config('analytics.google_id'), JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) !!});
        </script>
    @endif

    @if (preg_match('/^[1-9][0-9]*$/D', (string) config('analytics.yandex_id')))
        <script>
            (function(m,e,t,r,i,k,a) {
                m[i] = m[i] || function() {
                    (m[i].a = m[i].a || []).push(arguments);
                };
                m[i].l = 1 * new Date();
                for (var j = 0; j < document.scripts.length; j++) {
                    if (document.scripts[j].src === r) {
                        return;
                    }
                }
                k = e.createElement(t);
                a = e.getElementsByTagName(t)[0];
                k.async = 1;
                k.src = r;
                a.parentNode.insertBefore(k, a);
            })(window, document, "script", "https://mc.yandex.ru/metrika/tag.js", "ym");

            ym({{ (int) config('analytics.yandex_id') }}, "init", {
                clickmap: true,
                trackLinks: true,
                accurateTrackBounce: true
            });
        </script>
    @endif
@endif
