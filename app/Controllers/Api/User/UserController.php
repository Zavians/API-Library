<?php

namespace App\Controllers\Api\User;

use App\Models\AuthIdentitiesModel;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\Shield\Models\UserModel;

class UserController extends ResourceController
{
    protected $UserModel;

    protected $AuthIdentitiesModel;


    public function __construct()
    {
        $this->UserModel = new UserModel();
        $this->AuthIdentitiesModel = new AuthIdentitiesModel();
    }

    public function resetPassword() {
        
    }

    public function ubahUsername() {
        $user_id = auth()->id();
        $dataUser = $this->UserModel->find($user_id);

        if (!empty($dataUser)) {
            $username = $this->request->getVar('username');

            if (isset($username) && !empty($username)) {
                $dataUser->username = $username;

                $this->UserModel->update($user_id, $dataUser);
                $response = [
                    'status' => true,
                    'message' => 'Username Berhasil di Ubah',
                    'data' => $dataUser
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

    public function ubahEmail() {
        $user_id = auth()->id();
        $dataUser = $this->AuthIdentitiesModel->where('user_id', $user_id)->first();

        if (!empty($dataUser)) {
            $rules = [
                'email' => 'required|valid_email|is_unique[auth_identities.secret]',
            ];

            if (!$this->validate($rules)) {
                $response = [
                    'status' => false,
                    'message' => $this->validator->getErrors(),
                    'data' => []
                ];
            } else {
                $email = $this->request->getVar('email');
                if (isset($email) && !empty($email)) {
                    $dataUser['secret'] = $email;

                    $this->AuthIdentitiesModel->update($user_id , $dataUser);
                    $response = [
                        'status' => true,
                        'message' => 'Email Berhasil diubah',
                        'data' => [
                            'Email Baru Anda' => $dataUser['secret']
                        ]
                    ];
                }
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
}
