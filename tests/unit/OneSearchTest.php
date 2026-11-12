<?php

use CodeIgniter\Test\CIUnitTestCase;
use Rekomendasi\Libraries\OneSearch;

/** @internal */
final class OneSearchTest extends CIUnitTestCase
{
    /** @dataProvider petaProvider */
    public function testType(?string $searchBy, string $harapan)
    {
        $this->assertSame($harapan, OneSearch::type($searchBy));
    }

    public function petaProvider(): array
    {
        return [
            'judul'    => ['Title', 'Title'],
            'pengarang' => ['Author', 'Author'],
            'subjek'   => ['Subject', 'Subject'],
            'isbn'     => ['ISBN', 'ISN'],
            'penerbit' => ['Publisher', 'AllFields'],
            'ddc'      => ['DeweyNo', 'AllFields'],
            'semua'    => ['', 'AllFields'],
            'null'     => [null, 'AllFields'],
            'asing'    => ['Foo', 'AllFields'],
        ];
    }

    public function testUrlSemuaKolom()
    {
        $this->assertSame(
            'https://onesearch.id/Search/Results?lookfor=laskar%20pelangi&type=AllFields',
            OneSearch::url('laskar pelangi')
        );
    }

    public function testUrlMengeluarkanKarakterKhusus()
    {
        $this->assertSame(
            'https://onesearch.id/Search/Results?lookfor=a%26b%3Dc%20%23d&type=Title',
            OneSearch::url('  a&b=c #d ', 'Title')
        );
    }

    public function testUrlIsbn()
    {
        $this->assertSame(
            'https://onesearch.id/Search/Results?lookfor=9789793062792&type=ISN',
            OneSearch::url('9789793062792', 'ISBN')
        );
    }
}
