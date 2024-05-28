<?php

namespace App\Controllers\Api\Admin;

use App\Models\BukuModel;
use App\Models\PeminjamanModel;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\RESTful\ResourceController;

class BookController extends ResourceController
{
    protected $BukuModel;
    protected $PeminjamanModel;

    public function __construct()
    {
        $this->BukuModel = new BukuModel();
        $this->PeminjamanModel = new PeminjamanModel();
    }

    public function addBuku()
    {
        $rules = [
            'judul' => 'required|is_unique[buku.judul]',
            'penulis' => 'required',
            'penerbit' => 'required',
            'tahun_terbit' => 'required',
            'genre' => 'required',
            'jumlah_total_buku' => 'required',
            'jumlah_tersedia' => 'required',
        ];

        if (!$this->validate($rules)) {
            $response = [
                'status' => false,
                'message' => $this->validator->getErrors(),
                'data' => []
            ];
        } else {
            $imagesFile = $this->request->getFile('gambar_buku');
            $imagesName = $imagesFile->getName();
            $tempArray = explode(".", $imagesName);
            $newImageName = round(microtime(true)) . "." . end($tempArray);

            if ($imagesFile->move('images/Buku', $newImageName)) {
                $jumlah_total_buku = $this->request->getVar('jumlah_total_buku');
                $jumlah_tersedia = $this->request->getVar('jumlah_tersedia');

                if ($jumlah_tersedia > $jumlah_total_buku) {
                    $response = [
                        'status' => false,
                        'message' => 'Jumlah Buku Tersedia Melebihi Total Buku yang ada',
                        'data' => []
                    ];
                } else {
                    $dataUploaded = [
                        'judul' => $this->request->getVar('judul'),
                        'penulis' => $this->request->getVar('penulis'),
                        'penerbit' => $this->request->getVar('penerbit'),
                        'tahun_terbit' => $this->request->getVar('tahun_terbit'),
                        'genre' => $this->request->getVar('genre'),
                        'jumlah_total_buku' => $jumlah_total_buku,
                        'jumlah_tersedia' => $jumlah_tersedia,
                        'gambar_buku' => $newImageName
                    ];
                    $this->BukuModel->save($dataUploaded);
                    $response = [
                        'status' => true,
                        'message' => 'Detail Buku Ditambahkan',
                        'data' => $dataUploaded
                    ];
                }
            } else {
                $response = [
                    'status' => false,
                    'message' => 'Gambar Buku Gagal Terupload',
                    'data' => []
                ];
            }
        }
        return $this->respondCreated($response);
    }

    public function updateBuku($id_buku = null)
    {
        $dataBuku = $this->BukuModel->find($id_buku);

        if (!empty($dataBuku)) {
            $judul = $this->request->getVar('judul');
            if (isset($judul) && !empty($judul)) {
                $dataBuku['judul'] = $judul;
            }

            $penulis = $this->request->getVar('penulis');
            if (isset($penulis) && !empty($penulis)) {
                $dataBuku['penulis'] = $penulis;
            }

            $penerbit = $this->request->getVar('penerbit');
            if (isset($penerbit) && !empty($penerbit)) {
                $dataBuku['penerbit'] = $penerbit;
            }

            $tahun_terbit = $this->request->getVar('tahun_terbit');
            if (isset($tahun_terbit) && !empty($tahun_terbit)) {
                $dataBuku['tahun_terbit'] = $tahun_terbit;
            }

            $genre = $this->request->getVar('genre');
            if (isset($genre) && !empty($genre)) {
                $dataBuku['genre'] = $genre;
            }

            $dataTersedia = $dataBuku['jumlah_tersedia'];
            $jumlah_total_buku = $this->request->getVar('jumlah_total_buku');

            if ($dataTersedia > $jumlah_total_buku) {
                $response = [
                    'status' => false,
                    'message' => 'Data buku tersedia tidak bisa melebihi total buku',
                    'data' => []
                ];
            } else {
                if (isset($jumlah_total_buku) && !empty($jumlah_total_buku)) {
                    $dataBuku['jumlah_total_buku'] = $jumlah_total_buku;
                }
            }


            $dataTotalBuku = $dataBuku['jumlah_total_buku'];
            $jumlah_tersedia = $this->request->getVar('jumlah_tersedia');
            if ($dataTotalBuku < $jumlah_tersedia) {
                $response = [
                    'status' => false,
                    'message' => 'Data buku tersedia tidak bisa melebihi total buku',
                    'data' => []
                ];
            } else {
                if (isset($jumlah_tersedia) && !empty($jumlah_tersedia)) {
                    $dataBuku['jumlah_tersedia'] = $jumlah_tersedia;
                }
            }

            $imagesFile = $this->request->getFile('gambar_buku');

            if (!empty($imagesFile)) {
                $imagesName = $imagesFile->getName();
                $tempArray = explode(".", $imagesName);
                $newImageName = round(microtime(true)) . "." . end($tempArray);

                if ($imagesFile->move('images/Buku', $newImageName)) {
                    $dataBuku['gambar_buku'] = $newImageName;
                } else {
                    $response = [
                        'status' => false,
                        'message' => 'Gambar gagal diperbarui'
                    ];
                }
            }

            $this->BukuModel->update($id_buku, $dataBuku);
            $response = [
                'status' => true,
                'message' => 'Data diperbarui',
                'data' => $dataBuku
            ];
        } else {
            $response = [
                'status' => false,
                'message' => 'Data Tidak Ditemukan',
                'data' => []
            ];
        }
        return $this->respond($response);
    }

    public function showAllBuku() {
        $dataBuku = $this->BukuModel->findAll();

        if (!empty($dataBuku)) {
            $response = [
                'status' => true,
                'message'=>'Data ditampilkan',
                'data' => $dataBuku
            ];
        } else {
            $response = [
                'status' => true,
                'message'=>'Tidak ada data buku',
                'data' => []
            ];
        }

        return $this->respond($response);
    }

    public function showBukuById($id_buku = null){
        $dataBuku = $this->BukuModel->find($id_buku);
        if (!empty($dataBuku)) {
            $response = [
                'status' => true,
                'message'=>'Data ditampilkan',
                'data' => $dataBuku
            ];
        } else {
            $response = [
                'status' => true,
                'message'=>'Tidak ada data buku',
                'data' => []
            ];
        }

        return $this->respond($response);
    }

    public function deleteBuku($id_buku){
        $dataBuku = $this->BukuModel->find($id_buku);

        if (!empty($dataBuku)) {
            $cekData = $this->PeminjamanModel->where('buku_id', $id_buku)->findAll();
            if (!empty($cekData)) {
                $response = [
                    'status' => true,
                    'message' => 'Data Sedang digunakan',
                    'data' => []
                ];
            } else {
                $this->BukuModel->delete($dataBuku);
                $response = [
                    'status' => true,
                    'message' => 'Data dihapus',
                    'data' => []
                ];
            }
        } else {
            $response = [
                'status' => false,
                'message' => 'Data tidak ditemukan',
                'data' => []
            ];
        }
    }

    public function invalid() {
        return $this-> respondCreated([
            'status' => false,
            'message' => 'Akses Gagal',
            'data' => []
        ]);
    }
}
