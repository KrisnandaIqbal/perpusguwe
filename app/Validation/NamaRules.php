<?php

namespace App\Validation;

/**
 * Aturan validasi nama orang (anggota perpustakaan).
 *
 * Hanya huruf A-Z, spasi, dan tanda baca yang lazim di nama: titik (M. Iqbal), koma (gelar),
 * apostrof (Ma'ruf, juga ’ dari keyboard ponsel), dan tanda hubung (Siti-Nur).
 * Huruf hias Unicode seperti ℒ atau ℰ ikut ditolak walaupun tergolong huruf, karena tidak bisa
 * dicari, diurutkan, atau dicetak dengan benar di kartu.
 */
class NamaRules
{
    /** Pola yang sama dipakai atribut pattern di form (tanpa delimiter dan flag) */
    public const POLA = "[A-Za-z][A-Za-z .,'’\\-]*";

    public function nama_orang(?string $str = null): bool
    {
        return (bool) preg_match('/^' . self::POLA . '$/u', trim((string) $str));
    }
}
