<?php
namespace App\Controller\Api;

use Cake\Event\Event;
use Cake\Http\Exception\UnauthorizedException;
use Cake\Utility\Security;
use Firebase\JWT\JWT;
use Cake\Http\ServerRequest;
use Cake\I18n\Time;

class ApiController extends AppController
{
    
    /**
     * Initialize
     *
     * @return void
     */
    public function initialize(): void
    {
        parent::initialize();
        $this->loadModel('Users');

        $this->Auth->allow(['login', 'register']);
    }

    /**
     * Login method
     * Login user and generate a jwt
     * @return void
    */
    public function login()
    {
        $response = ['success' => false, 'msg' => "Invalid Request", 'errors' => ''];
        $token = "";
        $user = $this->Auth->identify();
        if (!$user) {
            throw new UnauthorizedException("Login Failed !, Invalid Login Credentials");
        }else{
            $key = Security::getSalt();
            $response = ['success' => true, 'msg' => "Logged in successfully", 'errors' => ""];
            $token = JWT::encode([
                'email' =>  $user['email'],
                'id' => $user['id'],
                'username' => $user['username'],
                'iat' => time(),
                'exp' =>  time() + 86400, // One Day
            ],
            $key);
        }

        extract($response);
        // $this->set(['success' => $success, 'msg' => $msg, 'errors' => $errors, 'token' => $token]);
        $this->set(compact('success', 'msg', 'errors', 'token'));
        $this->viewBuilder()->setOption('serialize', ['success', 'msg', 'errors', 'token']);

    }

         
    /**
     * Register
     *
     * @return void
     */
    public function register()
    {
        $response = ['success' => false, 'msg' => "Invalid Request", 'errors' => ''];
        $user = $this->Users->newEmptyEntity();
        if ($this->request->is('post')) {
            $user = $this->Users->patchEntity($user, $this->request->getData());
            if ($this->Users->save($user)) {
                $response = ['success'=> true, 'msg' => 'Registered Successfully', 'errors' => ''];
            } else {
                $response = ['success'=> false, 'msg' => 'Enable to Register', 'errors' => $user->getErrors()];
            }
        }

        extract($response);
        $this->set(compact('success', 'msg', 'errors'));
        $this->viewBuilder()->setOption('serialize', ['success', 'msg', 'errors']);

    }

        
    /**
     * index
     *
     * @return void
     */
    public function index()
    {
        return 'hi';
        $this->paginate = [
            'contain' => ['Groups'],
        ];
        $users = $this->paginate($this->Users);
        
        
        $this->set(compact('users'));
        $this->viewBuilder()->setOption('serialize', ['users']);
    }
	

    /**
     * Edit method
     *
     * @param string|null $id User id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $response = ['success' => false, 'msg' => "Invalid Request", 'errors' => ''];
        $user = $this->Users->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $user = $this->Users->patchEntity($user, $this->request->getData());
            if ($this->Users->save($user)) {
                $response = ['success'=> true, 'msg' => 'Updated Successfully', 'errors' => ''];
            } else {
                $response = ['success'=> false, 'msg' => 'Enable to Update', 'errors' => $user->getErrors()];
            }
        }

        extract($response);
        $this->set(compact('success', 'msg', 'errors'));
        $this->viewBuilder()->setOption('serialize', ['success', 'msg', 'errors']);
    }


       /**
     * View method
     *
     * @param string|null $id User id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $user = $this->Users->get($id, [
            'contain' => ['Groups'],
        ]);

        $this->set(compact('user'));
        $this->viewBuilder()->setOption('serialize', ['user']);
    }

    /**
     * Delete method
     *
     * @param string|null $id User id.
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $response = ['success' => false, 'msg' => "Invalid Request", 'errors' => ''];
        $this->request->allowMethod(['post', 'delete']);
        $user = $this->Users->get($id);
        if ($this->Users->delete($user)) {
            $response = ['success'=> true, 'msg' => 'Deleted Successfully', 'errors' => ''];
        } else {
            $response = ['success'=> false, 'msg' => 'Enable to Delete', 'errors' => $user->getErrors()];
        }

        extract($response);
        $this->set(compact('success', 'msg', 'errors'));
        $this->viewBuilder()->setOption('serialize', ['success', 'msg', 'errors']);
    }

}