<?php
namespace App\Controller\Api;

use Cake\Controller\Controller;
use Cake\Event\Event;
class AppController extends Controller
{
    public function initialize(): void
    {
        parent::initialize();

        $this->loadComponent('RequestHandler');
        $this->loadComponent('Auth', [
            'storage' => 'Memory',
            'authenticate' => [
                'Form' => [
                    'scope' => ['Users.id']
                ],
                'ADmad/JwtAuth.Jwt' => [
                    'userModel' => 'Users',
                    'fields' => [
                        'username' => 'id'
                    ],
                    'parameter' => 'token',
                    'queryDatasource' => true
                ]
            ],
            'unauthorizedRedirect' => false,
            'checkAuthIn' => 'Controller.initialize',
            'loginAction' => false
        ]);
    }
}
