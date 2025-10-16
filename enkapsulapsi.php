<?php
class BankAccount {
private $saldo;
public function __construct($saldoAwal) {
$this->saldo = $saldoAwal;
}
public function deposit($jumlah) {
if ($jumlah > 0) {
$this->saldo += $jumlah;
echo "Deposit berhasil. Saldo sekarang: " . $this->saldo . "<br>";
} else {
echo "Jumlah deposit harus lebih dari 0.<br>";
}
}
public function getSaldo() {
return $this->saldo;
}
}
$rekeningBudi = new BankAccount(1000);
$rekeningBudi->deposit(500);
echo "Saldo Budi: " . $rekeningBudi->getSaldo();
?>