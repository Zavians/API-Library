<?php

namespace App\Controllers\Api\Admin;

use CodeIgniter\Shield\Entities\User;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Shield\Models\UserModel;
use CodeIgniter\RESTful\ResourceController;

class AdminController extends ResourceController
{

    protected $UserModel;

    public function __construct()
    {
        $this->UserModel = new UserModel();
    }
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

    public function logout()  {
        auth()->logout();
        auth()->user()->revokeAllAccessTokens();

        return $this->respondDeleted([
            'status' => true,
            'message' => 'Logout Berhasil',
            'data' => []
        ]);
    }

    public function invalid() {
        return $this-> respondCreated([
            'status' => false,
            'message' => 'Akses Gagal',
            'data' => []
        ]);
    }
}
