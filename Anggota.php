<?php
declare(strict_types=1);

require_once __DIR__ . '/Buku.php';
require_once __DIR__ . '/PeminjamanBuku.php';

class Anggota implements PeminjamanBuku
{
    private string $nama;
    private string $nomorAnggota;
    /** @var array<string,Buku> */
    private array $bukuDipinjam = [];

    public function __construct(string $nama, string $nomorAnggota)
    {
        $this->nama = $nama;
        $this->nomorAnggota = $nomorAnggota;
    }

    public function getNama(): string { return $this->nama; }
    public function getNomorAnggota(): string { return $this->nomorAnggota; }

    /** @return Buku[] */
    public function getBukuDipinjam(): array
    {
        return array_values($this->bukuDipinjam);
    }

    public function hasBorrowed(string $isbn): bool
    {
        return isset($this->bukuDipinjam[$isbn]);
    }

    public function pinjam(Buku $buku): void
    {
        $isbn = $buku->getIsbn();
        if ($this->hasBorrowed($isbn)) {
            throw new RuntimeException("Anggota {$this->nomorAnggota} sudah meminjam buku dengan ISBN $isbn.");
        }
        $this->bukuDipinjam[$isbn] = $buku;
    }

    public function kembalikan(string $isbn): void
    {
        if (!$this->hasBorrowed($isbn)) {
            throw new RuntimeException("Anggota {$this->nomorAnggota} tidak meminjam buku dengan ISBN $isbn.");
        }
        unset($this->bukuDipinjam[$isbn]);
    }
}
