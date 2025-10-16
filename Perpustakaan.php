<?php
declare(strict_types=1);

require_once __DIR__ . '/Buku.php';
require_once __DIR__ . '/Anggota.php';

class Perpustakaan
{
    /** @var array<string,Buku> */
    private array $buku = [];
    /** @var array<string,Anggota> */
    private array $anggota = [];

    public function tambahBuku(Buku $b): void
    {
        $isbn = $b->getIsbn();
        if (isset($this->buku[$isbn])) {
            throw new RuntimeException("Buku dengan ISBN $isbn sudah ada.");
        }
        $this->buku[$isbn] = $b;
    }

    public function hapusBuku(string $isbn): void
    {
        $b = $this->buku[$isbn] ?? null;
        if (!$b) {
            throw new RuntimeException("Buku dengan ISBN $isbn tidak ditemukan.");
        }
        if (!$b->isTersedia()) {
            throw new RuntimeException("Tidak bisa menghapus. Buku sedang dipinjam.");
        }
        unset($this->buku[$isbn]);
    }

    public function cariBukuByISBN(string $isbn): ?Buku
    {
        return $this->buku[$isbn] ?? null;
    }

    /** @return Buku[] */
    public function daftarBuku(): array
    {
        return array_values($this->buku);
    }

    public function tambahAnggota(Anggota $a): void
    {
        $no = $a->getNomorAnggota();
        if (isset($this->anggota[$no])) {
            throw new RuntimeException("Anggota dengan nomor $no sudah terdaftar.");
        }
        $this->anggota[$no] = $a;
    }

    public function cariAnggotaByNomor(string $no): ?Anggota
    {
        return $this->anggota[$no] ?? null;
    }

    /** @return Anggota[] */
    public function getDaftarAnggota(): array
    {
        return array_values($this->anggota);
    }

    public function pinjamBuku(string $nomorAnggota, string $isbn): void
    {
        $anggota = $this->cariAnggotaByNomor($nomorAnggota);
        if (!$anggota) {
            throw new RuntimeException("Anggota dengan nomor $nomorAnggota tidak ditemukan.");
        }
        $buku = $this->cariBukuByISBN($isbn);
        if (!$buku) {
            throw new RuntimeException("Buku dengan ISBN $isbn tidak ditemukan.");
        }
        if (!$buku->isTersedia()) {
            throw new RuntimeException("Buku dengan ISBN $isbn sedang dipinjam.");
        }

        $buku->tandaiDipinjam($nomorAnggota);
        $anggota->pinjam($buku);
    }

    public function kembalikanBuku(string $nomorAnggota, string $isbn): void
    {
        $anggota = $this->cariAnggotaByNomor($nomorAnggota);
        if (!$anggota) {
            throw new RuntimeException("Anggota dengan nomor $nomorAnggota tidak ditemukan.");
        }
        $buku = $this->cariBukuByISBN($isbn);
        if (!$buku) {
            throw new RuntimeException("Buku dengan ISBN $isbn tidak ditemukan.");
        }
        if ($buku->isTersedia()) {
            throw new RuntimeException("Buku dengan ISBN $isbn tidak sedang dipinjam.");
        }
        if ($buku->getDipinjamOleh() !== $nomorAnggota) {
            throw new RuntimeException("Buku ini tidak dipinjam oleh anggota $nomorAnggota.");
        }

        $anggota->kembalikan($isbn);
        $buku->tandaiDikembalikan();
    }
}
