<?php

namespace App\Controllers\Api\Admin;

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

    public function updatePeminjaman($id_peminjaman) {
        $dataPeminjaman = $this->PeminjamanModel->find($id_peminjaman);
    
        if (!empty($dataPeminjaman)) {
            $tanggal_pengembalian = date('Y-m-d H:i:s');
            if (isset($tanggal_pengembalian) && !empty($tanggal_pengembalian)) {
                $tanggal_peminjaman = $dataPeminjaman['tanggal_peminjaman'];
                $datetime1 = strtotime($tanggal_peminjaman);
                $datetime2 = strtotime($tanggal_pengembalian);
                $diff = $datetime2 - $datetime1;
                $daysLate = round($diff / 86400); // 86400 seconds = 1 day
    
                $dataUlasan = [
                    'tanggal_pengembalian' => $tanggal_pengembalian,
                    'status_peminjaman' => 'dikembalikan'
                ];
    
                if ($daysLate > 7 && $daysLate <= 10) {
                    $dataUlasan['denda'] = 10000;
                } elseif ($daysLate > 10 && $daysLate <= 20) {
                    $dataUlasan['denda'] = 15000;
                } elseif ($daysLate > 20) {
                    $dataUlasan['denda'] = 20000;
                } else {
                    $dataUlasan['denda'] = 0;
                }
    
                // Update data peminjaman dengan tanggal pengembalian, denda, dan status peminjaman
                if ($this->PeminjamanModel->update($id_peminjaman, $dataUlasan)) {
                    $id_buku = $dataPeminjaman['buku_id'];
                    $this->BukuModel->where('id_buku', $id_buku)->increment('jumlah_tersedia', 1);
                    $response = [
                        'status' => true,
                        'message' => 'Peminjaman diperbarui',
                        'data' => $dataUlasan
                    ];
                } else {
                    $response = [
                        'status' => false,
                        'message' => 'Gagal memperbarui peminjaman',
                        'data' => []
                    ];
                }
            }
        } else {
            $response = [
                'status' => false,
                'message' => 'Tidak Ada Peminjaman',
                'data' => []
            ];
        }
    
        return $this->respond($response);
    }
    
}
