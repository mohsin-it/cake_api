<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * Comments Controller
 *
 * @property \App\Model\Table\CommentsTable $Comments
 *
 * @method \App\Model\Entity\Comment[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class CommentsController extends AppController
{

    public $helpers = array('Html', 'Form');
    public function initialize()
    {
        parent::initialize();
        $this->loadComponent('Format');
        $this->autoRender = false;
    }

    // APis

    public function comment()
    {
        $res['error'] = [];
        $res['success'] = [];
        $res['result'] = [];

        $comment = $this->Comments->newEntity();
        $comment = $this->Comments->patchEntity($comment, $this->request->getData());
        if (!$comment->post_id || $comment->post_id < 1) {
            $res['error']['status_code'] = 0;
            $res['error']['message'] = 'Empty request!';
            echo json_encode($res);
            exit;
        }
        $this->loadModel('Posts');
        $post = $this->Posts->findById($comment->post_id)->select('id')->first();
        if (!$post['id'] || $post['id'] < 1) {
            $res['error']['status_code'] = 0;
            $res['error']['message'] = 'Sorry, Post does not exist.';
            echo json_encode($res);
            exit;
        }
        $comment->comment = trim($comment->comment);
        $comment->user_id = $this->Auth->user('id');
        $comment->is_active = 1;
        $comment->created = GMT_DATETIME;
        $comment->modified = GMT_DATETIME;

        if ($this->Comments->save($comment)) {
            $res['success']['status_code'] = 1;
            $res['success']['message'] = 'Comment Posted Successfully.';
        } else {
            $res['error']['status_code'] = 0;
            $res['error']['message'] = 'Error while saving data!';
        }
        $responseResult = json_encode($res);
        $this->response->type('json');
        $this->response->body($responseResult);
    }

    public function getComments()
    {
        $res['error'] = [];
        $res['success'] = [];
        $res['result'] = [];

        $data = $this->request->getData();
        if (!$data || $data['post_id'] < 1) {
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

        $query = $this->Comments->find('all')
            ->where(['Comments.post_id' => $data['post_id'], 'Comments.is_active' => 1]);
        $total_comments = $query->count();
        $comments = $query->toArray();
        $res['result']['TotalComments'] = $total_comments;
        $res['result']['Comments'] = $comments;
        $responseResult = json_encode($res);
        $this->response->type('json');
        $this->response->body($responseResult);
    }
}
