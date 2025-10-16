<?php
interface Suara {
public function buatSuara();
}
class Anjing implements Suara {
public function buatSuara() {
echo "Guk! Guk!<br>";
}
}
class Kucing implements Suara {
public function buatSuara() {
echo "Meow!<br>";
}
}
function bunyikanHewan(Suara $hewan) {
$hewan->buatSuara();
}
$anjing = new Anjing();
$kucing = new Kucing();
bunyikanHewan($anjing);
bunyikanHewan($kucing);
?>