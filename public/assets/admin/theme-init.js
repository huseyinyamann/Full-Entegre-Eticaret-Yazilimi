// Sayfa çizilmeden önce kayıtlı temayı uygula (koyu/açık geçişinde parlama olmasın)
(function () {
    var theme = null;
    try { theme = localStorage.getItem('admin-theme'); } catch (e) {}
    if (!theme) theme = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
    document.documentElement.setAttribute('data-bs-theme', theme);
})();
