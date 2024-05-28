<?php

namespace App\Controllers\Api\User;

use App\Models\BukuModel;
use App\Models\PeminjamanModel;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\RESTful\ResourceController;

class PeminjamanController extends ResourceController
{
    protected $PeminjamanModel;
    protected $BukuModel;
    protected $UserModel;


    public function __construct()
    {
        $this->PeminjamanModel
            = new PeminjamanModel();

        $this->BukuModel
            = new BukuModel();
    }

    public function addPeminjaman($id_buku)
    {
        $dataBuku = $this->BukuModel->find($id_buku);
        if (!empty($dataBuku)) {
            $user_id = auth()->id();
            $jumlah_tersedia = $dataBuku['jumlah_tersedia'];

            if ($jumlah_tersedia == 0) {  // Corrected comparison
                $response = [
                    'status' => false,
                    'message' => 'Buku habis, harap cek dilain hari',
                    'data' => []
                ];
            } else {
                $dataPeminjaman = [
                    'user_id' => $user_id,
                    'buku_id' => $id_buku,  // Corrected to use $id_buku
                    'tanggal_peminjaman' => date('Y-m-d H:i:s'),
                    'status_peminjaman' => 'Dipinjam',
                    'tanggal_pengembalian' => 0,
                    'denda' => 0
                ];
                if ($this->PeminjamanModel->save($dataPeminjaman)) {
                    $dataBuku['jumlah_tersedia'] -= 1;
                    $this->BukuModel->update($id_buku, ['jumlah_tersedia' => $dataBuku['jumlah_tersedia']]);

                    $response = [
                        'status' => true,
                        'message' => 'Buku berhasil dipinjam',
                        'data' => $dataPeminjaman
                    ];
                } else {
                    $response = [
                        'status' => false,
                        'message' => 'Gagal menyimpan data peminjaman',
                        'data' => []
                    ];
                }
            }
        } else {
            $response = [
                'status' => false,
                'message' => 'Data buku tidak ditemukan',
                'data' => []
            ];
        }

        return $this->respondCreated($response);
    }

    public function showAllPeminjaman()
    {
        $user_id = auth()->id();
        $dataPeminjaman = $this->PeminjamanModel->where('user_id', $user_id)->findAll();
        if (!empty($dataPeminjaman)) {
            $response = [
                'status' => true,
                'message' => 'Sejarah Peminjaman Anda',
                'data' => $dataPeminjaman
            ];
        } else {
            $response = [
                'status' => false,
                'message' => 'User Belum melakukan peminjaman',
                'data' => []
            ];
        }
        return $this->respond($response);
    }

    public function showPinjamPeminjaman()
    {
        $user_id = auth()->id();
        $dataPeminjaman = $this->PeminjamanModel
            ->where('user_id', $user_id)
            ->where('status_peminjaman', 'dipinjam')
            ->findAll();

        if (!empty($dataPeminjaman)) {
            $response = [
                'status' => true,
                'message' => 'List Peminjaman Anda',
                'data' => $dataPeminjaman
            ];
        } else {
            $response = [
                'status' => false,
                'message' => 'User Belum melakukan peminjaman',
                'data' => []
            ];
        }
        return $this->respond($response);
    }

    public function showDikembalikanPeminjaman()
    {
        $user_id = auth()->id();
        $dataPeminjaman = $this->PeminjamanModel
            ->where('user_id', $user_id)
            ->where('status_peminjaman', 'dikembalikan')
            ->findAll();

        if (!empty($dataPeminjaman)) {
            $response = [
                'status' => true,
                'message' => 'List Pengembalian Anda',
                'data' => $dataPeminjaman
            ];
        } else {
            $response = [
                'status' => false,
                'message' => 'User Belum melakukan pengembalian',
                'data' => []
            ];
        }
        return $this->respond($response);
    }

    public function invalid()
    {
        return $this->respondCreated([
            'status' => false,
            'message' => 'Akses Gagal',
            'data' => []
        ]);
    }
}
