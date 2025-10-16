<?php
class Buku {
public $judul;
public $penulis;
public function __construct($judul, $penulis) {
$this->judul = $judul;
$this->penulis = $penulis;
echo "Buku '{$this->judul}' telah dibuat.<br>";
}
public function __destruct() {
echo "Buku '{$this->judul}' telah dihapus dari memor
i.<br>";
}
}
$buku1 = new Buku("Harry Potter", "J.K. Rowling");
echo "Judul: " . $buku1->judul . ", Penulis: " . $buku1->penulis . "<br>";
?>