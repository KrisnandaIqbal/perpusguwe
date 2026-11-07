// Tab "Unggulan" dan "Populer" di bagian buku.
(function () {
    // Tab buku terbaru / populer.
    var tabs = document.querySelectorAll('[data-tab]');
    tabs.forEach(function (tab) {
        tab.addEventListener('click', function () {
            tabs.forEach(function (other) {
                var on = other === tab;
                other.classList.toggle('is-active', on);
                other.setAttribute('aria-selected', on ? 'true' : 'false');
            });
            document.querySelectorAll('[data-panel]').forEach(function (panel) {
                panel.hidden = panel.getAttribute('data-panel') !== tab.getAttribute('data-tab');
            });
        });
    });
})();
