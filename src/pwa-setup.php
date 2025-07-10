<!-- manifest dan service worker -->
<link rel="manifest" href="manifest.json">
<meta name="theme-color" content="#5d4037">

<script>
if ('serviceWorker' in navigator) {
    navigator.serviceWorker.register('sw.js')
    .then(reg => console.log('✅ ServiceWorker registered:', reg.scope))
    .catch(err => console.error('❌ ServiceWorker failed:', err));
}
</script>