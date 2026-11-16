// Jendela Tata Tertib Keanggotaan (isi dari tata-tertib.json, dimuat saat pertama dibuka).
(function () {
    var data = window.HOME_DATA || {};
    var esc = Landing.esc;

    var rulesCache = null;
    var rulesTrigger = null;

    function rulesList(list) {
        return '<ol class="rules-list rules-list-' + list.style + '">' + list.items.map(function (item) {
            return '<li>' + (item.term ? '<strong>' + esc(item.term) + '</strong> ' : '') + esc(item.text) + (item.sub ? rulesList(item.sub) : '') + '</li>';
        }).join('') + '</ol>';
    }

    function rulesHtml(chapters) {
        return chapters.map(function (chapter) {
            return '<section class="rules-chapter"><h3>' + esc(chapter.title) + '</h3>' + chapter.articles.map(function (article) {
                return '<div class="rules-article"><h4>' + esc(article.title) + '</h4>' + article.blocks.map(function (block) {
                    return block.type === 'p' ? '<p>' + esc(block.text) + '</p>' : rulesList(block);
                }).join('') + '</div>';
            }).join('') + '</section>';
        }).join('');
    }

    function closeRules() {
        var modal = document.getElementById('rules-modal');
        if (modal) modal.remove();
        document.body.style.overflow = '';
        document.removeEventListener('keydown', rulesKey);
        if (rulesTrigger) rulesTrigger.focus();
    }

    function rulesKey(event) { if (event.key === 'Escape') closeRules(); }

    function openRules() {
        if (document.getElementById('rules-modal')) return;
        var modal = document.createElement('div');
        modal.id = 'rules-modal';
        modal.className = 'modal-backdrop';
        modal.innerHTML = '<div class="modal-panel modal-panel-wide" role="dialog" aria-modal="true" aria-labelledby="rules-title" tabindex="-1">' +
            '<header class="modal-header"><div><span class="modal-kicker">Syarat &amp; ketentuan</span>' +
            '<h2 id="rules-title">Tata Tertib Keanggotaan &amp; Layanan Sirkulasi</h2><p>Perpustakaan Kementerian Ketenagakerjaan</p></div>' +
            '<button type="button" class="modal-close" aria-label="Tutup" data-close-rules><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" aria-hidden="true"><path d="m6 6 12 12M18 6 6 18"/></svg></button></header>' +
            '<div class="modal-body rules-body" aria-live="polite">Memuat tata tertib…</div>' +
            '<footer class="modal-footer"><button type="button" class="btn-ghost" data-close-rules>Tutup</button></footer></div>';
        document.body.appendChild(modal);
        document.body.style.overflow = 'hidden';
        document.addEventListener('keydown', rulesKey);
        modal.addEventListener('mousedown', function (event) {
            if (event.target === modal) closeRules();
        });
        modal.addEventListener('click', function (event) {
            if (event.target.closest('[data-close-rules]')) closeRules();
        });
        modal.querySelector('.modal-panel').focus();

        var body = modal.querySelector('.rules-body');
        var show = function (chapters) { body.innerHTML = rulesHtml(chapters); };
        if (rulesCache) { show(rulesCache); return; }
        fetch(data.rulesUrl)
            .then(function (response) { return response.json(); })
            .then(function (chapters) { rulesCache = chapters; show(chapters); })
            .catch(function () { body.textContent = 'Tata tertib tidak dapat dimuat. Muat ulang halaman lalu coba lagi.'; });
    }

    document.querySelectorAll('[data-rules]').forEach(function (button) {
        button.addEventListener('click', function () { rulesTrigger = button; openRules(); });
    });
})();
