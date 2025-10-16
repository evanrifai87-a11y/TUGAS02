<?php
// Ini adalah class sederhana
class Mobil {
// Properti
public $merk;
public $warna;
// Metode
public function klakson() {
echo "Beep! Beep!";
}
}
// Membuat objek dari class Mobil
$mobilSaya = new Mobil();
$mobilSaya->merk = "Toyota";
$mobilSaya->warna = "Merah";
echo "Mobil saya bermerk " . $mobilSaya->merk . " dan berwarn
a " . $mobilSaya->warna . ".<br>";
$mobilSaya->klakson();
?>