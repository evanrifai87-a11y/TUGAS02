<?php
declare(strict_types=1);

class Buku
{
    private string $judul;
    private string $penulis;
    private string $isbn;
    private ?string $dipinjamOleh = null;

    public function __construct(string $judul, string $penulis, string $isbn)
    {
        $this->judul = $judul;
        $this->penulis = $penulis;
        $this->isbn = $isbn;
    }

    public function getJudul(): string { return $this->judul; }
    public function getPenulis(): string { return $this->penulis; }
    public function getIsbn(): string { return $this->isbn; }

    public function isTersedia(): bool { return $this->dipinjamOleh === null; }
    public function getDipinjamOleh(): ?string { return $this->dipinjamOleh; }

    public function tandaiDipinjam(string $nomorAnggota): void
    {
        if (!$this->isTersedia()) {
            throw new RuntimeException("Buku dengan ISBN {$this->isbn} sedang dipinjam.");
        }
        $this->dipinjamOleh = $nomorAnggota;
    }

    public function tandaiDikembalikan(): void
    {
        if ($this->isTersedia()) {
            throw new RuntimeException("Buku dengan ISBN {$this->isbn} tidak sedang dipinjam.");
        }
        $this->dipinjamOleh = null;
    }
}
