# Tes E2E (Playwright)

Folder ini terpisah dari tes PHPUnit di `tests/unit`, `tests/feature`, dll.

```bash
npx playwright test              # jalankan semua
npx playwright test --ui         # mode interaktif
npx playwright show-report       # buka laporan HTML terakhir
```

Alamat situs default `http://perpusguwe-main.test/`. Ganti dengan:

```bash
E2E_BASE_URL=http://localhost/ npx playwright test
```

Laragon harus sudah jalan sebelum tes dijalankan.

## Pengujian halaman depan dan laporan PDF

Berkas `landing.spec.js` menguji halaman depan (24 pengujian). Gambar layar tersimpan di `docs/laporan-uji-landing/img`.
Jalankan dengan satu proses uji agar urutan dan gambar stabil, lalu buat laporan PDF:

```bash
PLAYWRIGHT_JSON_OUTPUT_NAME=docs/laporan-uji-landing/hasil.json npx playwright test tests/e2e/landing.spec.js --workers=1 --reporter=list,json
node tests/e2e/buat-laporan.js
```

Hasilnya: `docs/laporan-uji-landing/laporan-uji-landing.pdf` (dan `.html`). Laragon harus menyala sebelum pengujian dijalankan.

Pengujian T16 (penanda posisi keyboard) dan T17 (keterbacaan warna teks) menjaga agar teks tetap cukup jelas dan penanda keyboard
tetap terlihat. Bila salah satunya gagal setelah perubahan tampilan, lihat tabel pada bagian 3 laporan untuk bagian mana yang kurang.
