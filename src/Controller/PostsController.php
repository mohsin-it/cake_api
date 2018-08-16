<?php
namespace App\Controller;

use App\Controller\AppController;

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
            $res['error']['message'] = 'Sorry, Profile does not exist.';
            echo json_encode($res);
            exit;
        }
        $my_posts = $this->Posts->find('all', [
            'conditions' => ['pet_id' => $profile['id'],'is_active' => 1],
            'fields' => ['id','media', 'caption', 'created', 'modified'],
            'order' => ['created']
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
