<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

Route::get('/mahasiswa/{nim?}', function (?string $nim = null) {
    try {
        // 1. Mengambil koneksi PDO dari Laravel
        $pdo = DB::connection()->getPdo();

        // 2. Menyusun query SQL dengan teknik JOIN tabel mahasiswa dan program_studi
        $sql = 'SELECT m.nim, m.nama, m.email, m.usia, p.nama_prodi 
                FROM mahasiswa AS m 
                JOIN program_studi AS p 
                ON p.id = m.program_studi_id';

        // 3. Menambahkan klausa WHERE hanya jika parameter NIM diberikan di URL
        if ($nim !== null) {
            $sql .= ' WHERE m.nim = :nim';
        }

        // 4. Mengurutkan data berdasarkan NIM
        $sql .= ' ORDER BY m.nim';

        // 5. Eksekusi prepared statement untuk keamanan query (mencegah SQL Injection)
        $statement = $pdo->prepare($sql);
        $statement->execute($nim !== null ? ['nim' => $nim] : []);
        $daftarMahasiswa = $statement->fetchAll(\PDO::FETCH_ASSOC);

        // 6. Mengirim data ke tampilan Blade
        return view('mahasiswa', compact('daftarMahasiswa', 'nim'));

    } catch (\Throwable $error) {
        // 7. Error handling jika koneksi atau query gagal
        report($error);
        return response(
            'Koneksi atau query basis data gagal. Periksa file .env dan layanan MySQL.',
            500
        );
    }
});