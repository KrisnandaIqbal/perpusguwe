<?php

namespace Rekomendasi\Libraries;

/** Penyusun URL pencarian Indonesia OneSearch (onesearch.id). Fungsi murni, tanpa akses jaringan. */
class OneSearch
{
    private const BASE = 'https://onesearch.id/Search/Results';

    // search_by di OPAC -> type di OneSearch. Hanya AllFields yang sudah dicek ke situsnya;
    // sisanya nilai bawaan VuFind. Penerbit dan DDC tidak punya padanan, jatuh ke AllFields.
    private const TYPE = [
        'Title'  => 'Title',
        'Author' => 'Author',
        'Subject' => 'Subject',
        'ISBN'   => 'ISN',
    ];

    public static function type(?string $searchBy): string
    {
        return self::TYPE[(string) $searchBy] ?? 'AllFields';
    }

    public static function url(string $query, ?string $searchBy = null): string
    {
        return self::BASE . '?' . http_build_query([
            'lookfor' => trim($query),
            'type'    => self::type($searchBy),
        ], '', '&', PHP_QUERY_RFC3986);
    }
}
