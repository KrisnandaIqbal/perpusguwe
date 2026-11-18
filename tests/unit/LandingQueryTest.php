<?php

use CodeIgniter\Test\CIUnitTestCase;

/** @internal */
final class LandingQueryTest extends CIUnitTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        require_once APPPATH . 'Modules/Home/Helpers/landing_helper.php';
    }

    public function testSingkatanRegulasiDibuka()
    {
        $this->assertSame('undang-undang nomor 13 tahun 2003', landing_query_expand('uu 13 2003'));
        $this->assertSame('undang-undang nomor 13 tahun 2003', landing_query_expand('UU 13/2003'));
        $this->assertSame('peraturan menteri tenaga kerja', landing_query_expand('permenaker'));
    }

    public function testNomorYangSudahLengkapTidakDiubah()
    {
        $this->assertSame('undang-undang no 13 tahun 2003', landing_query_expand('uu no. 13 tahun 2003'));
    }

    public function testTanpaSingkatanKembaliNull()
    {
        $this->assertNull(landing_query_expand('K3'));
        $this->assertNull(landing_query_expand('pph pasal 21'));
        $this->assertNull(landing_query_expand('13 2003'));
    }
}
