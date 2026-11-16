// Tes E2E landing page (beranda). Setiap tes menyimpan tangkapan layar dan keterangan ke
// docs/laporan-uji-landing/img, dipakai oleh scripts/laporan-uji-landing.js untuk membuat laporan PDF.
const { test, expect } = require('@playwright/test');
const fs = require('fs');
const path = require('path');

const OUT = path.join(__dirname, '..', '..', 'docs', 'laporan-uji-landing', 'img');
fs.mkdirSync(OUT, { recursive: true });

test.use({ viewport: { width: 1366, height: 768 } });

/** Simpan tangkapan layar dan catat keterangannya sebagai anotasi tes. */
async function shot(page, id, name, caption, opts = {}) {
  const file = `${id}-${name}.png`;
  await page.screenshot({ path: path.join(OUT, file), ...opts });
  test.info().annotations.push({ type: 'gambar', description: `${file}|${caption}` });
}

function note(text) {
  test.info().annotations.push({ type: 'keterangan', description: text });
}

/** Data terstruktur untuk laporan PDF (dibaca buat-laporan.js, tidak ditampilkan sebagai kalimat). */
function data(obj) {
  test.info().annotations.push({ type: 'data', description: JSON.stringify(obj) });
}

/** Buka beranda, tunggu layar pembuka hilang dan font siap. */
async function bukaBeranda(page) {
  const response = await page.goto('/');
  expect(response.status(), 'status HTTP beranda').toBeLessThan(400);
  await page.waitForSelector('.pk-loader', { state: 'hidden', timeout: 20000 }).catch(() => {});
  await page.evaluate(() => document.fonts.ready);
  await page.waitForSelector('.site-frame');
  await page.waitForTimeout(500);
  return response;
}

async function gulirKe(page, selector, offset = -110) {
  await page.evaluate(([sel, off]) => {
    const el = document.querySelector(sel);
    window.scrollTo({ top: el.getBoundingClientRect().top + window.scrollY + off, behavior: 'instant' });
  }, [selector, offset]);
  await page.waitForTimeout(700);
}

/** Hitung rasio kontras teks terhadap latar efektifnya (hanya latar warna polos). */
function hitungKontras(page, daftar) {
  return page.evaluate((items) => {
    const parse = (c) => {
      const m = c.match(/rgba?\(([^)]+)\)/);
      if (!m) return null;
      const p = m[1].split(/[ ,/]+/).filter(Boolean).map(Number);
      return { r: p[0], g: p[1], b: p[2], a: p.length > 3 ? p[3] : 1 };
    };
    const over = (fg, bg) => ({
      r: fg.r * fg.a + bg.r * (1 - fg.a),
      g: fg.g * fg.a + bg.g * (1 - fg.a),
      b: fg.b * fg.a + bg.b * (1 - fg.a),
      a: 1,
    });
    const lum = (c) => {
      const f = (v) => { v /= 255; return v <= 0.03928 ? v / 12.92 : Math.pow((v + 0.055) / 1.055, 2.4); };
      return 0.2126 * f(c.r) + 0.7152 * f(c.g) + 0.0722 * f(c.b);
    };
    const latar = (el) => {
      const lapis = [];
      for (let n = el; n; n = n.parentElement) {
        const c = parse(getComputedStyle(n).backgroundColor);
        if (c && c.a > 0) { lapis.push(c); if (c.a === 1) break; }
      }
      let dasar = { r: 255, g: 255, b: 255, a: 1 };
      for (let i = lapis.length - 1; i >= 0; i--) dasar = over(lapis[i], dasar);
      return dasar;
    };
    return items.map(([selector, label]) => {
      const el = document.querySelector(selector);
      if (!el) return { label, selector, ada: false };
      const cs = getComputedStyle(el);
      let opasitas = 1;
      for (let n = el; n; n = n.parentElement) opasitas *= parseFloat(getComputedStyle(n).opacity);
      const fg = parse(cs.color);
      fg.a *= opasitas;
      const bg = latar(el);
      const teks = over(fg, bg);
      const [a, b] = [lum(teks), lum(bg)];
      const rasio = (Math.max(a, b) + 0.05) / (Math.min(a, b) + 0.05);
      const ukuran = parseFloat(cs.fontSize);
      const besar = ukuran >= 24 || (ukuran >= 18.66 && parseInt(cs.fontWeight, 10) >= 700);
      return { label, selector, ada: true, rasio: Math.round(rasio * 100) / 100, ukuran, besar, batas: besar ? 3 : 4.5 };
    });
  }, daftar);
}

const DAFTAR_KONTRAS = [
  ['.hero h1', 'Judul bagian utama'],
  ['.hero-copy > p', 'Paragraf bagian utama'],
  ['.eyebrow', 'Label kecil biru di bagian utama'],
  ['.omni-suggestions span', 'Tulisan "Populer:"'],
  ['.shortcuts-head h2', 'Judul "Akses cepat layanan" (putih di atas biru)'],
  ['.shortcut-card small', 'Keterangan kartu layanan'],
  ['.section-kicker', 'Label kecil biru di bagian buku'],
  ['.showcase .section-head h2', 'Judul bagian buku'],
  ['.showcase-meta', 'Keterangan kecil di kartu buku'],
  ['.showcase-info h3', 'Judul buku'],
  ['.showcase-info p', 'Nama penulis buku'],
  ['.catalog-cta-copy p', 'Paragraf bagian katalog'],
  ['.subject-list small', 'Jumlah judul pada daftar subjek'],
  ['.footer-description', 'Deskripsi di bagian bawah halaman'],
  ['.footer-col a', 'Tautan di bagian bawah halaman'],
  ['.footer-contact li', 'Informasi kontak di bagian bawah halaman'],
  ['.footer-bottom span', 'Hak cipta di bagian bawah halaman'],
];

test.describe('Halaman depan (beranda)', () => {
  test('T01 Halaman terbuka tanpa kesalahan', async ({ page }) => {
    const galatConsole = [];
    const gagalMuat = [];
    const respons4xx = [];
    page.on('console', (m) => { if (m.type() === 'error') galatConsole.push(m.text()); });
    page.on('pageerror', (e) => galatConsole.push(String(e)));
    page.on('requestfailed', (r) => gagalMuat.push(`${r.url()} (${r.failure() && r.failure().errorText})`));
    page.on('response', (r) => { if (r.status() >= 400) respons4xx.push(`${r.status()} ${r.url()}`); });

    const t0 = Date.now();
    await bukaBeranda(page);
    const waktu = await page.evaluate(() => {
      const n = performance.getEntriesByType('navigation')[0];
      return { dcl: Math.round(n.domContentLoadedEventEnd), load: Math.round(n.loadEventEnd), transfer: Math.round(n.transferSize / 1024) };
    });
    await expect(page).toHaveTitle(/Beranda/);
    const detikPemuatan = (Date.now() - t0) / 1000;
    data({ layarPemuatanDetik: detikPemuatan });
    note(`Judul halaman: "${await page.title()}". Isi halaman siap dalam ${waktu.dcl} milidetik dan selesai dimuat dalam ${waktu.load} milidetik. Ukuran dokumen ${waktu.transfer} KB. Layar pemuatan (logo) hilang ${detikPemuatan} detik setelah halaman dibuka.`);
    await shot(page, 'T01', 'beranda-pertama', 'Tampilan pertama beranda di layar 1366 x 768, setelah layar pemuatan (logo) hilang.');

    expect(galatConsole, 'pesan galat di konsol browser').toEqual([]);
    expect(gagalMuat, 'berkas yang gagal dimuat').toEqual([]);
    expect(respons4xx.filter((u) => !u.includes('favicon')), 'berkas yang dijawab dengan kode galat').toEqual([]);
  });

  test('T02 Bagian utama: judul, kolom pencarian langsung terlihat, foto tampil', async ({ page }) => {
    await bukaBeranda(page);
    await expect(page.locator('.hero h1')).toContainText('Pengetahuan Untuk');
    await expect(page.locator('.hero h1')).toContainText('Kemajuan Pekerja');
    const batas = await page.evaluate(() => ({
      search: document.querySelector('.catalog-search').getBoundingClientRect().bottom,
      chips: document.querySelector('.omni-suggestions').getBoundingClientRect().bottom,
      tinggi: window.innerHeight,
    }));
    note(`Kolom pencarian berakhir pada ${Math.round(batas.search)} px dan saran "Populer" pada ${Math.round(batas.chips)} px dari tepi atas, sedangkan tinggi layar ${batas.tinggi} px. Artinya pengunjung dapat langsung mencari tanpa menggulir halaman.`);
    expect(batas.chips, 'saran Populer harus terlihat tanpa menggulir').toBeLessThanOrEqual(batas.tinggi);

    const foto = await page.evaluate(() => {
      const i = document.querySelector('.hero-image-wrap img');
      return { dimuat: i.complete && i.naturalWidth > 0, lebar: i.naturalWidth, alt: i.alt };
    });
    expect(foto.dimuat, 'foto hero termuat').toBe(true);
    expect(foto.alt.length, 'teks alternatif foto').toBeGreaterThan(10);
    note(`Foto utama selebar ${foto.lebar} px. Teks pengganti gambar: "${foto.alt}".`);

    // Teks di kiri berada di dalam rentang tinggi foto dan kira-kira di tengahnya (selisih atas dan bawah tidak jauh).
    const jarak = await page.evaluate(() => {
      const foto = document.querySelector('.hero-image-wrap').getBoundingClientRect();
      const atas = document.querySelector('.hero-copy .eyebrow').getBoundingClientRect().top;
      const bawah = document.querySelector('.omni-suggestions').getBoundingClientRect().bottom;
      return { atas: Math.round(atas - foto.top), bawah: Math.round(foto.bottom - bawah) };
    });
    note(`Jarak teks kiri ke tepi atas foto ${jarak.atas} px dan ke tepi bawah foto ${jarak.bawah} px (bila seimbang, teks berada di tengah foto).`);
    expect(jarak.atas, 'teks tidak lebih tinggi dari foto').toBeGreaterThanOrEqual(0);
    expect(jarak.bawah, 'teks tidak lebih rendah dari foto').toBeGreaterThanOrEqual(0);
    expect(Math.abs(jarak.atas - jarak.bawah), 'teks tidak jauh dari tengah foto').toBeLessThanOrEqual(80);
    await shot(page, 'T02', 'hero', 'Bagian utama: judul, kolom pencarian, saran Populer, dan foto ruang baca.', { clip: { x: 0, y: 0, width: 1366, height: 700 } });
  });

  test('T03 Huruf: teks memakai Plus Jakarta Sans, judul konten memakai Source Serif 4', async ({ page }) => {
    await bukaBeranda(page);
    const hasil = await page.evaluate(() => {
      const bukan = [];
      let total = 0;
      document.querySelectorAll('.page-shell, .page-shell *').forEach((el) => {
        const punyaTeks = [...el.childNodes].some((n) => n.nodeType === 3 && n.textContent.trim());
        if (!punyaTeks) return;
        total++;
        const f = getComputedStyle(el).fontFamily.split(',')[0].replace(/["']/g, '').trim();
        if (f !== 'Plus Jakarta Sans' && !(f === 'Source Serif 4' && el.closest('h1, h2, h3'))) bukan.push(`${el.tagName}.${el.className} -> ${f}`);
      });
      const kontrol = [...document.querySelectorAll('.page-shell input, .page-shell button, .page-shell select')]
        .filter((e) => !getComputedStyle(e).fontFamily.includes('Plus Jakarta Sans')).length;
      const dimuat = [...new Set([...document.fonts].filter((f) => f.status === 'loaded').map((f) => `${f.family} ${f.weight}`))];
      return { total, bukan, kontrol, dimuat };
    });
    note(`${hasil.total} bagian teks diperiksa. Ketebalan huruf yang dimuat: ${hasil.dimuat.join(', ')}. Bagian teks berhuruf lain: ${hasil.bukan.length}. Kolom isian dan tombol berhuruf lain: ${hasil.kontrol}.`);
    expect(hasil.bukan).toEqual([]);
    expect(hasil.kontrol).toBe(0);
    expect(hasil.dimuat.some((f) => f.startsWith('Plus Jakarta Sans'))).toBe(true);
    expect(hasil.dimuat.some((f) => f.startsWith('Source Serif 4'))).toBe(true);
  });

  test('T04 Bilah menu atas: pilihan menu, tombol Masuk, dan menu turun', async ({ page }) => {
    await bukaBeranda(page);
    const menu = page.locator('.gn-bar');
    for (const teks of ['Beranda', 'OPAC', 'Browse', 'Buku Tamu', 'Layanan', 'Statistik', 'Masuk']) {
      await expect(menu).toContainText(teks);
    }
    await expect(page.locator('.gn-brand-copy strong')).toHaveText('Perpustakaan');
    await expect(page.locator('.gn-brand-copy span')).toHaveText('Kementerian Ketenagakerjaan RI');
    await expect(page.locator('.gn-nav a.active')).toHaveText(/Beranda/);

    const tombol = page.locator('.gn-drop-btn').first();
    await tombol.click();
    await expect(tombol).toHaveAttribute('aria-expanded', 'true');
    const butir = await page.locator('.gn-drop.is-open .gn-item').count();
    note(`Menu turun Layanan berisi ${butir} pilihan.`);
    expect(butir).toBeGreaterThanOrEqual(2);
    await shot(page, 'T04', 'dropdown', 'Menu turun Layanan terbuka dari bilah menu di atas.', { clip: { x: 0, y: 0, width: 1366, height: 460 } });

    await page.keyboard.press('Escape');
    await expect(tombol).toHaveAttribute('aria-expanded', 'false');
    await tombol.click();
    await page.mouse.click(300, 600);
    await expect(tombol).toHaveAttribute('aria-expanded', 'false');
    note('Menu turun tertutup dengan tombol Esc dan dengan klik di luar menu.');
  });

  test('T05 Bilah menu mengambang (efek kaca) saat halaman digulir', async ({ page }) => {
    await bukaBeranda(page);
    const header = page.locator('.gn-bar');
    await expect(header).not.toHaveClass(/is-stuck/);
    await gulirKe(page, '.showcase', -140);
    await expect(header).toHaveClass(/is-stuck/);
    const info = await page.evaluate(() => {
      const h = document.querySelector('.gn-bar');
      const kaca = getComputedStyle(h, '::before');
      return { posisi: getComputedStyle(h).position, atas: Math.round(h.getBoundingClientRect().top), tinggi: Math.round(h.getBoundingClientRect().height), ruang: Math.round(parseFloat(getComputedStyle(document.querySelector('.page-shell')).paddingTop)), blur: kaca.backdropFilter, framOverflow: getComputedStyle(document.querySelector('.site-frame')).overflow };
    });
    note(`Bilah menu menempel di tepi atas layar (jaraknya ${info.atas} px), tingginya ${info.tinggi} px dan ruang di atas halaman ${info.ruang} px. Efek kaca: ${info.blur}. Pengaturan pembungkus halaman: ${info.framOverflow} (harus "visible" agar efek kaca bekerja).`);
    expect(info.posisi).toBe('fixed');
    expect(info.atas).toBe(0);
    expect(info.tinggi, 'ruang di atas halaman (.page-shell padding-top) harus cukup untuk navbar').toBeLessThanOrEqual(info.ruang);
    expect(info.blur).toMatch(/blur|lens-filter/);
    expect(info.framOverflow, 'overflow pembungkus harus visible agar blur kaca bekerja').toBe('visible');
    await shot(page, 'T05', 'navbar-kaca', 'Bilah menu menempel di atas dan berubah menjadi kotak kaca saat halaman digulir.', { clip: { x: 0, y: 0, width: 1366, height: 330 } });
    await page.evaluate(() => window.scrollTo({ top: 0, behavior: 'instant' }));
    await expect(header).not.toHaveClass(/is-stuck/);
  });

  test('T06 Pencarian: saran, hasil langsung, dan pindah ke katalog OPAC', async ({ page }) => {
    await bukaBeranda(page);
    const input = page.locator('#omnisearch-input');
    await page.locator('[data-suggest="K3"]').click();
    await expect(input).toHaveValue('K3');
    const jawaban = page.waitForResponse((r) => r.url().includes('/home/search-books'), { timeout: 15000 });
    await input.fill('hukum');
    const respons = await jawaban;
    expect(respons.status(), 'status /home/search-books').toBe(200);
    const json = await respons.json();
    note(`Layanan pencarian (/home/search-books) menjawab dengan kode ${respons.status()}: ${json.data.length} buku ditampilkan dari ${json.total} buku yang cocok.`);
    const hasil = page.locator('#omni-results .omni-option');
    await expect(hasil.last()).toContainText('di katalog OPAC', { timeout: 10000 });
    await expect(hasil).toHaveCount(json.data.length + 1, { timeout: 10000 });
    await expect(page.locator('.catalog-search')).toHaveClass(/has-results/);
    await expect(input).toHaveAttribute('aria-expanded', 'true');
    const jumlah = await hasil.count();
    const adaBuku = await page.locator('#omni-results .omni-tag.is-free').count();
    note(`Pencarian "hukum" menampilkan ${jumlah} pilihan hasil (${adaBuku} buku dari katalog) dan satu tautan untuk melihat semua hasil di katalog OPAC.`);
    expect(jumlah).toBeGreaterThanOrEqual(2);
    await shot(page, 'T06', 'hasil-pencarian', 'Kolom pencarian menampilkan buku dari katalog dan tautan "Lihat hasil di katalog OPAC".', { clip: { x: 0, y: 0, width: 1366, height: 760 } });

    await page.keyboard.press('ArrowDown');
    await expect(page.locator('#omni-results .omni-option.is-active')).toHaveCount(1);
    await page.keyboard.press('Escape');
    await expect(page.locator('#omni-results')).toBeHidden();

    await input.fill('');
    await input.fill('hukum');
    await expect(hasil).toHaveCount(json.data.length + 1, { timeout: 10000 });
    await hasil.last().click();
    await page.waitForURL(/\/opac\?search=hukum/);
    note(`Memilih tautan "Lihat hasil" membuka ${page.url()}.`);
    expect(page.url()).toContain('search=hukum');
  });

  test('T07 Akses cepat layanan: tujuan tautan dan tampilan saat kursor diarahkan', async ({ page }) => {
    await bukaBeranda(page);
    const kartu = page.locator('.shortcut-card');
    const n = await kartu.count();
    expect(n).toBeGreaterThanOrEqual(5);
    const href = await kartu.evaluateAll((els) => els.map((e) => e.getAttribute('href')));
    note(`${n} kartu layanan dengan tujuan: ${href.join(', ')}.`);
    for (const h of href) {
      expect(h, 'tautan kartu layanan').toMatch(/^https?:\/\//);
    }
    expect(href.some((h) => h.includes('/peminjaman-mandiri'))).toBe(true);
    expect(href.some((h) => h.includes('/buku-tamu'))).toBe(true);

    await gulirKe(page, '#layanan', -130);
    const pertama = kartu.first();
    const sebelum = await pertama.evaluate((e) => getComputedStyle(e).backgroundColor);
    await shot(page, 'T07', 'layanan-normal', 'Bagian "Akses cepat layanan" dalam keadaan biasa.', { clip: { x: 0, y: 0, width: 1366, height: 520 } });
    await pertama.hover();
    await page.waitForTimeout(500);
    const hover = await pertama.evaluate((e) => ({ bg: getComputedStyle(e).backgroundColor, warna: getComputedStyle(e).color, ikon: getComputedStyle(e.querySelector('.shortcut-icon')).backgroundColor }));
    note(`Saat kursor diarahkan ke kartu pertama: warna latar berubah dari ${sebelum} menjadi ${hover.bg}, warna teks menjadi ${hover.warna}, dan warna ikon menjadi ${hover.ikon}.`);
    expect(hover.bg).toBe('rgb(255, 255, 255)');
    expect(hover.warna).toBe('rgb(21, 25, 34)');
    expect(hover.ikon).toBe('rgb(15, 63, 180)');
    await shot(page, 'T07', 'layanan-hover', 'Kartu layanan pertama saat kursor diarahkan: latar putih, teks gelap, ikon biru.', { clip: { x: 0, y: 0, width: 1366, height: 520 } });
  });

  test('T08 Buku: pilihan Terbaru/Populer, sampul, dan gerak saat kursor diarahkan', async ({ page }) => {
    await bukaBeranda(page);
    await gulirKe(page, '.showcase', -110);
    const terbaru = page.locator('[data-panel="terbaru"] .showcase-card');
    await expect(terbaru).toHaveCount(4);
    const gen = await page.locator('[data-panel="terbaru"] .book-cover').count();
    const img = await page.locator('[data-panel="terbaru"] .showcase-cover img').count();
    note(`Pilihan Terbaru: 4 kartu buku (${gen} sampul otomatis, ${img} sampul dari gambar asli).`);
    await shot(page, 'T08', 'buku-terbaru', 'Bagian buku, pilihan Terbaru. Sampul otomatis dipakai untuk buku yang belum punya gambar sampul.', { clip: { x: 0, y: 0, width: 1366, height: 768 } });

    await page.getByRole('tab', { name: 'Populer' }).click();
    await expect(page.locator('[data-panel="populer"]')).toBeVisible();
    await expect(page.locator('[data-panel="terbaru"]')).toBeHidden();
    await expect(page.getByRole('tab', { name: 'Populer' })).toHaveAttribute('aria-selected', 'true');
    const populer = await page.locator('[data-panel="populer"] .showcase-card').count();
    note(`Pilihan Populer menampilkan ${populer} buku (hanya buku yang pernah dipinjam).`);
    await shot(page, 'T08', 'buku-populer', 'Pilihan Populer: buku yang paling sering dipinjam.', { clip: { x: 0, y: 0, width: 1366, height: 768 } });
    await page.getByRole('tab', { name: 'Terbaru' }).click();

    const sampul = page.locator('[data-panel="terbaru"] .showcase-card').first().locator('.book-cover-md, .showcase-cover img').first();
    const diam = await sampul.evaluate((e) => getComputedStyle(e).transform);
    await page.locator('[data-panel="terbaru"] .showcase-card').first().hover();
    await page.waitForTimeout(600);
    const naik = await sampul.evaluate((e) => getComputedStyle(e).transform);
    note('Saat diam, sampul tidak bergerak. Saat kursor diarahkan, sampul miring ke atas dan terangkat.');
    expect(diam).toBe('none');
    expect(naik).not.toBe('none');
    await shot(page, 'T08', 'buku-hover', 'Kartu buku pertama saat kursor diarahkan: sampul miring ke atas dan terangkat.', { clip: { x: 0, y: 0, width: 1366, height: 768 } });
  });

  test('T09 Katalog: daftar subjek dan tombol OPAC', async ({ page }) => {
    await bukaBeranda(page);
    await gulirKe(page, '.catalog-cta', -100);
    const ubin = page.locator('.subject-list a');
    const n = await ubin.count();
    expect(n).toBeGreaterThanOrEqual(1);
    const href = await ubin.first().getAttribute('href');
    note(`${n} subjek ditampilkan. Tautan pertama: ${href}. Judul bagian: "${(await page.locator('.catalog-cta h2').innerText()).replace(/\n/g, ' ')}".`);
    expect(href).toContain('/opac?Subject=');
    await expect(page.locator('.catalog-cta .cta-button')).toHaveAttribute('href', /\/opac$/);
    await shot(page, 'T09', 'katalog-subjek', 'Bagian katalog: daftar subjek beserta jumlah judul yang sebenarnya.', { clip: { x: 0, y: 0, width: 1366, height: 768 } });
    await ubin.first().click();
    await page.waitForURL(/\/opac\?Subject=/);
    note(`Memilih subjek membuka ${decodeURIComponent(page.url())}.`);
  });

  test('T10 Bagian bawah halaman: kontak, media sosial, dan tautan lain', async ({ page }) => {
    await bukaBeranda(page);
    await page.evaluate(() => window.scrollTo({ top: document.body.scrollHeight, behavior: 'instant' }));
    await page.waitForTimeout(600);
    const footer = page.locator('.site-footer');
    await expect(footer).toContainText('Gatot Subroto Kav. 51');
    await expect(footer).toContainText('Gedung B');
    await expect(footer.locator('a[href^="mailto:perpustakaan@kemnaker.go.id"]').first()).toBeVisible();
    await expect(footer).toContainText('07.30 – 16.00 WIB');
    await expect(footer).toContainText('07.30 – 16.30 WIB');
    const sosial = footer.locator('.footer-socials a');
    await expect(sosial).toHaveCount(4);
    for (const a of await sosial.all()) {
      await expect(a).toHaveAttribute('target', '_blank');
      await expect(a).toHaveAttribute('rel', /noreferrer/);
      expect(await a.getAttribute('aria-label')).toMatch(/Kemnaker/);
    }
    const direktori = footer.locator('[aria-label="Direktori"] a');
    await expect(direktori).toHaveCount(5);
    for (const col of ['Layanan', 'Petunjuk', 'Direktori']) {
      await expect(footer.locator(`.footer-col h2:text-is("${col}")`)).toBeVisible();
    }
    const teksHakCipta = await footer.locator('.footer-bottom').innerText();
    expect(teksHakCipta).toContain('Seluruh hak cipta dilindungi');
    note(`Bagian bawah halaman memuat 4 tautan media sosial (dibuka di tab baru dan aman), 5 tautan situs lain, serta 3 kolom tautan. Hak cipta: "${teksHakCipta.replace(/\n+/g, ' | ')}".`);
    await shot(page, 'T10', 'footer', 'Bagian bawah halaman: identitas dan kontak di kiri, tiga kolom tautan, dan baris hak cipta.', { clip: { x: 0, y: 0, width: 1366, height: 768 } });
  });

  test('T11 Jendela Tata Tertib Keanggotaan', async ({ page }) => {
    await bukaBeranda(page);
    await page.evaluate(() => window.scrollTo({ top: document.body.scrollHeight, behavior: 'instant' }));
    const pemicu = page.locator('[data-rules]').first();
    await pemicu.click();
    const modal = page.locator('#rules-modal');
    await expect(modal).toBeVisible();
    await expect(modal.locator('.rules-chapter')).toHaveCount(10, { timeout: 10000 });
    await expect(modal.locator('.rules-chapter h3').first()).toContainText('BAB I');
    expect(await page.evaluate(() => document.body.style.overflow)).toBe('hidden');
    note('Jendela memuat 10 bab dari berkas tata-tertib.json. Halaman di belakangnya tidak dapat digulir selama jendela terbuka.');
    await shot(page, 'T11', 'modal-tata-tertib', 'Jendela Tata Tertib Keanggotaan dan Layanan Sirkulasi.');

    await modal.locator('[data-close-rules]').first().click();
    await expect(modal).toHaveCount(0);
    await expect(pemicu).toBeFocused();
    expect(await page.evaluate(() => document.body.style.overflow)).toBe('');

    await pemicu.click();
    await expect(modal).toBeVisible();
    await page.keyboard.press('Escape');
    await expect(modal).toHaveCount(0);
    await pemicu.click();
    await page.mouse.click(8, 8);
    await expect(modal).toHaveCount(0);
    note('Jendela dapat ditutup dengan tombol Tutup, tombol Esc, atau klik di luar jendela. Setelah ditutup, posisi keyboard kembali ke tombol yang tadi ditekan.');
  });

  test('T12 Semua tautan di dalam situs dapat dibuka', async ({ page }) => {
    test.setTimeout(180000);
    await bukaBeranda(page);
    const tautan = await page.evaluate(() => {
      const asal = location.origin;
      return [...new Set([...document.querySelectorAll('.page-shell a[href]')].map((a) => a.href).filter((h) => h.startsWith(asal)).map((h) => h.split('#')[0]))]
        .filter((h) => h !== asal + '/' && h !== asal);
    });
    const baris = [];
    for (const url of tautan) {
      const t0 = Date.now();
      const r = await page.request.get(url, { maxRedirects: 5, timeout: 60000 });
      baris.push({ url: url.replace(/^https?:\/\/[^/]+/, ''), status: r.status(), ms: Date.now() - t0 });
    }
    data({ tautan: baris });
    note('Semua tautan di dalam situs dijawab dengan kode berhasil (di bawah 400). Rincian alamat, kode, dan waktu jawaban ada pada tabel.');
    for (const b of baris) expect(b.status, `status ${b.url}`).toBeLessThan(400);
    expect(tautan.length).toBeGreaterThanOrEqual(8);
  });

  test('T13 Gambar: semua tampil dan punya teks pengganti', async ({ page }) => {
    await bukaBeranda(page);
    await page.evaluate(async () => {
      for (let y = 0; y < document.body.scrollHeight; y += 500) { window.scrollTo(0, y); await new Promise((r) => setTimeout(r, 120)); }
      window.scrollTo(0, 0);
    });
    await page.waitForTimeout(800);
    const hasil = await page.evaluate(() => [...document.querySelectorAll('.page-shell img')].map((i) => ({ src: i.currentSrc.replace(/^https?:\/\/[^/]+/, ''), ok: i.complete && i.naturalWidth > 0, alt: i.hasAttribute('alt') })));
    const rusak = hasil.filter((h) => !h.ok);
    const tanpaAlt = hasil.filter((h) => !h.alt);
    note(`${hasil.length} gambar diperiksa. Gambar yang gagal tampil: ${rusak.length}. Gambar tanpa teks pengganti: ${tanpaAlt.length}.`);
    expect(rusak).toEqual([]);
    expect(tanpaAlt).toEqual([]);
  });

  test('T14 Tampilan di empat ukuran layar tanpa geser ke samping', async ({ page }) => {
    const baris = [];
    for (const [lebar, tinggi] of [[375, 812], [768, 1024], [1366, 768], [1920, 1080]]) {
      await page.setViewportSize({ width: lebar, height: tinggi });
      await bukaBeranda(page);
      const ukur = await page.evaluate(() => ({
        sw: document.documentElement.scrollWidth,
        cw: document.documentElement.clientWidth,
        zoom: getComputedStyle(document.querySelector('.site-frame')).zoom,
        menu: getComputedStyle(document.querySelector('#gn-nav')).display,
      }));
      baris.push(`Lebar ${lebar} px: lebar halaman ${ukur.sw} dari ${ukur.cw} px, perbesaran ${Number(ukur.zoom).toFixed(2)} kali, menu ${ukur.menu === 'none' ? 'berupa tombol' : 'tampil lengkap'}`);
      expect(ukur.sw, `halaman bergeser ke samping pada lebar ${lebar} px`).toBeLessThanOrEqual(ukur.cw);
      await shot(page, 'T14', `layar-${lebar}`, `Tampilan awal pada lebar ${lebar} px.`);
    }
    note(baris.join('. ') + '.');
  });

  test('T15 Ponsel: tombol menu dan tampilan halaman penuh', async ({ page }) => {
    await page.setViewportSize({ width: 375, height: 812 });
    await bukaBeranda(page);
    await expect(page.locator('.gn-burger')).toBeVisible();
    await expect(page.locator('.gn-nav')).toBeHidden();
    await page.locator('.gn-burger').click();
    await expect(page.locator('.gn-nav')).toHaveClass(/is-open/);
    await expect(page.locator('.gn-burger')).toHaveAttribute('aria-expanded', 'true');
    await expect(page.locator('.gn-nav')).toBeVisible();
    await shot(page, 'T15', 'ponsel-menu', 'Menu terbuka di ponsel (lebar 375 px).');
    await page.locator('.gn-burger').click();
    await expect(page.locator('.gn-nav')).toBeHidden();
    await shot(page, 'T15', 'ponsel-halaman-penuh', 'Seluruh halaman di ponsel (lebar 375 px).', { fullPage: true });
    note('Teks kecil "Kementerian Ketenagakerjaan RI" di bawah nama perpustakaan sengaja disembunyikan di ponsel agar muat (sesuai desain).');
  });

  test('T19 Tombol Masuk', async ({ page }) => {
    await bukaBeranda(page);
    const masuk = page.locator('.gn-actions .gn-login');
    await expect(masuk).toBeVisible();
    await expect(masuk).toHaveText('Masuk');
    await expect(masuk).toHaveAttribute('href', /\/login$/);
    const tinggi = (await masuk.boundingBox()).height;
    note(`Tombol "Masuk" terlihat di bilah menu, mengarah ke /login, tingginya ${Math.round(tinggi)} px.`);
    expect(tinggi).toBeGreaterThanOrEqual(44);
    await page.setViewportSize({ width: 375, height: 812 });
    await bukaBeranda(page);
    const kotak = await page.evaluate(() => {
      const r = (q) => document.querySelector(q).getBoundingClientRect();
      const m = r('.gn-login'), b = r('.gn-burger'), l = r('.gn-brand');
      return { masukKanan: m.right, burgerKiri: b.left, brandKanan: l.right, masukKiri: m.left, lebar: document.documentElement.clientWidth };
    });
    note(`Di ponsel (375 px): logo berakhir di ${Math.round(kotak.brandKanan)} px, tombol Masuk ${Math.round(kotak.masukKiri)}-${Math.round(kotak.masukKanan)} px, tombol menu mulai ${Math.round(kotak.burgerKiri)} px.`);
    expect(kotak.brandKanan, 'logo dan Masuk tidak bertumpuk').toBeLessThanOrEqual(kotak.masukKiri);
    expect(kotak.masukKanan, 'Masuk dan tombol menu tidak bertumpuk').toBeLessThanOrEqual(kotak.burgerKiri + 1);
  });

  test('T16 Kemudahan akses: penanda posisi saat memakai keyboard', async ({ page }) => {
    await bukaBeranda(page);
    const urutan = [];
    for (let i = 0; i < 6; i++) {
      await page.keyboard.press('Tab');
      await page.waitForTimeout(450); // tunggu transisi warna selesai
      urutan.push(await page.evaluate(() => {
        const parse = (c) => { const m = c.match(/rgba?\(([^)]+)\)/); const p = m[1].split(/[ ,/]+/).filter(Boolean).map(Number); return { r: p[0], g: p[1], b: p[2], a: p.length > 3 ? p[3] : 1 }; };
        const lum = (c) => { const f = (v) => { v /= 255; return v <= 0.03928 ? v / 12.92 : Math.pow((v + 0.055) / 1.055, 2.4); }; return 0.2126 * f(c.r) + 0.7152 * f(c.g) + 0.0722 * f(c.b); };
        const kontrasTerhadapPutih = (warna) => {
          const c = parse(warna);
          const campur = { r: c.r * c.a + 255 * (1 - c.a), g: c.g * c.a + 255 * (1 - c.a), b: c.b * c.a + 255 * (1 - c.a) };
          const [x, y] = [lum(campur), lum({ r: 255, g: 255, b: 255 })];
          return Math.round(((Math.max(x, y) + 0.05) / (Math.min(x, y) + 0.05)) * 100) / 100;
        };
        const e = document.activeElement;
        const cs = getComputedStyle(e);
        const punyaOutline = cs.outlineStyle !== 'none' && parseFloat(cs.outlineWidth) > 0;
        const nama = `${e.tagName.toLowerCase()}${e.className ? '.' + String(e.className).split(' ')[0] : ''}`;
        const teksAsli = (e.innerText || e.getAttribute('aria-label') || '').trim().replace(/\s+/g, ' ').slice(0, 24);
        const teks = e.tagName === 'INPUT' ? 'Kolom pencarian' : e.classList.contains('brand') ? 'Logo dan nama perpustakaan' : teksAsli;
        if (punyaOutline) return { el: nama, teks, jenis: cs.outlineStyle === 'auto' ? 'cincin penanda bawaan peramban' : `cincin penanda ${cs.outlineWidth}`, kontras: null, cukup: true };
        // Tanpa outline: indikator fokus ada pada pembungkus (kolom pencarian) lewat warna garis tepi.
        const pembungkus = e.closest('.catalog-search');
        if (pembungkus) {
          const pc = getComputedStyle(pembungkus);
          if (pc.outlineStyle !== 'none' && parseFloat(pc.outlineWidth) > 0) {
            return { el: nama, teks, jenis: `cincin penanda biru pada kolom pencarian (${pc.outlineWidth})`, kontras: kontrasTerhadapPutih(pc.outlineColor), cukup: kontrasTerhadapPutih(pc.outlineColor) >= 3 };
          }
          const rasio = kontrasTerhadapPutih(pc.borderTopColor);
          return { el: nama, teks, jenis: 'hanya warna garis tepi kolom pencarian berubah', kontras: rasio, cukup: rasio >= 3 };
        }
        return { el: nama, teks, jenis: 'tidak ada', kontras: null, cukup: false };
      }));
    }
    data({ urutanTab: urutan });
    note('Urutan perpindahan dengan tombol Tab: ' + urutan.map((u, i) => `${i + 1}. ${u.teks || u.el} (${u.jenis}${u.kontras !== null ? ', kontras penanda ' + String(u.kontras).replace('.', ',') + ':1' : ''})`).join('; ') + '.');
    const lemah = urutan.filter((u) => !u.cukup);
    note(`Bagian yang penandanya kurang jelas (kontras di bawah 3:1 atau tidak ada): ${lemah.length}.`);
    await shot(page, 'T16', 'fokus-keyboard', 'Penanda posisi saat menekan Tab: menu memakai cincin bawaan peramban, kolom pencarian memakai cincin biru.', { clip: { x: 0, y: 0, width: 1366, height: 140 } });
    await page.locator('#omnisearch-input').focus();
    await page.waitForTimeout(450);
    const kotak = await page.locator('.catalog-search').boundingBox();
    await shot(page, 'T16', 'fokus-pencarian', 'Penanda posisi pada kolom pencarian saat dipilih dengan keyboard.', { clip: { x: Math.max(0, kotak.x - 24), y: Math.max(0, kotak.y - 24), width: kotak.width + 48, height: kotak.height + 48 } });
    expect(lemah.map((u) => `${u.el} (${u.jenis}, kontras ${u.kontras})`), 'penanda posisi keyboard kurang jelas').toEqual([]);
  });

  test('T17 Kemudahan akses: keterbacaan warna teks (kontras)', async ({ page }) => {
    await bukaBeranda(page);
    await page.evaluate(async () => {
      for (let y = 0; y < document.body.scrollHeight; y += 500) { window.scrollTo(0, y); await new Promise((r) => setTimeout(r, 80)); }
      window.scrollTo(0, 0);
    });
    const daftar = DAFTAR_KONTRAS;
    const hasil = await hitungKontras(page, daftar);
    const gagal = hasil.filter((h) => h.ada && h.rasio < h.batas);
    data({ kontras: hasil.filter((h) => h.ada) });
    note('Rasio kontras mengukur seberapa jelas teks terbaca di atas latarnya. Batas yang dipakai: 4,5 banding 1 untuk teks kecil dan 3 banding 1 untuk teks besar (pedoman WCAG AA). Rincian setiap teks ada pada tabel.');
    note(`Jumlah teks yang masih di bawah batas: ${gagal.length}.`);
    await shot(page, 'T17', 'kontras', 'Teks abu-abu pendamping (paragraf bagian utama, keterangan buku) adalah yang paling dekat dengan batas kontras.', { clip: { x: 0, y: 0, width: 1366, height: 700 } });
    expect(gagal.map((g) => `${g.label} ${g.rasio}:1 (batas ${g.batas})`), 'teks yang kontrasnya di bawah batas').toEqual([]);
  });

  test('T18 Seluruh halaman sebagai ringkasan tampilan', async ({ page }) => {
    await bukaBeranda(page);
    await page.evaluate(async () => {
      for (let y = 0; y < document.body.scrollHeight; y += 500) { window.scrollTo(0, y); await new Promise((r) => setTimeout(r, 100)); }
      window.scrollTo(0, 0);
    });
    await page.waitForTimeout(600);
    const tinggi = await page.evaluate(() => document.documentElement.scrollHeight);
    note(`Tinggi seluruh halaman ${tinggi} px pada lebar 1366 px.`);
    await shot(page, 'T18', 'halaman-penuh', 'Seluruh beranda dari bagian utama sampai bagian bawah (lebar 1366 px).', { fullPage: true });
  });

  test('T22 Klik satu per satu tanpa pesan galat', async ({ page }) => {
    const galat = [];
    page.on('console', (m) => { if (m.type() === 'error') galat.push(m.text()); });
    page.on('pageerror', (e) => galat.push(String(e)));
    const catatan = [];
    {
      const nama = 'beranda';
      await bukaBeranda(page);

      await page.locator('.gn-drop-btn').first().click();
      await expect(page.locator('.gn-drop-btn').first()).toHaveAttribute('aria-expanded', 'true');
      await page.keyboard.press('Escape');
      await expect(page.locator('.gn-drop-btn').first()).toHaveAttribute('aria-expanded', 'false');
      catatan.push(`${nama}: menu turun Layanan membuka dan menutup (Esc)`);

      await page.locator('[data-suggest="K3"]').click();
      await expect(page.locator('#omnisearch-input')).toHaveValue('K3');
      await expect(page.locator('#omni-results .omni-option').last()).toContainText('di katalog OPAC', { timeout: 10000 });
      await page.keyboard.press('Escape');
      await expect(page.locator('#omni-results')).toBeHidden();
      await page.locator('#omnisearch-input').fill('');
      catatan.push(`${nama}: saran "K3" mengisi kolom dan hasil muncul lalu menutup (Esc)`);

      await page.getByRole('tab', { name: 'Populer' }).click();
      await expect(page.locator('[data-panel="populer"]')).toBeVisible();
      await page.getByRole('tab', { name: 'Terbaru' }).click();
      await expect(page.locator('[data-panel="terbaru"]')).toBeVisible();
      catatan.push(`${nama}: pilihan Populer lalu Terbaru berganti`);

      const kartu = page.locator('.shortcut-card').first();
      await kartu.scrollIntoViewIfNeeded();
      await kartu.hover();
      await page.waitForTimeout(400);
      const warnaTeks = await kartu.evaluate((e) => getComputedStyle(e.querySelector('strong')).color);
      expect(warnaTeks, 'teks kartu layanan terbaca saat kursor diarahkan').not.toBe('rgb(255, 255, 255)');
      catatan.push(`${nama}: kartu layanan saat kursor diarahkan memakai teks gelap (${warnaTeks})`);

      await page.locator('[data-rules]').first().click();
      await expect(page.locator('#rules-modal .rules-chapter').first()).toBeVisible({ timeout: 10000 });
      await page.keyboard.press('Escape');
      await expect(page.locator('#rules-modal')).toHaveCount(0);
      catatan.push(`${nama}: jendela Tata Tertib membuka dan menutup (Esc)`);

      await page.locator('.gn-login').hover();
      await expect(page.locator('.gn-login')).toBeVisible();
      catatan.push(`${nama}: tombol "Masuk" terlihat dan dapat disorot`);
      await page.context().clearCookies();
    }
    note('Urutan klik: ' + catatan.join('; ') + '.');
    note(`Pesan galat di konsol peramban selama seluruh klik: ${galat.length}.`);
    expect(galat, 'pesan galat di konsol').toEqual([]);
  });

  test('T24 Efek kaca dibatasi: bilah menu, dua kartu melayang, dan latar jendela', async ({ page }) => {
    await bukaBeranda(page);
    const hitung = () => page.evaluate(() => [...document.querySelectorAll('body *')].filter((e) => { const v = getComputedStyle(e).backdropFilter; return v && v !== 'none'; }).map((e) => `${e.tagName.toLowerCase()}.${String(e.className).split(' ')[0]}`));
    const awal = await hitung();
    await page.evaluate(() => window.scrollTo({ top: 600, behavior: 'instant' }));
    const header = await page.evaluate(() => getComputedStyle(document.querySelector('.gn-bar'), '::before').backdropFilter);
    await page.locator('[data-rules]').first().click();
    await expect(page.locator('#rules-modal')).toBeVisible();
    const saatJendela = await hitung();
    note(`Elemen dengan efek kaca saat halaman biasa: ${awal.length} (ditambah lapisan kaca bilah menu: ${header}). Saat jendela Tata Tertib terbuka: ${saatJendela.join(', ') || 'tidak ada'}.`);
    expect(awal.length, 'hanya dua kartu melayang yang berkaca di halaman biasa').toBeLessThanOrEqual(2);
    expect(header).toMatch(/blur|lens-filter/);
    expect(saatJendela.length, 'jendela menambah paling banyak satu elemen berkaca (dua kartu sudah termasuk)').toBeLessThanOrEqual(3);
  });

  test('T26 Target sentuh: tombol dan tautan minimal 44 px di desktop dan ponsel', async ({ page }) => {
    for (const [lebar, tinggi] of [[1366, 768], [375, 812]]) {
      await page.setViewportSize({ width: lebar, height: tinggi });
      await bukaBeranda(page);
      const kecil = await page.evaluate(() => [...document.querySelectorAll('.page-shell a, .page-shell button, .page-shell input')]
        .filter((e) => !e.matches('.hero-dot') && !e.closest('[hidden]') && getComputedStyle(e).visibility !== 'hidden')
        .map((e) => ({ nama: `${e.tagName.toLowerCase()}.${String(e.className).split(' ')[0]} "${(e.getAttribute('aria-label') || e.textContent).trim().slice(0, 20)}"`, r: e.getBoundingClientRect() }))
        .filter(({ r }) => r.width && r.height && (r.width < 44 || r.height < 44))
        .map(({ nama, r }) => `${nama} ${Math.round(r.width)}x${Math.round(r.height)}`));
      note(`Lebar ${lebar} px: ${kecil.length} target di bawah 44 px${kecil.length ? ': ' + kecil.join('; ') : ''}. Titik carousel foto (44 x 30) dikecualikan: fotonya juga bisa digeser dan dipilih dengan tombol panah.`);
      expect(kecil, `target sentuh kurang dari 44 px pada lebar ${lebar} px`).toEqual([]);
    }
  });

  test('T27 Pencarian: keadaan "mencari" dan pesan gagal saat server tidak menjawab', async ({ page }) => {
    await page.route('**/home/search-books*', (route) => route.abort());
    await bukaBeranda(page);
    await page.locator('#omnisearch-input').fill('k3');
    await expect(page.locator('.omni-status')).toContainText('Buku tidak dapat dimuat', { timeout: 5000 });
    await expect(page.locator('#omni-results .omni-option').last()).toContainText('di katalog OPAC');
    note('Saat permintaan buku gagal, baris "Buku tidak dapat dimuat" muncul dan tautan ke katalog OPAC tetap ada.');
  });

  test('T25 Tulisan putih di atas foto terbaca di titik terterang', async ({ page }) => {
    await bukaBeranda(page);
    await page.evaluate(() => {
      document.querySelectorAll('.floating-card').forEach((e) => { e.style.animation = 'none'; e.style.visibility = 'hidden'; });
      document.querySelectorAll('.image-caption strong, .image-caption span').forEach((e) => { e.style.visibility = 'hidden'; });
    });
    const bagian = await page.evaluate(() => [...document.querySelectorAll('.image-caption strong, .image-caption span')].filter((e) => getComputedStyle(e).display !== 'none').map((e) => { const r = e.getBoundingClientRect(); return { nama: e.tagName === 'SPAN' ? 'label "01 / Ruang Baca"' : 'kalimat "Tempat ide bertemu dan bertumbuh."', x: r.left, y: r.top, width: Math.max(r.width, 2), height: Math.max(r.height, 2) }; }));
    const q = await page.context().newPage();
    const hasil = [];
    for (const g of bagian) {
      const buf = await page.screenshot({ clip: { x: g.x, y: g.y, width: g.width, height: g.height } });
      const rasio = await q.evaluate(async (b64) => {
        const img = new Image(); img.src = 'data:image/png;base64,' + b64; await img.decode();
        const c = document.createElement('canvas'); c.width = img.width; c.height = img.height; const gg = c.getContext('2d'); gg.drawImage(img, 0, 0);
        const d = gg.getImageData(0, 0, c.width, c.height).data; const f = (v) => { v /= 255; return v <= 0.03928 ? v / 12.92 : Math.pow((v + 0.055) / 1.055, 2.4); };
        let maks = 0; for (let i = 0; i < d.length; i += 4) { maks = Math.max(maks, 0.2126 * f(d[i]) + 0.7152 * f(d[i + 1]) + 0.0722 * f(d[i + 2])); }
        return Math.round((1.05 / (maks + 0.05)) * 100) / 100;
      }, buf.toString('base64'));
      hasil.push({ nama: g.nama, rasio });
    }
    await q.close();


    note('Kontras tulisan putih terhadap titik terterang di area tulisan pada foto: ' + hasil.map((h) => `${h.nama} ${String(h.rasio).replace('.', ',')} banding 1`).join('; ') + '. Batas 4,5 banding 1.');
    for (const h of hasil) expect(h.rasio, `kontras ${h.nama}`).toBeGreaterThanOrEqual(4.5);
  });
});
