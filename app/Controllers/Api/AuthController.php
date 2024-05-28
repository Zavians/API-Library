<?php

namespace App\Controllers\Api;

use CodeIgniter\Shield\Entities\User;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Shield\Models\UserModel;
use CodeIgniter\RESTful\ResourceController;

class AuthController extends ResourceController
{

    protected $UserModel;

    public function __construct()
    {
        $this->UserModel = new UserModel();
    }

    public function firstRegister()
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
            $status = 'admin';
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

    public function login()  {

        if (auth()->loggedIn()) {
            auth()->logout();
        }
        $rules = [
            'email' => 'required|valid_email',
            'password' => 'required'
        ];

        if (!$this->validate($rules)) {
            $response = [
                'status' => false,
                'message' => 'Pastikan kembali akun login anda',
                'data' => []
            ];
        } else {
            $dataLogin = [
                'email' => $this->request->getVar('email'),
                'password' => $this->request->getVar('password')
            ];

            $login = auth()->attempt($dataLogin);

            if (!$login->isOK()) {
                $response = [
                    'status' => false,
                    'message' => 'Akun Tidak Ada, Pastikan Kembali Akun Anda',
                    'data' => []
                ];
            } else {
                $dataUser = $this->UserModel->findById(auth()->id());
                $token = $dataUser ->generateAccessToken('Ini Token Anda');
                $auth_token = $token->raw_token;

                $response = [
                    'status' => true,
                    'message' => 'Login Berhasil',
                    'data' => [
                        'token' => $auth_token,
                        'dataUser' => $dataUser
                    ]
                ];
            }
        }

        return $this->respondCreated($response);
    }

}
