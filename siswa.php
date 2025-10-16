<?php
class Siswa {
public $nama;
public $kelas;
private $nilai;
public function setNilai($nilai) {
$this->nilai = $nilai;
}
public function getNilai() {
return $this->nilai;
}
public function hitungRataRata($nilai1, $nilai2, $nilai3)
{
return ($nilai1 + $nilai2 + $nilai3) / 3;
}
}
$siswa1 = new Siswa();
$siswa1->nama = "Budi";
$siswa1->kelas = "XI IPA 1";
$siswa1->setNilai(85);
echo "Nilai " . $siswa1->nama . ": " . $siswa1->getNilai() .
"<br>";
echo "Rata-rata nilai: " . $siswa1->hitungRataRata(80, 85, 90);
?>