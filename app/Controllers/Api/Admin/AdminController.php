<?php

namespace App\Controllers\Api\Admin;

use App\Models\AuthIdentitiesModel;
use CodeIgniter\Shield\Entities\User;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Shield\Models\UserModel;
use CodeIgniter\RESTful\ResourceController;
use PhpParser\Node\Expr\Isset_;

class AdminController extends ResourceController
{

    protected $UserModel;
    protected $AuthIdentitiesModel;

    public function __construct()
    {
        $this->UserModel = new UserModel();
        $this->AuthIdentitiesModel = new AuthIdentitiesModel();
    }
    

    public function ubahPassword()
    {
        // Ambil ID pengguna yang sedang login
        $dataUser = auth()->id();
        $cekUser = $this->AuthIdentitiesModel->where('user_id', $dataUser)->first();
        $passwordLama = $this->request->getVar('password_lama');
        $passwordBaru = $this->request->getVar('password_baru');

        if (!password_verify($passwordLama, $cekUser['secret2'])) {
            $response = [
                'status' => false,
                'message' => 'Verifikasi Password Salah, Gagal Merubah Password',
                'data' => []
            ];
        } else {

            if (!empty($passwordBaru) && isset($passwordBaru)) {
                // Hash password baru sebelum menyimpannya
                $hashedPassword = password_hash($passwordBaru, PASSWORD_DEFAULT);

                // Update password pengguna di database
                $this->AuthIdentitiesModel->update($cekUser['id'], ['secret2' => $hashedPassword]);

                $response = [
                    'status' => true,
                    'message' => 'Password berhasil diubah',
                    'data' => []
                ];
            } else {
                $response = [
                    'status' => false,
                    'message' => 'Password baru tidak boleh kosong dan harus minimal 8 karakter',
                    'data' => []
                ];
            }
        }
        return $this->respond($response);
    }

    public function ubahUsername()
    {
        $dataUser = auth()->id();
        $cekData = $this->UserModel->find($dataUser);

        if (!empty($cekData)) {
            $username = $this->request->getVar('username');
            if (isset($username) && !empty($username)) {
                $cekData->username = $username;

                $this->UserModel->update($dataUser, $cekData);
                $response = [
                    'status' => false,
                    'message' => 'Username Berhasil diubah',
                    'data' => $cekData
                ];
            } else {
                $response = [
                    'status' => false,
                    'message' => 'Gagal Mengubah Username',
                    'data' => []
                ];
            }
        } else {
            $response = [
                'status' => false,
                'message' => 'Data Pengguna Tidak ditemukan',
                'data' => []
            ];
        }
        return $this->respondUpdated($response);
    }

    public function logout()
    {
        auth()->logout();
        auth()->user()->revokeAllAccessTokens();

        return $this->respondDeleted([
            'status' => true,
            'message' => 'Logout Berhasil',
            'data' => []
        ]);
    }

    public function ubahEmail()
    {
        $dataLogin = auth()->id();
        $dataAdmin = $this->AuthIdentitiesModel->where('user_id', $dataLogin)->first();

        if (!empty($dataAdmin)) {
            $email = $this->request->getVar('email');
            if (isset($email) && !empty($email)) {
                $dataAdmin['secret'] = $email;

                $this->AuthIdentitiesModel->update($dataLogin, $dataAdmin);
                $response = [
                    'status' => true,
                    'message' => 'Email Berhasil diubah',
                    'data' => $dataAdmin
                ];
            } else {
                $response = [
                    'status' => false,
                    'message' => 'Gagal Mengubah Email',
                    'data' => []
                ];
            }
        } else {
            $response = [
                'status' => false,
                'message' => 'Data Pengguna Tidak ditemukan',
                'data' => []
            ];
        }
        return $this->respondUpdated($response);
    }

    //Setting Akun Pegawai
    public function userRegister()
    {
        $rules = [
            'username' => 'required|is_unique[users.username]',
            'email' => 'required|valid_email|is_unique[auth_identities.secret]',
            'password' => 'required'
        ];

        if (!$this->validate($rules)) {
            $response = [
                'status' => false,
                'message' => $this->validator->getErrors(),
                'data' => []
            ];
        } else {
            $status = 'pegawai';
            $userEntityData = new User([
                'username' => $this->request->getVar('username'),
                'email' => $this->request->getVar('email'),
                'password' => $this->request->getVar('password'),
                'status' => $status
            ]);

            $this->UserModel->save($userEntityData);
            $response = [
                'status' => true,
                'message' => 'Registrasi Akun Berhasil',
                'data' => []
            ];
        }

        return $this->respondCreated($response);
    }

    //Reset Password Pegawai
    public function updatePegawai($id) {
        // Find user by id
        $dataUser = $this->UserModel->find($id);
        
        // Find user identity by user id
        $dataUsers = $this->AuthIdentitiesModel->where('user_id', $id)->first();
    
        if (!empty($dataUser)) {
            if ($dataUser->status === 'admin') { // Check if the user is an admin
                $response = [
                    'status' => false,
                    'message' => 'Tidak Bisa mengubah data sesama Admin',
                    'data' => []
                ];
            } else {
                $username = $this->request->getVar('username');
                if (isset($username) && !empty($username)) {
                    $dataUser->username= $username;
                }
    
                $email = $this->request->getVar('email');
                if (isset($email) && !empty($email)) {
                    $dataUsers['secret'] = $email;
                }
    
                $password = $this->request->getVar('password');
                if (isset($password) && !empty($password)) {
                    $hashPassword = password_hash($password, PASSWORD_DEFAULT);
                    $dataUsers['secret'] = $hashPassword;
                }
    
                // Update user and identity data
                $this->UserModel->update($id, $dataUser);
                $this->AuthIdentitiesModel->update($dataUsers['id'], $dataUsers);
    
                $response = [
                    'status' => true,
                    'message' => 'Data Pegawai Berhasil di Ubah',
                    'data' => []
                ];
            }
        } else {
            $response = [
                'status' => false,
                'message' => 'Data Pengguna Tidak ditemukan',
                'data' => []
            ];
        }
    
        return $this->respondUpdated($response);
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
