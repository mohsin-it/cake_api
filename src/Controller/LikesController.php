<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * Likes Controller
 *
 * @property \App\Model\Table\LikesTable $Likes
 *
 * @method \App\Model\Entity\Like[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class LikesController extends AppController
{

    public $helpers = array('Html', 'Form');
    public function initialize()
    {
        parent::initialize();
        $this->loadComponent('Format');
        $this->autoRender = false;
    }

    // APIs

    public function like()
    {
        $res['error'] = [];
        $res['success'] = [];
        $res['result'] = [];

        $like = $this->Likes->newEntity();
        $like = $this->Likes->patchEntity($like, $this->request->getData());
        if (!$like->post_id || $like->post_id < 1) {
            $res['error']['status_code'] = 0;
            $res['error']['message'] = 'Empty request!';
            echo json_encode($res);
            exit;
        }
        $this->loadModel('Posts');
        $post = $this->Posts->findById($like->post_id)->select('id')->first();
        if (!$post['id'] || $post['id'] < 1) {
            $res['error']['status_code'] = 0;
            $res['error']['message'] = 'Sorry, Post does not exist.';
            echo json_encode($res);
            exit;
        }
        $query = $this->Likes->find('all')
            ->where(['Likes.user_id' => $this->Auth->user('id'), 'Likes.post_id' => $like->post_id]);
        $isLiked = $query->count();
        if ($isLiked && $isLiked > 0) {
            $like = $query->first();
            $status = 0;
            $msg = "Post Un-Liked.";
            if ($like['is_active'] == 0) {
                $status = 1;
                $msg = "Post Liked.";
            }
            $like->is_active = $status;
            if ($this->Likes->save($like)) {
                $res['success']['status_code'] = 1;
                $res['success']['message'] = $msg;
            } else {
                $res['error']['status_code'] = 0;
                $res['error']['message'] = 'Error while saving data!';
            }
        } else {
            $like->user_id = $this->Auth->user('id');
            $like->is_active = 1;
            $like->created = GMT_DATETIME;
            $like->modified = GMT_DATETIME;
            if ($this->Likes->save($like)) {
                $res['success']['status_code'] = 1;
                $res['success']['message'] = 'Post Liked.';
            } else {
                $res['error']['status_code'] = 0;
                $res['error']['message'] = 'Error while saving data!';
            }
        }
        $query = $this->Likes->find('all')
            ->where(['Likes.post_id' => $like->post_id, 'Likes.is_active' => 1]);
        $total_likes = $query->count();
        $res['result']['TotalLikes'] = $total_likes;
        $responseResult = json_encode($res);
        $this->response->type('json');
        $this->response->body($responseResult);
    }

    public function likeCounts()
    {

        $res['error'] = [];
        $res['success'] = [];
        $res['result'] = [];

        $data = $this->request->getData();
        if (!$data['post_id'] || $data['post_id'] < 1) {
            $res['error']['status_code'] = 0;
            $res['error']['message'] = 'Empty request!';
            echo json_encode($res);
            exit;
        }
        $this->loadModel('Posts');
        $post = $this->Posts->findById($data['post_id'])->select('id')->first();
        if (!$post['id'] || $post['id'] < 1) {
            $res['error']['status_code'] = 0;
            $res['error']['message'] = 'Sorry, Post does not exist.';
            echo json_encode($res);
            exit;
        }
        $query = $this->Likes->find('all')
            ->where(['Likes.post_id' => $data['post_id'], 'Likes.is_active' => 1]);
        $total_likes = $query->count();
        $res['result']['TotalLikes'] = $total_likes;
        $responseResult = json_encode($res);
        $this->response->type('json');
        $this->response->body($responseResult);

    }
}
