(function () {
    var data = window.HOME_DATA || {};

    // Skala tampilan: desain dibuat untuk lebar sekitar 1420 px. Di layar yang lebih lebar, seluruh halaman
    // diperbesar sebanding supaya proporsinya sama dengan desain. SKALA mengalikan semuanya (1.1 = 10% lebih besar).
    var SKALA = 1;
    var DESAIN = 1420;
    var frame = document.querySelector('.site-frame');
    function applyZoom() {
        var width = window.innerWidth;
        var zoom = width >= 1024 ? Math.min(2, Math.max(1, width / DESAIN)) * SKALA : 1;
        frame.style.zoom = zoom === 1 ? '' : zoom.toFixed(3);
    }
    applyZoom();
    window.addEventListener('resize', applyZoom);

    // Navbar melayang: beri kelas saat halaman sudah digulir.
    var header = document.querySelector('.site-header');
    function markStuck() { header.classList.toggle('is-stuck', window.scrollY > 24); }
    markStuck();
    window.addEventListener('scroll', markStuck, { passive: true });

    function esc(value) {
        var div = document.createElement('div');
        div.textContent = value == null ? '' : String(value);
        return div.innerHTML;
    }

    function normalize(value) {
        return String(value).toLowerCase().normalize('NFD').replace(/[̀-ͯ]/g, '');
    }

    // Header: menu mobile dan dropdown Layanan.
    var nav = document.getElementById('main-nav');
    var menuButton = document.getElementById('menu-button');
    var dropdown = document.getElementById('nav-dropdown');
    var trigger = dropdown && dropdown.querySelector('.dropdown-trigger');

    function setDropdown(open) {
        dropdown.classList.toggle('is-open', open);
        trigger.setAttribute('aria-expanded', open ? 'true' : 'false');
    }

    function setMenu(open) {
        nav.classList.toggle('is-open', open);
        menuButton.setAttribute('aria-expanded', open ? 'true' : 'false');
        menuButton.setAttribute('aria-label', open ? 'Tutup menu' : 'Buka menu');
        menuButton.querySelector('[data-open]').hidden = open;
        menuButton.querySelector('[data-close]').hidden = !open;
    }

    menuButton.addEventListener('click', function () { setMenu(!nav.classList.contains('is-open')); });
    trigger.addEventListener('click', function () { setDropdown(!dropdown.classList.contains('is-open')); });
    nav.querySelectorAll('a').forEach(function (link) {
        link.addEventListener('click', function () { setDropdown(false); setMenu(false); });
    });
    document.addEventListener('pointerdown', function (event) {
        if (!dropdown.contains(event.target)) setDropdown(false);
    });
    window.addEventListener('keydown', function (event) {
        if (event.key === 'Escape') setDropdown(false);
    });

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

    // Omnisearch: layanan (lokal) + buku (AJAX) + tautan ke katalog OPAC.
    var box = document.getElementById('omnisearch');
    var form = box.querySelector('form');
    var input = document.getElementById('omnisearch-input');
    var list = document.getElementById('omni-results');
    var options = [];
    var active = 0;
    var timer = null;
    var requestId = 0;

    function isOpen() { return !list.hidden && options.length > 0; }

    function close() {
        list.hidden = true;
        form.classList.remove('has-results');
        input.setAttribute('aria-expanded', 'false');
    }

    function highlight() {
        list.querySelectorAll('.omni-option').forEach(function (item, index) {
            item.classList.toggle('is-active', index === active);
            item.setAttribute('aria-selected', index === active ? 'true' : 'false');
        });
    }

    function render() {
        if (!options.length) { close(); return; }
        list.innerHTML = options.map(function (option, index) {
            var attrs = 'class="omni-option" role="option" data-index="' + index + '"';
            if (option.kind === 'service') {
                return '<li ' + attrs + '><span class="omni-thumb omni-thumb-service"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">' +
                    option.service.icon.replace(/^<svg[^>]*>|<\/svg>$/g, '') + '</svg></span><span class="omni-copy"><strong>' + esc(option.service.title) +
                    '</strong><small>Layanan · ' + esc(option.service.short) + '</small></span><span class="omni-tag">Buka</span></li>';
            }
            if (option.kind === 'book') {
                var book = option.book;
                return '<li ' + attrs + '><span class="omni-thumb" style="background:#0f3fb4;color:#9db4ff">' + esc(book.Title.charAt(0)) +
                    '</span><span class="omni-copy"><strong>' + esc(book.Title) + '</strong><small>' + esc(book.Author || 'Anonim') +
                    (book.PublishYear ? ' · ' + esc(book.PublishYear) : '') + '</small></span><span class="omni-tag is-free">Katalog</span></li>';
            }
            return '<li ' + attrs + '><span class="omni-all">Lihat ' + (option.total != null ? option.total + ' ' : '') + 'hasil untuk “' + esc(option.query) +
                '” di katalog OPAC</span>' + data.arrow + '</li>';
        }).join('');
        list.hidden = false;
        form.classList.add('has-results');
        input.setAttribute('aria-expanded', 'true');
        active = Math.min(active, options.length - 1);
        highlight();
    }

    function matchingServices(query) {
        var words = normalize(query.trim()).split(/\s+/);
        return data.services.filter(function (service) {
            var haystack = normalize(service.title + ' ' + service.keywords);
            return words.every(function (word) { return haystack.indexOf(word) !== -1; });
        }).slice(0, 2).map(function (service) { return { kind: 'service', service: service }; });
    }

    function search() {
        var query = input.value.trim();
        if (!query) { options = []; close(); return; }
        var current = ++requestId;
        var local = matchingServices(query);
        options = local.concat([{ kind: 'all', query: query }]);
        active = 0;
        render();
        fetch(data.searchUrl + '?q=' + encodeURIComponent(query), { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(function (response) { return response.json(); })
            .then(function (json) {
                if (current !== requestId || json.status !== 'success') return;
                var books = json.data.map(function (book) { return { kind: 'book', book: book }; });
                options = local.concat(books, [{ kind: 'all', query: query, total: json.total }]);
                render();
            })
            .catch(function () { /* tetap tampilkan layanan dan tautan katalog */ });
    }

    function choose(option) {
        if (option.kind === 'service') window.location.href = option.service.url;
        else if (option.kind === 'book') window.location.href = data.detailUrl + '/' + option.book.ID;
        else window.location.href = data.opacUrl + '?search=' + encodeURIComponent(option.query);
    }

    input.addEventListener('input', function () {
        clearTimeout(timer);
        timer = setTimeout(search, 200);
    });
    input.addEventListener('focus', function () { if (input.value.trim()) search(); });
    input.addEventListener('blur', function () { setTimeout(close, 120); });
    input.addEventListener('keydown', function (event) {
        if (!isOpen()) return;
        if (event.key === 'ArrowDown') { event.preventDefault(); active = (active + 1) % options.length; highlight(); }
        else if (event.key === 'ArrowUp') { event.preventDefault(); active = (active - 1 + options.length) % options.length; highlight(); }
        else if (event.key === 'Escape') close();
    });
    form.addEventListener('submit', function (event) {
        if (isOpen() && options[active] && options[active].kind !== 'all') {
            event.preventDefault();
            choose(options[active]);
        }
    });
    list.addEventListener('mousedown', function (event) { event.preventDefault(); });
    list.addEventListener('click', function (event) {
        var item = event.target.closest('.omni-option');
        if (item) choose(options[Number(item.getAttribute('data-index'))]);
    });
    list.addEventListener('mouseover', function (event) {
        var item = event.target.closest('.omni-option');
        if (item) { active = Number(item.getAttribute('data-index')); highlight(); }
    });

    // Modal Tata Tertib Keanggotaan (isi dari tata-tertib.json, dimuat saat pertama dibuka).
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

    box.querySelectorAll('[data-suggest]').forEach(function (button) {
        button.addEventListener('click', function () {
            input.value = button.getAttribute('data-suggest');
            input.focus();
            search();
        });
    });
})();
