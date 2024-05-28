<?php

namespace App\Controllers\Api\User;

use App\Models\BukuModel;
use App\Models\UlasanModel;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\RESTful\ResourceController;

class UlasanController extends ResourceController
{
    protected $UlasanModel;
    protected $BukuModel;

    public function __construct()
    {
        $this->UlasanModel = new UlasanModel();
        $this->BukuModel = new BukuModel();
    }

    public function addUlasan($id_buku)
    {
        $dataBuku = $this->BukuModel->find($id_buku);
        if (!empty($dataBuku)) {
            $rules = [
                'rating' => 'required',
                'komentar' => 'required',
            ];

            if (!$this->validate($rules)) {
                $response = [
                    'status' => false,
                    'message' => $this->validator->getErrors(),
                    'data' => []
                ];
            } else {
                $user_id = auth()->id();
                $dataUlasan = [
                    'user_id' => $user_id,
                    'buku_id' => $dataBuku['id_buku'],
                    'rating' => $this->request->getVar('rating'),
                    'komentar' => $this->request->getVar('komentar'),
                    'tanggal_ulasan' => date('Y-m-d H:i:s')
                ];
                $this->UlasanModel->save($dataUlasan);
                $response = [
                    'status' => true,
                    'message' => 'Ulasan berhasil ditambahkan',
                    'data' => $dataUlasan
                ];
            }
        } else {
            $response = [
                'status' => false,
                'message' => 'Data buku tidak ditemukan',
                'data' => []
            ];
        }
        return $this->respond($response);
    }

    public function showAllUlasan($id_buku)
    {
        $dataBuku = $this->BukuModel->find($id_buku);
        if (!empty($dataBuku)) {
            $ulasanBuku = $this->UlasanModel->findAll();
            if (!empty($ulasanBuku)) {
                $response = [
                    'status' => false,
                    'message' => 'Ulasan ditemukan',
                    'data' => [
                        'data_buku' => $dataBuku,
                        'ulasan_buku' => $ulasanBuku
                    ]
                ];
            } else {
                $response = [
                    'status' => false,
                    'message' => 'Buku belum memiliki ulasan',
                    'data' => []
                ];
            }
        } else {
            $response = [
                'status' => false,
                'message' => 'Data buku tidak ditemukan',
                'data' => []
            ];
        }
        return $this->respond($response);
    }

    public function updateUlasan($id_buku, $id_ulasan)
    {
        // Find the book data
        $dataBuku = $this->BukuModel->find($id_buku);
        if (!empty($dataBuku)) {
            // Find the review data
            $dataUlasan = $this->UlasanModel->find($id_ulasan);
            $user_id = auth()->id();
            if ($dataUlasan['user_id'] != $user_id) {
                $response = [
                    'status' => false,
                    'message' => 'Tidak bisa merubah ulasan orang lain',
                    'data' => []
                ];
            } else {
                // Update rating if provided
                $rating = $this->request->getVar('rating');
                if (isset($rating) && !empty($rating)) {
                    $dataUlasan['rating'] = $rating;
                }

                // Update comment if provided
                $komentar = $this->request->getVar('komentar');
                if (isset($komentar) && !empty($komentar)) {
                    $dataUlasan['komentar'] = $komentar;
                }

                // Update review date
                $tanggal_ulasan = date('Y-m-d H:i:s');
                $dataUlasan['tanggal_ulasan'] = $tanggal_ulasan;

                // Save updated review data
                $this->UlasanModel->update($id_ulasan, $dataUlasan);
                $response = [
                    'status' => true,
                    'message' => 'Data Updated',
                    'data' => $dataUlasan
                ];
            }
        } else {
            $response = [
                'status' => false,
                'message' => 'Data buku tidak ditemukan',
                'data' => []
            ];
        }
        return $this->respondUpdated($response);
    }

    public function deleteUlasan($id_buku, $id_ulasan)
    {
        $dataBuku = $this->BukuModel->find($id_buku);
        if (!empty($dataBuku)) {
            $dataUlasan = $this->UlasanModel->find($id_ulasan);
            $user_id = auth()->id();
            if ($dataUlasan['user_id'] != $user_id) {
                $response = [
                    'status' => false,
                    'message' => 'Tidak bisa menghapus ulasan orang lain',
                    'data' => []
                ];
            } else {
                $this->UlasanModel->delete($dataUlasan);
                $response = [
                    'status' => false,
                    'message' => 'Ulasan anda berhasil dihapus',
                    'data' => []
                ];
            }
        } else {
            $response = [
                'status' => false,
                'message' => 'Data buku tidak ditemukan',
                'data' => []
            ];
        }

        return $this->respondDeleted($response);
    }

    public function invalid() {
        return $this-> respondCreated([
            'status' => false,
            'message' => 'Akses Gagal',
            'data' => []
        ]);
    }
}
