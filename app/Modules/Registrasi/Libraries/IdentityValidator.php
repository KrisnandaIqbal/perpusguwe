<?php

namespace Registrasi\Libraries;

class IdentityValidator
{
    /**
     * Normalizes an Indonesian phone number.
     * Converts +62 or 62 to 0 and ensures only digits remain.
     */
    public static function normalizePhone($phone)
    {
        $phone = preg_replace('/[^0-9]/', '', $phone);
        
        if (substr($phone, 0, 2) === '62') {
            $phone = '0' . substr($phone, 2);
        }
        
        return $phone;
    }

    /**
     * Validates if a phone number length is between 10 and 15 digits
     * and starts with 08.
     */
    public static function isValidPhone($phone)
    {
        $phone = self::normalizePhone($phone);
        return preg_match('/^08[0-9]{8,13}$/', $phone);
    }

    /**
     * Validates if NIP is exactly 18 digits.
     */
    public static function isValidNIP($nip)
    {
        return preg_match('/^[0-9]{18}$/', $nip);
    }

    /**
     * Validates if NIK is exactly 16 digits.
     */
    public static function isValidNIK($nik)
    {
        return preg_match('/^[0-9]{16}$/', $nik);
    }
}
