<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\Http\Cookie\Cookie;

/**
 * Users Controller
 *
 * @property \App\Model\Table\UsersTable $Users
 *
 * @method \App\Model\Entity\User[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class UsersController extends AppController
{

    public $helpers = array('Html', 'Form');
    public function initialize()
    {
        parent::initialize();
        //$this->loadComponent('Csrf');
        $this->autoRender = false;
        $this->Auth->allow(['addUser', 'logout']);
        // if ($this->request->param('action') == 'login' || $this->request->param('action') == 'addUser') {
        //     $this->getEventManager()->off($this->Csrf);
        // }
    }
    /**
     * Index method
     *
     * @return \Cake\Http\Response|void
     */
    public function index()
    {
        $this->paginate = [
            'contain' => ['Roles'],
        ];
        $users = $this->paginate($this->Users);

        $this->set(compact('users'));
    }

    /**
     * View method
     *
     * @param string|null $id User id.
     * @return \Cake\Http\Response|void
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $user = $this->Users->get($id, [
            'contain' => ['Roles', 'UserDetails'],
        ]);

        $this->set('user', $user);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $user = $this->Users->newEntity();
        if ($this->request->is('post')) {
            $user = $this->Users->patchEntity($user, $this->request->getData());
            if ($this->Users->save($user)) {
                $this->Flash->success(__('The user has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The user could not be saved. Please, try again.'));
        }
        $roles = $this->Users->Roles->find('list', ['limit' => 200]);
        $this->set(compact('user', 'roles'));
    }

    /**
     * Edit method
     *
     * @param string|null $id User id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $user = $this->Users->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $user = $this->Users->patchEntity($user, $this->request->getData());
            if ($this->Users->save($user)) {
                $this->Flash->success(__('The user has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The user could not be saved. Please, try again.'));
        }
        $roles = $this->Users->Roles->find('list', ['limit' => 200]);
        $this->set(compact('user', 'roles'));
    }

    /**
     * Delete method
     *
     * @param string|null $id User id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $user = $this->Users->get($id);
        if ($this->Users->delete($user)) {
            $this->Flash->success(__('The user has been deleted.'));
        } else {
            $this->Flash->error(__('The user could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }

    public function addUser()
    {
        $this->autoRender = false;
        $this->RequestHandler->renderAs($this, 'json');
        $this->response->type('application/json');
        $request = $this->request->getData();
        $user = $this->Users->newEntity($request);
        $query = $this->Users->find('all')
            ->where(['Users.email' => $user->email]);
        $existing_user = $query->first();
        if (isset($existing_user) && !empty($existing_user)) {
            $res['status'] = 0;
            $res['message'] = 'Email Already Exist!';
        } else {
            $user->terms_accepted = 1;
            $user->is_verified = 0;
            $user->is_active = 1;
            $user->created = GMT_DATETIME;
            $user->modified = GMT_DATETIME;
            if ($user = $this->Users->save($user)) {
                if ($user->role_id == 1) {
                    $this->loadModel('UserDetails');
                    $userDetail = $this->UserDetails->newEntity($request);
                    $userDetail->user_id = $user->id;
                    $userDetail->created = GMT_DATETIME;
                    $userDetail->modified = GMT_DATETIME;
                    $detail = $this->UserDetails->save($userDetail);
                    //  $userDetail->profession_id =
                }
                $res['status'] = 1;
                $res['message'] = 'Plerase Check Your Email!';
            } else {
                $res['status'] = 0;
                $res['message'] = 'Error While Saving Data!';
            }
        }
        $responseResult = json_encode($res);
        $this->response->type('json');
        $this->response->body($responseResult);
    }
    public function login()
    {
        $res['status'] = 0;
        $res['message'] = 'Username or password is incorrect!';
        if ($this->request->is('post')) {
            if ($this->Auth->user()) {
                $res['status'] = 1;
                $res['message'] = 'Already LoggedIn!';
                $res['User'] = $this->Auth->user();
            } else {
                $user = $this->Auth->identify();
                if ($user) {
                    $this->Auth->setUser($user);
                    $res['status'] = 1;
                    $res['message'] = 'Login Successfull!';
                    $res['User'] = $this->Auth->user();
                } else {
                    $res['status'] = 0;
                    $res['message'] = 'Username or password is incorrect!';
                }
            }
        }
        $responseResult = json_encode($res);
        $this->response->type('json');
        $this->response->body($responseResult);
    }
    public function logout()
    {
        if ($this->Auth->logout()) {
            $res['status'] = 1;
            $res['message'] = 'You are now logged out!';
        }
        $responseResult = json_encode($res);
        $this->response->type('json');
        $this->response->body($responseResult);
    }
}
