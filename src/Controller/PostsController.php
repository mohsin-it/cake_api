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

        $this->loadModel('Profiles');
        $profile = $this->Profiles->findByUniq_id($request['profile_id'])->select('id')->first();
        if (!$profile['id'] || $profile['id'] < 1) {
            $res['error']['status_code'] = 0;
            $res['error']['message'] = 'Sorry, Profile does not exist';
            echo json_encode($res);
            exit;
        }

        if (empty($request['file']) && empty($request['caption'])) {
            $res['error']['status_code'] = 0;
            $res['error']['message'] = 'Please Upload Either Picture or Text.';
            echo json_encode($res);
            exit;
        }
        $isUploaded = false;
        $new_name = null;
        if (isset($request['file'])) {
            $temp = explode('.', $request['file']['name']);
            if (!in_array(end($temp), $ext_list)) {
                $res['error']['status_code'] = 0;
                $res['error']['message'] = 'Sorry, only JPG, JPEG, PNG & GIF files are allowed.';
                echo json_encode($res);
                exit;
            }
            $new_name = rand() . '_' . time() . '.' . end($temp);
            $isUploaded = move_uploaded_file($request['file']['tmp_name'], $target_dir . $new_name);
        }
        $post = $this->Posts->newEntity();
        if (!empty($request['caption']) || $isUploaded == true) {
            $post = $this->Posts->patchEntity($post, $request);
            $post->media = $new_name;
            $post->pet_id = $profile['id'];
            $post->user_id = $this->Auth->user('id');
            $post->status = 1;
            $post->host = null;
            $post->created = GMT_DATETIME;
            $post->modified = GMT_DATETIME;
            if ($this->Posts->save($post)) {
                $res['success']['status_code'] = 1;
                $res['success']['message'] = 'Published Successfully!';
            } else {
                $res['error']['status_code'] = 0;
                $res['error']['message'] = 'Error while publishing the post!';
            }
        } else {
            $res['error']['status_code'] = 0;
            $res['error']['message'] = 'Error while publishing the post!';
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
        $this->loadModel('Comments');
        $profile = $this->Profiles->findByUniq_id(trim($uniq_id))->select('id')->first();
        if (!$profile['id'] || $profile['id'] < 1) {
            $res['error']['status_code'] = 0;
            $res['error']['message'] = 'Sorry, Profile does not exist.';
            echo json_encode($res);
            exit;
        }
        $my_posts = $this->Posts->find('all', [
            'contain' => ['Comments' => [
                'conditions' => ['Comments.is_active' => 1],
                'sort' => ['Comments.created' => 'DESC'],
            ]],
            'conditions' => ['pet_id' => $profile['id'], 'Posts.is_active' => 1],
            'fields' => ['Posts.id', 'Posts.media', 'Posts.caption', 'Posts.created', 'Posts.modified'],
            'order' => ['Posts.created DESC'],
        ])->toArray();

        if ($my_posts) {
            $this->loadModel('Likes');
            for ($i = 0; $i < count($my_posts); $i++) {
                $data[$i]['id'] = $my_posts[$i]->id;
                $data[$i]['media'] = $my_posts[$i]->media;
                $data[$i]['caption'] = $my_posts[$i]->caption;
                $data[$i]['created'] = $my_posts[$i]->created;
                $data[$i]['modified'] = $my_posts[$i]->modified;
                $query = $this->Likes->find('all')
                    ->where(['Likes.post_id' => $my_posts[$i]->id, 'Likes.is_active' => 1]);
                $total_likes = $query->count();
                $data[$i]['total_likes'] = $total_likes;
                $data[$i]['Comments'] = $my_posts[$i]['comments'];

            }
            $res['result']['FilePath'] = $target_dir;
            $res['result']['Posts'] = $data;
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
