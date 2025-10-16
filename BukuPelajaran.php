<?php
declare(strict_types=1);

require_once __DIR__ . '/Buku.php';

class BukuPelajaran extends Buku
{
    private string $mataPelajaran;
    private string $tingkatKelas;

    public function __construct(
        string $judul,
        string $penulis,
        string $isbn,
        string $mataPelajaran,
        string $tingkatKelas
    ) {
        parent::__construct($judul, $penulis, $isbn);
        $this->mataPelajaran = $mataPelajaran;
        $this->tingkatKelas = $tingkatKelas;
    }

    public function getMataPelajaran(): string
    {
        return $this->mataPelajaran;
    }

    public function getTingkatKelas(): string
    {
        return $this->tingkatKelas;
    }
}
