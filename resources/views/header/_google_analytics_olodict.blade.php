@if (config('analytics.enabled') && config('analytics.olodict_google_id'))
    <script async src="https://www.googletagmanager.com/gtag/js?id={{ config('analytics.olodict_google_id') }}"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag() { dataLayer.push(arguments); }
        gtag('js', new Date());
        gtag('config', {!! json_encode(config('analytics.olodict_google_id'), JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) !!});
    </script>
@endif
