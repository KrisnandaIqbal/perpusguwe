# Changelog Session — INLISLite v33

Tanggal: 2026-05-21

---

## 1. Anggota — Hapus insert user di `create()`

**File:** `app/Modules/SubModule/Keanggotaan/Anggota/Controllers/Anggota.php`

**Masalah:** Method `create()` menyimpan data ke tabel `users` secara otomatis saat anggota dibuat, tidak diinginkan.

**Before:**
```php
$password = get_parameter('password-default', 'inlislite=');
$activate_hash = bin2hex(random_bytes(16));
$db = db_connect('default');
$users = $db->table('users');
$data_user = [
    'username'      => $MemberNo,
    'password_hash' => $password,
    'anggota'       => $newAnggotaId,
    'email'         => $save_data['Email'],
    'activate_hash' => $activate_hash,
    'active'        => 0
];
$users->insert($data_user);
```

**After:** Seluruh blok dihapus.

---

## 2. Anggota — Fix `aktifkan_online()` tidak insert ke tabel users

**File:** `app/Modules/SubModule/Keanggotaan/Anggota/Controllers/Anggota.php`

**Masalah:**
- `$userModel->insert()` gagal diam-diam karena validasi model menolak field
- Transaksi `transBegin/transCommit` tidak mencakup operasi `$userModel` (koneksi berbeda)
- Jika user sudah ada (dengan `active=0`), langsung di-skip tanpa diperbaiki
- Tidak ada notifikasi username/password untuk anggota yang berhasil diaktifkan

**Before:**
```php
$db->transBegin();
// ...
$userModel = new \User\Models\UserModel();
$existingUser = $userModel->where('username', $member->MemberNo)
                          ->orWhere('email', $member->Email)->first();
if ($existingUser) { $existingCount++; continue; }
// ...
if ($userModel->insert($userData)) { ... }
$db->transCommit();
```

**After:**
```php
// Pakai db_connect('default') langsung, tanpa transaksi
$existingUser = $db->table('users')->where('username', $member->MemberNo)->get()->getRow();
if ($existingUser) {
    // Update active=1 dan pastikan auth_groups_users ada
    $db->table('users')->where('id', $userId)->update(['active' => 1]);
    // ...
    continue;
}
// Insert langsung via $db->table('users')->insert()
// Notifikasi includes: Username & Password = No Anggota
```

---

## 3. Filters — Fix infinite redirect untuk user group anggota/layanan

**File:** `app/Config/Filters.php`

**Masalah:** `Permissions` filter menolak akses ke `dashboard/index` → redirect ke `dashboard/index` → filter menolak lagi → ERR_TOO_MANY_REDIRECTS.

**Before:**
```php
'permissions' => ['except' => [
    '', 'logout*', 'oai*', 'login*', ...
]],
```

**After:**
```php
'permissions' => ['except' => [
    '', 'logout*', 'oai*', 'login*',
    'dashboard*',   // <-- ditambahkan
    ...
]],
```

---

## 4. Navbar online anggota — Teks putih

**File:** `app/Modules/SubModule/Keanggotaan/Anggota/Views/online/nav_list.php`

**Masalah:** Teks navbar tidak terlihat karena warna default mengikuti tema (gelap di background gelap).

**Before:** Tidak ada style override.

**After:**
```css
.body-tabs-animated .nav-link,
.body-tabs-animated .nav-link span,
.body-tabs-animated .nav-link i {
    color: #fff !important;
}
```

---

## 5. User API — Fix nama kolom `location_library_ids` → `location_ids`

**File:** `app/Modules/Core/User/Controllers/Api/User.php`

**Masalah:** Kolom di database bernama `location_ids` tapi kode menggunakan `location_library_ids` → `DatabaseException: Unknown column`.

**Before:**
```php
$update_data['location_library_ids'] = ...;
// dan
'location_library_ids' => $location_ids_str
```

**After:**
```php
$update_data['location_ids'] = ...;
// dan
'location_ids' => $location_ids_str
```

---

## 6. User controller — Auto-redirect ke group pertama

**File:** `app/Modules/Core/User/Controllers/User.php`

**Masalah:** Saat membuka `/user/index` tanpa `?slug=`, tidak ada tab aktif dan tabel kosong.

**Before:** `$slug` dibiarkan kosong jika tidak ada query string.

**After:**
```php
if (empty($slug) && !empty($groups)) {
    return redirect()->to(base_url('user/index?slug=' . $groups[0]->name));
}
```

---

## 7. User Helper — Tambah group `layanan` ke daftar navigasi

**File:** `app/Modules/Core/User/Helpers/user_helper.php`

**Masalah:** Group `layanan` tidak muncul di tab navbar user meski sudah ada di database.

**Before:**
```php
if (is_member('admin')) {
    $include = ['admin', 'sa_prov', ..., 'anggota', 'kataloger'];
}
```

**After:**
```php
if (is_member('admin')) {
    $include = ['admin', 'sa_prov', ..., 'anggota', 'kataloger', 'layanan'];
}
// + 'layanan' ditambahkan ke semua level (sa_prov, sa_kabkot, sa_pkpt, sa_psm)
```

---

## 8. Menu Helper — Fix menu tidak sesuai permission group non-admin

**File:** `app/Modules/Core/Menu/Helpers/menu_helper.php` — fungsi `display_menu_backend()`

**Masalah:** INNER JOIN di SQL mengharuskan setiap item menu (termasuk label/section header) punya permission `/access`. Label tidak punya permission → seluruh section diblokir → menu kosong untuk group selain admin.

**Before:**
```sql
FROM c_menus cm
INNER JOIN auth_permissions ap
    ON cm.controller = REPLACE(ap.name, '/access', '')
INNER JOIN auth_groups_permissions agp ON ap.id = agp.permission_id
INNER JOIN auth_groups ag ON ag.id = agp.group_id
WHERE ag.name = ? AND ap.name LIKE '%/access%'
-- childs: COUNT semua anak tanpa filter permission
```

**After:**
```sql
FROM c_menus cm
WHERE (
    -- Item biasa: harus punya permission untuk group ini
    (cm.type != 'label' AND EXISTS (SELECT 1 ... ag.name = ?))
    OR
    -- Label/section: tampil jika minimal 1 anak punya permission
    (cm.type = 'label' AND EXISTS (SELECT 1 FROM c_menus c2 ... ag2.name = ?))
)
-- childs: COUNT hanya anak yang punya permission untuk group ini
```

---

## 9. User update_modal — Fix lokasi tidak ter-load & SweetAlert

**File:** `app/Modules/Core/User/Views/update_modal.php`

**Masalah:**
- `$user->location_library_ids` tidak ada (kolom sudah di-rename ke `location_ids`)
- SweetAlert pakai `type` (SweetAlert v1) bukan `icon` (SweetAlert2) → ikon tidak muncul
- `setTimeout` redirect bisa tabrakan dengan timer Swal

**Before:**
```php
$selected_locations = !empty($user->location_library_ids) ? explode(',', $user->location_library_ids) : [];
```
```js
Swal.fire({ type: 'success', timer: 3000 });
setTimeout(function() { window.location.href = '...'; }, 2000);
```

**After:**
```php
$selected_locations = !empty($user->location_ids) ? explode(',', $user->location_ids) : [];
```
```js
Swal.fire({ icon: 'success', timer: 3000 }).then(function() {
    window.location.href = '...';
});
```

---

## 10. User add_modal — Fix SweetAlert ikon tidak muncul

**File:** `app/Modules/Core/User/Views/add_modal.php`

**Masalah:** Sama seperti update_modal — `type` (v1) bukan `icon` (v2), dan `setTimeout` terpisah.

**Before:**
```js
Swal.fire({ title: 'Success', type: 'success', timer: 3000 });
setTimeout(function() { window.location.href = '...'; }, 2000);
```

**After:**
```js
Swal.fire({ title: 'Berhasil', icon: 'success', timer: 3000 }).then(function() {
    window.location.href = '...';
});
```

---

## 11. Group list — DataTable DOM & pagination standar

**File:** `app/Modules/Core/Group/Views/list.php`

**Masalah:** DataTable menggunakan `setDataTable()` dengan style berbeda dari halaman lain.

**After:** Diganti dengan inisialisasi penuh menggunakan DOM layout, `pagingType: "full_numbers"`, `oLanguage` dengan ikon FontAwesome, dan `drawCallback` untuk auto-numbering.

---

## 12. Login — Tahun footer dinamis

**File:** `app/Modules/Core/Auth/Views/login.php`

**Before:**
```php
<?= get_parameter('site-copyright', '&copy; 2023 Perpustakaan Nasioanl RI') ?>
```

**After:**
```php
<?= get_parameter('site-copyright', '&copy; ' . date('Y') . ' Perpustakaan Nasioanl RI') ?>
```

---

## 13. Sumbangan list — Fix DataTable column index error

**File:** `app/Modules/SubModule/Keanggotaan/Sumbangan/Views/list.php`

**Masalah:** Tabel punya 7 kolom (index 0–6) tapi `disableOrderCols = [0, 7]` → DataTables warning "unknown parameter '7'". `defaultOrderCols = [6, 'desc']` mencoba sort kolom Aksi yang non-orderable.

**Before:**
```js
setDataTable('#tbl_sumbangans', disableOrderCols = [0, 7], defaultOrderCols = [6, 'desc'], autoNumber = true);
```

**After:**
```js
setDataTable('#tbl_sumbangans', disableOrderCols = [0, 6], defaultOrderCols = [1, 'asc'], autoNumber = true);
```

---

## 14. Sumbangan add — Fix halaman putih

**File:** `app/Modules/SubModule/Keanggotaan/Sumbangan/Views/add.php`

**Masalah:** `section('page')` dibuka **dua kali** dan `section('style')` diletakkan di dalam `section('page')` → CI4 template engine crash diam-diam → halaman putih.

**Before:**
```php
<?= $this->extend('App\Views\layout\main'); ?>
<?= $this->section('page'); ?>      // ← dibuka pertama kali (SALAH)
<?= $this->section('style'); ?>
...
<?= $this->endSection('style'); ?>
<?= $this->section('page'); ?>      // ← dibuka lagi (DUPLIKAT)
```

**After:**
```php
<?= $this->extend('App\Views\layout\main'); ?>
<?= $this->section('style'); ?>     // ← style dulu
...
<?= $this->endSection('style'); ?>
<?= $this->section('page'); ?>      // ← baru page, sekali saja
```
