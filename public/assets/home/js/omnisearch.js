// Kolom pencarian hero: layanan (lokal), buku (AJAX), dan tautan ke katalog OPAC.
(function () {
    var data = window.HOME_DATA || {};
    var esc = Landing.esc;
    var normalize = Landing.normalize;

    // Omnisearch: layanan (lokal) + buku (AJAX) + tautan ke katalog OPAC.
    var box = document.getElementById('omnisearch');
    var form = box.querySelector('form');
    var input = document.getElementById('omnisearch-input');
    var list = document.getElementById('omni-results');
    var options = [];
    var bookStatus = ''; // '' | 'loading' | 'error': keadaan pencarian buku, tampil sebagai baris di bawah daftar
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
        var statusLine = bookStatus
            ? '<li class="omni-status" role="presentation">' + (bookStatus === 'loading' ? 'Mencari buku…' : 'Buku tidak dapat dimuat. Layanan dan katalog tetap bisa dibuka.') + '</li>'
            : '';
        list.innerHTML = options.map(function (option, index) {
            var attrs = 'class="omni-option" role="option" data-index="' + index + '"';
            if (option.kind === 'service') {
                return '<li ' + attrs + '><span class="omni-thumb omni-thumb-service"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">' +
                    option.service.icon.replace(/^<svg[^>]*>|<\/svg>$/g, '') + '</svg></span><span class="omni-copy"><strong>' + esc(option.service.title) +
                    '</strong><small>Layanan · ' + esc(option.service.short) + '</small></span><span class="omni-tag">Buka</span></li>';
            }
            if (option.kind === 'book') {
                var book = option.book;
                return '<li ' + attrs + '><span class="omni-thumb" style="background:#0f3fb4;color:#cfe0ff">' + esc(book.Title.charAt(0)) +
                    '</span><span class="omni-copy"><strong>' + esc(book.Title) + '</strong><small>' + esc(book.Author || 'Anonim') +
                    (book.PublishYear ? ' · ' + esc(book.PublishYear) : '') + '</small></span><span class="omni-tag is-free">Katalog</span></li>';
            }
            return '<li ' + attrs + '><span class="omni-all">Lihat ' + (option.total != null ? option.total + ' ' : '') + 'hasil untuk “' + esc(option.query) +
                '” di katalog OPAC</span>' + data.arrow + '</li>';
        }).join('') + statusLine;
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
        if (!query) { options = []; bookStatus = ''; close(); return; }
        var current = ++requestId;
        var local = matchingServices(query);
        options = local.concat([{ kind: 'all', query: query }]);
        bookStatus = 'loading';
        active = 0;
        render();
        fetch(data.searchUrl + '?q=' + encodeURIComponent(query), { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(function (response) { return response.json(); })
            .then(function (json) {
                if (current !== requestId) return;
                if (json.status !== 'success') throw new Error('pencarian gagal');
                var books = json.data.map(function (book) { return { kind: 'book', book: book }; });
                options = local.concat(books, [{ kind: 'all', query: query, total: json.total }]);
                bookStatus = '';
                render();
            })
            .catch(function () {
                // Layanan dan tautan katalog tetap tampil; hanya beri tahu bahwa buku tidak termuat.
                if (current !== requestId) return;
                bookStatus = 'error';
                render();
            });
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

    // Tombol saran "Populer:" mengisi kolom pencarian.
    box.querySelectorAll('[data-suggest]').forEach(function (button) {
        button.addEventListener('click', function () {
            input.value = button.getAttribute('data-suggest');
            input.focus();
            search();
        });
    });
})();
