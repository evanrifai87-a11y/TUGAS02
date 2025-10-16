<?php
declare(strict_types=1);

require_once __DIR__ . '/Buku.php';

/**
 * Kontrak peminjaman buku bagi entitas yang dapat meminjam.
 */
interface PeminjamanBuku
{
    /**
     * Meminjam sebuah buku.
     */
    public function pinjam(Buku $buku): void;

    /**
     * Mengembalikan buku berdasarkan ISBN.
     */
    public function kembalikan(string $isbn): void;

    /**
     * Mengecek apakah buku (ISBN) sedang dipinjam oleh entitas ini.
     */
    public function hasBorrowed(string $isbn): bool;

    /**
     * Mengambil daftar buku yang sedang dipinjam.
     *
     * @return Buku[]
     */
    public function getBukuDipinjam(): array;
}
