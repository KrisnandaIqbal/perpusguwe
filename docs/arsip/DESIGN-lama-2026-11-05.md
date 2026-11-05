---
version: alpha
name: Perpustakaan Kementerian Ketenagakerjaan
description: Architectural minimalism meets journalistic gravitas with Kemnaker clarity.
dials:
  energy: 2
  rhythm: 2
  motion: 1
colors:
  primary: "#0F3FB4"
  secondary: "#F0F4F9"
  surface: "#FFFFFF"
  text-dark: "#111827"
  text-muted: "#475569"
  tertiary: "#FF914D"
  tertiary-alt: "#F2A93B"
  on-primary: "#FFFFFF"
  on-tertiary: "#111827"
  border: "#CBD5E1"
  border-interactive: "#64748B"
  focus-ring: "#0F3FB4"
typography:
  h1:
    fontFamily: "Plus Jakarta Sans"
    fontSize: 3rem
    lineHeight: 1.2
    fontWeight: 700
  h2:
    fontFamily: "Plus Jakarta Sans"
    fontSize: 1.75rem
    lineHeight: 1.3
    fontWeight: 700
  body-md:
    fontFamily: "Plus Jakarta Sans"
    fontSize: 1rem
    lineHeight: 1.5
    fontWeight: 400
  body-editorial:
    fontFamily: "Lora"
    fontSize: 1.0625rem
    lineHeight: 1.7
    fontWeight: 400
  label-caps:
    fontFamily: "Plus Jakarta Sans"
    fontSize: 0.75rem
    lineHeight: 1.4
    fontWeight: 600
    letterSpacing: "0.04em"
rounded:
  sm: 4px
  md: 8px
spacing:
  sm: 8px
  md: 16px
  lg: 24px
components:
  button-primary:
    backgroundColor: "{colors.tertiary}"
    textColor: "{colors.on-tertiary}"
    rounded: "{rounded.sm}"
    padding: "12px 20px"
    minHeight: 44px
    typography: "{typography.body-md}"
    fontWeight: 600
  card:
    backgroundColor: "{colors.surface}"
    textColor: "{colors.text-dark}"
    borderColor: "{colors.border}"
    rounded: "{rounded.md}"
    padding: 20px
  input:
    backgroundColor: "{colors.surface}"
    textColor: "{colors.text-dark}"
    borderColor: "{colors.border-interactive}"
    focusRingColor: "{colors.focus-ring}"
    rounded: "{rounded.sm}"
    padding: "10px 14px"
    minHeight: 44px
---

# Perpustakaan Kementerian Ketenagakerjaan

## Design Read (Liveliness Dials)

Reading this as: institutional public library catalog and reader portal for Indonesian workers, researchers, and civil servants, in an architectural minimalist and journalistic broadsheet style, dial `ENERGY 2 / RHYTHM 2 / MOTION 1`.

- **ENERGY 2 (Balanced)**: Tegas dan berwibawa melalui blok identitas Vivid Blue (`#0F3FB4`), namun tetap tenang di area ruang baca.
- **RHYTHM 2 (Consistent with purposeful breaks)**: Grid katalog yang terstruktur dan dapat diprediksi, diselingi sorotan koleksi atau kutipan literasi menggunakan tipografi serif (`Lora`).
- **MOTION 1 (Hover and focus states only)**: Transisi fungsional pada `:hover` dan `:focus-visible` tanpa animasi berulang yang mengganggu konsentrasi membaca.

## Overview

Architectural minimalism meets journalistic gravitas. The UI evokes a matte broadsheet and a modern public reading room: combining Kemnaker clarity (`Plus Jakarta Sans`) with literary warmth (`Lora`) on cool, balanced reader surfaces.

## Colors (Prinsip 60-30-10)

Pemilihan warna didasarkan pada karakter dan citra yang ingin dibangun menggunakan **Prinsip 60-30-10**. Gunakan token semantik (misalnya `{colors.primary}`), jangan gunakan kode hex secara langsung saat membuat komponen.

### 60% Primary / Dominant Color
- **primary (`#0F3FB4`, Vivid Blue)**: Merepresentasikan identitas Kemnaker, kejelasan (clarity), teknologi modern, dan kepercayaan publik. Digunakan pada navigasi utama, header struktural, dan indikator fokus (`{colors.focus-ring}`).
- **on-primary (`#FFFFFF`, Pure White)**: Warna teks dan ikon di atas permukaan `{colors.primary}` (rasio kontras **8.79:1**, lulus WCAG AAA).

### 30% Secondary / Reader Base Color
- **secondary (`#F0F4F9`, Cool Off-White)**: Warna dasar ruang baca (reader base) untuk menyeimbangkan Vivid Blue dan menjaga kenyamanan mata saat membaca katalog berdurasi lama.
- **surface (`#FFFFFF`, Pure White)**: Permukaan kartu koleksi, kontainer baca, dan kolom input untuk mengangkat konten dari latar `{colors.secondary}`.

### 10% Text Dark & Structural Contrast
- **text-dark (`#111827`, Deep Slate / Ink)**: Warna tipografi utama, judul buku, pengumuman (announcements), dan informasi keterlibatan komunitas (rasio kontras **17.74:1** di atas `#FFFFFF` dan **16.06:1** di atas `#F0F4F9`, lulus WCAG AAA).
- **text-muted (`#475569`, Slate 600)**: Warna teks sekunder untuk metadata katalog seperti tahun terbit dan nomor panggil (rasio kontras **7.58:1** di atas `#FFFFFF` dan **6.86:1** di atas `#F0F4F9`, lulus WCAG AA/AAA).
- **border (`#CBD5E1`)**: Garis pemisah struktural halus antar kartu dan seksi.
- **border-interactive (`#64748B`)**: Garis batas untuk elemen form/input agar memenuhi batas kontras non-teks WCAG 1.4.11 minimal 3:1 (**4.66:1** di atas `#FFFFFF`, **4.22:1** di atas `#F0F4F9`).

### Opsional CTA / Penekanan (Accent)
- **tertiary (`#FF914D`, Vivid Orange)**: Aksen utama khusus untuk Call-to-Action (CTA) prioritas tertinggi seperti tombol pencarian katalog atau peminjaman.
- **tertiary-alt (`#F2A93B`, Energetic Amber / Gold)**: Aksen pendukung untuk penanda status koleksi penting atau sorotan pengumuman.
- **on-tertiary (`#111827`, Deep Slate / Ink)**: Warna teks wajib di atas `{colors.tertiary}` (rasio kontras **7.95:1**, lulus WCAG AAA) dan `{colors.tertiary-alt}` (rasio kontras **8.88:1**, lulus WCAG AAA). Jangan gunakan teks putih (`#FFFFFF`) di atas `#FF914D` karena rasio kontrasnya hanya **2.23:1** (gagal WCAG AA).

## Typography

Sistem tipografi menggunakan tepat **2 keluarga font** untuk memisahkan fungsi struktural UI dan pengalaman membaca literer:

1. **Font Utama (Sans-Serif): `Plus Jakarta Sans`**
   - **Karakter**: Identitas lokal (Jakarta sebagai pusat industri), modern (dirancang tahun 2020), dan bersih (mudah dibaca).
   - **Peran**: Headline (`h1`, `h2`), logo, judul halaman, navigasi, label metadata (`label-caps`), tabel katalog, input form, dan tombol (`body-md`).
2. **Font Sekunder (Serif): `Lora`**
   - **Karakter**: Literer (sesuai dengan konteks pustaka dan literasi), unik sebagai pembeda visual, serta memadukan kesan klasik dan modern.
   - **Peran**: Isi konten bacaan panjang (`body-editorial`), sinopsis/abstrak buku, artikel pengumuman perpustakaan, dan kutipan literasi.

| Token | Font | Size | Line Height | Weight | Peruntukan |
| --- | --- | --- | --- | --- | --- |
| `h1` | Plus Jakarta Sans | 3rem | 1.2 | 700 | Judul utama halaman / Hero |
| `h2` | Plus Jakarta Sans | 1.75rem | 1.3 | 700 | Judul seksi & kategori koleksi |
| `body-md` | Plus Jakarta Sans | 1rem | 1.5 | 400 | Teks UI, tabel katalog, form, tombol |
| `body-editorial` | Lora | 1.0625rem | 1.7 | 400 | Sinopsis buku, artikel, isi konten panjang |
| `label-caps` | Plus Jakarta Sans | 0.75rem | 1.4 | 600 | Kategori, nomor panggil, label status |

## Layout

Skala jarak (gunakan skala bernama, hindari nilai arbitrer):

- `spacing.sm`: 8px
- `spacing.md`: 16px
- `spacing.lg`: 24px

## Elevation & Depth

Kedalaman visual dibangun melalui pelapisan tonal (tonal layering) dan garis batas satu piksel, bukan bayangan tebal (drop shadows). Kartu (`{colors.surface}` / `#FFFFFF`) terangkat dari latar ruang baca (`{colors.secondary}` / `#F0F4F9`) melalui garis batas `{colors.border}`.

## Shapes

Skala sudut membulat (corner radius):

- `rounded.sm`: 4px (tombol, input, label status)
- `rounded.md`: 8px (kartu katalog, panel konten)

## Components

### button-primary
- backgroundColor: `{colors.tertiary}`
- textColor: `{colors.on-tertiary}`
- rounded: `{rounded.sm}`
- padding: `12px 20px`
- minHeight: `44px`
- typography: `{typography.body-md}` (fontWeight: `600`)

### card
- backgroundColor: `{colors.surface}`
- textColor: "{colors.text-dark}"
- borderColor: `{colors.border}`
- rounded: `{rounded.md}`
- padding: `20px`

### input
- backgroundColor: `{colors.surface}`
- textColor: `{colors.text-dark}`
- borderColor: `{colors.border-interactive}`
- focusRingColor: `{colors.focus-ring}`
- rounded: `{rounded.sm}`
- padding: `10px 14px`
- minHeight: `44px`

## Design Rationale (R-31 One-Line Reasons)

- **Why this color palette**: Mengikuti prinsip 60-30-10 Perpustakaan Kemnaker dengan Vivid Blue (`#0F3FB4`) sebagai identitas institusi, Cool Off-White (`#F0F4F9`) sebagai ruang baca bebas lelah mata, Deep Slate (`#111827`) untuk keterbacaan teks tajam, dan Vivid Orange (`#FF914D`) khusus untuk titik aksi utama.
- **Why Plus Jakarta Sans (Primary Font)**: Memberikan identitas lokal Indonesia yang modern dan bersih untuk elemen struktural seperti headline, navigasi, label, dan kontrol UI.
- **Why Lora (Secondary Font)**: Menghadirkan karakter literer yang memadukan nuansa klasik-modern untuk sinopsis buku dan bacaan panjang, sekaligus menjadi motif identitas (*identity motif*) yang membedakan perpustakaan ini dari dashboard generik.
- **Why this layout & spacing**: Skala kelipatan 8px (`8px`, `16px`, `24px`) menjaga ketertiban grid layaknya halaman surat kabar dan memudahkan pemindaian metadata bibliografi.
- **Why use cards**: Kartu putih (`#FFFFFF`) di atas latar `#F0F4F9` mengelompokkan metadata tiap buku (judul, pengarang, ketersediaan eksemplar) tanpa memerlukan bayangan dekoratif.
- **Why these radii (`4px` & `8px`)**: Sudut tegas `4px` pada kontrol interaktif dan `8px` pada kartu membedakan fungsi elemen (R-11) serta menghindari bentuk kapsul seragam khas template AI.

## Accessibility & Human Compatibility (R-03, R-25, R-27, R-32)

- **Kontras Warna Terverifikasi (R-25)**:
  - `#111827` di atas `#FFFFFF`: **17.74:1** (Lulus WCAG AAA)
  - `#111827` di atas `#F0F4F9`: **16.06:1** (Lulus WCAG AAA)
  - `#FFFFFF` di atas `#0F3FB4`: **8.79:1** (Lulus WCAG AAA)
  - `#0F3FB4` di atas `#F0F4F9`: **7.95:1** (Lulus WCAG AAA)
  - `#111827` di atas `#FF914D`: **7.95:1** (Lulus WCAG AAA)
  - `#111827` di atas `#F2A93B`: **8.88:1** (Lulus WCAG AAA)
  - `#64748B` (border input) di atas `#FFFFFF`: **4.66:1** (Lulus WCAG 1.4.11 non-text 3:1)
- **Navigasi Keyboard & Fokus (R-32)**: Setiap elemen interaktif wajib memiliki indikator `:focus-visible` dengan warna `{colors.focus-ring}` (`#0F3FB4`, `2px solid`, `outline-offset: 2px`). Dilarang menghapus outline fokus tanpa pengganti.
- **Target Sentuh & Zoom Mobile (R-03)**: Semua tombol dan input memiliki tinggi minimum `44px`, menggunakan unit relatif (`rem`) agar mendukung perbesaran teks hingga 200% tanpa terpotong.
- **Kelengkapan State Data (R-27)**: Setiap tampilan pencarian/katalog wajib menyertakan *empty state* (dengan penyebab dan saran tindakan), *loading state* berlabel teks, dan *error state* yang menjelaskan solusi.

## Do's and Don'ts

- **Do** terapkan rasio 60-30-10 secara disiplin: Vivid Blue untuk struktur identitas, Cool Off-White dan Pure White untuk area ruang baca, serta Deep Slate untuk teks.
- **Do** pisahkan peran kedua font secara tegas: gunakan `Plus Jakarta Sans` untuk headline, navigasi, tabel, dan kontrol UI; gunakan `Lora` khusus untuk konten literer seperti sinopsis buku, abstrak, dan artikel bacaan.
- **Do** gunakan warna tertiary (`#FF914D` / `#F2A93B`) secara hemat hanya pada aksi berprioritas tertinggi dan selalu pasangkan dengan teks gelap `{colors.on-tertiary}` (`#111827`).
- **Do** gunakan bahasa tindakan yang spesifik pada tombol (contoh: "Cari Koleksi", "Pinjam Buku", "Lihat Ketersediaan"), bukan label generik (R-15).
- **Don't** menggunakan karakter em dash dalam teks antarmuka maupun dokumentasi salinan (R-02).
- **Don't** menggabungkan lebih dari dua keluarga font (`Plus Jakarta Sans` dan `Lora`) dalam satu layar.
- **Don't** menggunakan `Lora` untuk teks berukuran kecil seperti label badge, tombol, atau header tabel karena kait serif mengurangi ketajaman di ukuran kecil.
- **Don't** menggunakan gambar lebar penuh (full-width) tanpa margin bawah minimal `spacing.lg` (`24px`).
