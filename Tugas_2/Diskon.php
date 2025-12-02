<?php
function hitungDiskon($totalBelanja) {
    
    if ($totalBelanja >= 150000) {
        $nominalDiskon = $totalBelanja * 0.10;
    } 
    elseif ($totalBelanja >= 100000 && $totalBelanja < 150000) {
        $nominalDiskon = $totalBelanja * 0.05;
    } 
    else {
        $nominalDiskon = 0;
    }

    return $nominalDiskon;
}

$totalBelanja = 215500;

$diskon = hitungDiskon($totalBelanja);

$totalBayar = $totalBelanja - $diskon;

echo "=== Rincian Pembayaran ===" . "<br>";
echo "Total Belanja : Rp. " . number_format($totalBelanja, 0, ',', '.') . "<br>";
echo "Diskon        : Rp. " . number_format($diskon, 0, ',', '.') . "<br>";
echo "--------------------------" . "<br>";
echo "Total Bayar   : Rp. " . number_format($totalBayar, 0, ',', '.') . "<br>";
?>