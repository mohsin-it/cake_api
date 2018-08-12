<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\Routing\Router;

/**
 * Posts Controller
 *
 * @property \App\Model\Table\PostsTable $Posts
 *
 * @method \App\Model\Entity\Post[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class PostsController extends AppController
{

    public $helpers = array('Html', 'Form');
    public function initialize()
    {
        parent::initialize();
        $this->loadComponent('Format');
        $this->autoRender = false;
    }
    /**
     * Index method
     *
     * @return \Cake\Http\Response|void
     */
    public function index()
    {
        $this->paginate = [
            'contain' => ['Pets', 'Users'],
        ];
        $posts = $this->paginate($this->Posts);

        $this->set(compact('posts'));
    }

    /**
     * View method
     *
     * @param string|null $id Post id.
     * @return \Cake\Http\Response|void
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $post = $this->Posts->get($id, [
            'contain' => ['Pets', 'Users'],
        ]);

        $this->set('post', $post);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $post = $this->Posts->newEntity();
        if ($this->request->is('post')) {
            $post = $this->Posts->patchEntity($post, $this->request->getData());
            if ($this->Posts->save($post)) {
                $this->Flash->success(__('The post has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The post could not be saved. Please, try again.'));
        }
        $pets = $this->Posts->Pets->find('list', ['limit' => 200]);
        $users = $this->Posts->Users->find('list', ['limit' => 200]);
        $this->set(compact('post', 'pets', 'users'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Post id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $post = $this->Posts->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $post = $this->Posts->patchEntity($post, $this->request->getData());
            if ($this->Posts->save($post)) {
                $this->Flash->success(__('The post has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The post could not be saved. Please, try again.'));
        }
        $pets = $this->Posts->Pets->find('list', ['limit' => 200]);
        $users = $this->Posts->Users->find('list', ['limit' => 200]);
        $this->set(compact('post', 'pets', 'users'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Post id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $post = $this->Posts->get($id);
        if ($this->Posts->delete($post)) {
            $this->Flash->success(__('The post has been deleted.'));
        } else {
            $this->Flash->error(__('The post could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }

    // APIs

    public function publish()
    {
        $res['error'] = [];
        $res['success'] = [];
        $res['result'] = [];
        $ext_list = array('jpg', 'jpeg', 'png', 'gif');
        $target_dir = WWW_ROOT . 'posts/' . $this->Auth->user('id') . '/';
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0755, true);
        }

        $request = $this->request->getData();

        $temp = explode('.', $request['file']['name']);
        if (!in_array(end($temp), $ext_list)) {
            $res['error']['status_code'] = 0;
            $res['error']['message'] = 'Sorry, only JPG, JPEG, PNG & GIF files are allowed.';
            echo json_encode($res);
            exit;
        }
        $new_name = rand() . '_' . time() . '.' . end($temp);

        $post = $this->Posts->newEntity();
        if (move_uploaded_file($request['file']['tmp_name'], $target_dir . $new_name)) {
            $post = $this->Posts->patchEntity($post, $request);

            $this->loadModel('Profiles');
            $profile = $this->Profiles->findByUniq_id($request['profile_id'])->select('id')->first();
            if (!$profile['id'] || $profile['id'] < 1) {
                $res['error']['status_code'] = 0;
                $res['error']['message'] = 'Sorry, Profile does not exist';
                echo json_encode($res);
                exit;
            }
            $post->media = $new_name;
            $post->pet_id = $profile['id'];
            $post->user_id = $this->Auth->user('id');
            $post->status = 1;
            $post->host = $this->get_client_ip_server;
            $post->created = GMT_DATETIME;
            $post->modified = GMT_DATETIME;
            if ($this->Posts->save($post)) {
                $res['success']['status_code'] = 1;
                $res['success']['message'] = 'Published Successfully!';
            } else {
                $res['error']['status_code'] = 0;
                $res['error']['message'] = 'Error while saving data!';
            }
        } else {
            $res['error']['status_code'] = 0;
            $res['error']['message'] = 'Error while uploading media!';
        }

        $responseResult = json_encode($res);
        $this->response->type('json');
        $this->response->body($responseResult);
    }

    public function getActivity($uniq_id = null)
    {
        $target_dir = HOME . 'posts/' . $this->Auth->user('id') . '/';
      //  echo '<img src="'. $target_dir . '51/35995460_1533988659.png" >';exit;
        $res['error'] = [];
        $res['success'] = [];
        $res['result'] = [];
        if (!trim($uniq_id)) {
            $res['error']['status_code'] = 0;
            $res['error']['message'] = 'Empty request!';
            echo json_encode($res);
            exit;
        }
        $this->loadModel('Profiles');
        $profile = $this->Profiles->findByUniq_id(trim($uniq_id))->select('id')->first();
        if (!$profile['id'] || $profile['id'] < 1) {
            $res['error']['status_code'] = 0;
            $res['error']['message'] = 'Sorry, Profile does not exist';
            echo json_encode($res);
            exit;
        }
        $my_posts = $this->Posts->find('all', [
            'conditions' => ['pet_id' => $profile['id']],
            'fields' => ['media', 'caption', 'created', 'modified'],
        ])->toArray();
        if ($my_posts) {
            $res['result']['FilePath'] = $target_dir;
            $res['result']['Posts'] = $my_posts;
        } else {
            $res['error']['status_code'] = 0;
            $res['error']['message'] = 'No item published for this profile.';
        }
        $responseResult = json_encode($res);
        $this->response->type('json');
        $this->response->body($responseResult);
    }

    public function get_client_ip_server()
    {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            //ip from share internet
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            //ip pass from proxy
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        return $ip;
    }
}
