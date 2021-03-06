<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\Mailer\Email;
use Cake\ORM\TableRegistry;

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
        $this->loadComponent('Format');
        $this->autoRender = false;
        $this->Auth->allow(['addUser', 'logout', 'confirmEmail']);
        if ($this->request->param('action') == 'login' || $this->request->param('action') == 'addUser') {
            $this->getEventManager()->off($this->Csrf);
        }
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

    // APIs Starts

    public function addUser()
    {
        $this->autoRender = false;
        $res['result'] = [];
        $res['error'] = [];
        $res['success'] = [];
        $this->RequestHandler->renderAs($this, 'json');
        $this->response->type('application/json');
        $request = $this->request->getData();
        $user = $this->Users->newEntity($request);
        // if (!trim($user->first_name)) {
        //     $res['error']['status_code'] = 0;
        //     $res['error']['message'] = 'First Name is required!';
        //     echo json_encode($res);
        //     exit;
        // }
        // if (!trim($user->last_name)) {
        //     $res['error']['status_code'] = 0;
        //     $res['error']['message'] = 'Last Name is required!';
        //     echo json_encode($res);
        //     exit;
        // }
        if (!trim($user->email)) {
            $res['error']['status_code'] = 0;
            $res['error']['message'] = 'Email is required!';
            echo json_encode($res);
            exit;
        }
        if (!filter_var($user->email, FILTER_VALIDATE_EMAIL)) {
            $res['error']['status_code'] = 0;
            $res['error']['message'] = 'Email is not valid!';
            echo json_encode($res);
            exit;
        }
        if (!trim($user->password)) {
            $res['error']['status_code'] = 0;
            $res['error']['message'] = 'Password is required!';
            echo json_encode($res);
            exit;
        }
        if (!trim($user->role_id)) {
            $res['error']['status_code'] = 0;
            $res['error']['message'] = 'User type is required!';
            echo json_encode($res);
            exit;
        }
        $query = $this->Users->find('all')
            ->where(['Users.email' => $user->email]);
        $existing_user = $query->first();
        if (isset($existing_user) && !empty($existing_user)) {
            $res['error']['status_code'] = 0;
            $res['error']['message'] = 'Email Already Exist!';
        } else {
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
                $email_verification_code = md5(uniqid());
                $users = TableRegistry::get('Users');
                $user = $users->find('all')->where(['id' => $user->id])->first();
                $user->token = $email_verification_code;
                $users->save($user);
                $this->Format->sendConfirmEmail($email_verification_code, $user);
                $res['success']['status_code'] = 1;
                $res['success']['message'] = 'Please Check Your Email!';
            } else {
                $res['error']['status_code'] = 0;
                $res['error']['message'] = 'Error While Saving Data!';
            }
        }
        $responseResult = json_encode($res);
        $this->response->type('json');
        $this->response->body($responseResult);
    }
    public function userProfile($id = null)
    {
        $res['result'] = [];
        $res['error'] = [];
        $res['success'] = [];
        if ($this->Auth->User('id')) {
            $id = $this->Auth->User('id');
            $this->loadModel('Professions');
            $user = $this->Users->get($id, [
                'contain' => ['Roles', 'UserDetails'],
            ]);
            if (isset($user) && !empty($user)) {
                $res['success']['status_code'] = 1;
                $res['result']['User'] = $user;
                if (array_key_exists('Create Profile', $GLOBALS['roleAccess']) && $GLOBALS['roleAccess']['Create Profile']['isAllowed'] == 1) {
                    $this->loadModel('Profiles');
                    $query = $this->Profiles->find('all')
                        ->where(['Profiles.owner_id' => $this->Auth->user('id')]);
                    $existing_profile = $query->count();
                    $res['result']['ProfileCount'] = $existing_profile;
                }
            } else {
                $res['error']['status_code'] = 0;
                $res['error']['message'] = 'User not found!';
            }
        } else {
            $res['error']['status_code'] = 0;
            $res['error']['message'] = 'NOt authorized!';
        }
        $responseResult = json_encode($res);
        $this->response->type('json');
        $this->response->body($responseResult);
    }
    public function login()
    {
        $res['result'] = [];
        $res['error'] = [];
        $res['success'] = [];
        $res['error']['status_code'] = 0;
        $res['error']['message'] = 'Please Login!';
        if ($this->request->is('post')) {
            if ($this->Auth->user()) {
                $res['error'] = [];
                $res['success']['status_code'] = 1;
                $res['success']['message'] = 'Already LoggedIn!';
            } else {
                $user = $this->Auth->identify();
                if ($user) {
                    $this->Auth->setUser($user);
                    $token = $this->request->getCookie('csrfToken');
                    $res['error'] = [];
                    $res['success']['status_code'] = 1;
                    $res['success']['message'] = 'Login Successfull!';
                    $res['result']['token'] = $token;
                    $res['result']['User'] = $this->Auth->user();
                } else {
                    $res['error']['status_code'] = 0;
                    $res['error']['message'] = 'Username or password is incorrect!';
                }
            }
        }
        $responseResult = json_encode($res);
        $this->response->type('json');
        $this->response->body($responseResult);
    }
    public function logout()
    {
        $res['result'] = [];
        $res['error'] = [];
        $res['success'] = [];
        if ($this->Auth->logout()) {
            $res['success']['status_code'] = 1;
            $res['success']['message'] = 'You are now logged out!';
        }
        $responseResult = json_encode($res);
        $this->response->type('json');
        $this->response->body($responseResult);
    }

    public function confirmEmail($token = null)
    {
        $res['result'] = [];
        $res['error'] = [];
        $res['success'] = [];
        if ($token) {
            $query = $this->Users->find('all', ['conditions' => ['token' => trim($token)]]);
            $user = $query->first();
            if ($user) {
                $this->request->data['token'] = null;
                $this->request->data['is_verified'] = 1;
                $this->request->data['modified'] = GMT_DATETIME;
                $user = $this->Users->patchEntity($user, $this->request->data);
                if ($this->Users->save($user)) {
                    $res['success']['status_code'] = 1;
                    $res['success']['message'] = 'Email verified!';
                } else {
                    $res['error']['status_code'] = 0;
                    $res['error']['message'] = 'Email verification failed!';
                }
            } else {
                $res['error']['status_code'] = 0;
                $res['error']['message'] = 'Invalid token, try again!';
            }
        } else {
            $res['error']['status_code'] = 0;
            $res['error']['message'] = 'Token Missing!';
        }
        $responseResult = json_encode($res);
        $this->response->type('json');
        $this->response->body($responseResult);
    }

    public function editUser()
    {
        $res['result'] = [];
        $res['error'] = [];
        $res['success'] = [];

        $request = $this->request->getData();
        if (!$request) {
            $res['error']['status_code'] = 0;
            $res['error']['message'] = 'Empty Request!';
            echo json_encode($res);
            exit;
        }
        
        $user = $this->Users->find('all', [
            'conditions' => ['id' => $this->Auth->user('id')],
        ])->first();
        $user->first_name = $request['first_name'];
        $user->last_name = $request['last_name'];
        $user->country = $request['country'];
        $user->city = $request['city'];
        $user->modified = GMT_DATETIME;
        if ($this->Users->save($user)) {
            if ($user->role_id == 1) {
                $this->loadModel('UserDetails');
                $user_detail = $this->UserDetails->find('all', [
                    'conditions' => ['user_id' => $this->Auth->user('id')],
                ])->first();
                $user_detail->profession_id = $request['profession_id'];
                $user_detail->company = $request['company'];
                $user_detail->address = $request['address'];
                $user_detail->phone = $request['phone'];
                $user_detail->modified = GMT_DATETIME;
                $this->UserDetails->save($user_detail);
            }
            $res['success']['status_code'] = 1;
            $res['success']['message'] = 'Profile updated successfuly.';
        } else {
            $res['error']['status_code'] = 0;
            $res['error']['message'] = 'Unable to update profile.Plese try again!';
        }
        $responseResult = json_encode($res);
        $this->response->type('json');
        $this->response->body($responseResult);
    }

    public function changePassword()
    {
        $res['result'] = [];
        $res['error'] = [];
        $res['success'] = [];
        $user = $this->Users->find('all', [
            'conditions' => [
                'id' => $this->Auth->user('id'),
            ],
        ])->first();
        $request = $this->request->getData();
        if (!$request) {
            $res['error']['status_code'] = 0;
            $res['error']['message'] = 'Empty Request!';
            echo json_encode($res);
            exit;
        }
        if ($user) {
            if (!$this->Format->checkPassword($request['old_password'], $user->password)) {
                $res['error']['status_code'] = 0;
                $res['error']['message'] = 'Password Does not matched!';
                echo json_encode($res);
                exit;
            }
        } else {
            $res['error']['status_code'] = 0;
            $res['error']['message'] = 'User not found!';
            echo json_encode($res);
            exit;
        }

        if ($request['password']) {
            $user->password = $request['password'];
            $user->modified = GMT_DATETIME;
            if ($this->Users->save($user)) {
                $res['success']['status_code'] = 1;
                $res['success']['message'] = 'Password Changed Successfully.';
            }
        } else {
            $res['error']['status_code'] = 0;
            $res['error']['message'] = 'Please enter required fields!';
        }

        $responseResult = json_encode($res);
        $this->response->type('json');
        $this->response->body($responseResult);
    }
}
