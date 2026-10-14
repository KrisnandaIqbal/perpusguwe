<?php
/**
 * Template Xprinter XP-420B - 10x5cm
 * Versi HTML murni supaya bisa langsung memicu dialog print browser,
 * meniru cara kerja plugin cetak label SLiMS lama.
 * Variabel: $LabelData (array)
 */
$labelsHtml = '';
$index = 0;
foreach ($LabelData as $row) {
    $title = character_limiter($row['Title'], 60);
    $title = html_entity_decode($title, ENT_QUOTES, 'UTF-8');

    $sliced = preg_split(
        "/((?<=\w)\s+(?=\D))|((?<=\D)\s+(?=\d))/m",
        trim($row['CallNumber'])
    );

    $namaPerpus = 'PERPUSTAKAAN KEMNAKER';

    $callNumberHtml = '';
    foreach ($sliced as $i => $slice) {
        $cls = ($i === 0) ? 'cn-first' : 'cn-rest';
        $callNumberHtml .= '<span class="' . $cls . '">' . htmlspecialchars($slice) . '</span>';
    }

    // Label pertama tanpa page-break-before, sisanya diberi page-break-before
    // (bukan page-break-after) supaya tidak ada halaman kosong ekstra setelah label terakhir
    $pageBreakClass = ($index === 0) ? '' : ' page-break';
    $index++;

    $labelsHtml .= '
    <div class="label-box' . $pageBreakClass . '">
        <div class="label-border">
            <div class="label-left">
                <div class="barcode-title">' . htmlspecialchars($title) . '</div>
                <img src="' . $row['BarcodePNG'] . '" class="barcode-img">
                <div class="barcode-text">' . htmlspecialchars($row['Barcode']) . '</div>
            </div>
            <div class="label-right">
                <div class="call-header">' . htmlspecialchars($namaPerpus) . '</div>
                <div class="call-number">' . $callNumberHtml . '</div>
            </div>
        </div>
    </div>';
}
?>
<!doctype html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Cetak Label</title>
<style>
    * { box-sizing: border-box; margin: 0; padding: 0; }
    html, body { background: #fff; }
    @page { size: 100mm 50mm; margin: 0; }
    .label-box {
        width: 100mm; height: 50mm;
        padding: 2mm;
        overflow: hidden;
    }
    .label-box.page-break {
        page-break-before: always;
    }
    .label-border {
        width: 100%; height: 100%;
        border: 0.3mm solid #333;
        display: flex; flex-direction: row;
        overflow: hidden;
    }
    .label-left {
        width: 50%; height: 100%;
        display: flex; flex-direction: column;
        align-items: center; justify-content: center;
        border-right: 0.3mm solid #ccc;
        padding: 3mm 2mm; overflow: hidden;
    }
    .barcode-title {
        font-family: Arial, sans-serif; font-size: 7pt; font-weight: bold;
        text-align: center; color: #333; line-height: 1.2; margin-bottom: 1mm;
    }
    .barcode-img { width: 42mm; height: 19mm; }
    .barcode-text { font-family: Arial, sans-serif; font-size: 7pt; margin-top: 1mm; }
    .label-right {
        width: 50%; height: 100%;
        display: flex; flex-direction: column;
        align-items: center; justify-content: center;
        padding: 3mm; text-align: center; overflow: hidden;
    }
    .call-header {
        font-family: Arial, sans-serif; font-size: 8pt; font-weight: bold;
        border-bottom: 0.3mm solid #999; padding-bottom: 1.5mm;
        margin-top: 3mm; margin-bottom: 2mm; width: 100%;
    }
    .call-number { display: flex; flex-direction: column; align-items: center; gap: 0.3mm; }
    .call-number span { font-family: Arial, sans-serif; font-weight: bold; display: block; line-height: 1; }
    .call-number .cn-first { font-size: 16pt; }
    .call-number .cn-rest  { font-size: 11pt; }
    .no-print { display: block; margin: 5mm; padding: 2mm 4mm; background: #28a745; color: #fff; border: none; cursor: pointer; font-size: 10pt; }
    @media print { .no-print { display: none !important; } }
</style>
</head>
<body>
    <button class="no-print" onclick="window.print()">Cetak Kembali</button>
    <?= $labelsHtml ?>
    <script>
    window.onload = function () {
        var images = document.querySelectorAll('.barcode-img');
        var loaded = 0;
        if (images.length === 0) { window.print(); return; }
        images.forEach(function (img) {
            if (img.complete) {
                loaded++;
                if (loaded === images.length) window.print();
            } else {
                img.onload = function () {
                    loaded++;
                    if (loaded === images.length) window.print();
                };
            }
        });
    };
    </script>
</body>
</html>
