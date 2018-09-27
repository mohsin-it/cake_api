<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * Groups Controller
 *
 * @property \App\Model\Table\GroupsTable $Groups
 *
 * @method \App\Model\Entity\Group[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class GroupsController extends AppController
{

    public $helpers = array('Html', 'Form');
    public function initialize()
    {
        parent::initialize();
        $this->loadComponent('Format');
        $this->autoRender = false;
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $res['error'] = [];
        $res['success'] = [];
        $res['result'] = [];

        $request = $this->request->getData();
        $group = $this->Groups->newEntity();
        $group = $this->Groups->patchEntity($group, $request);

        $group->user_id = $this->Auth->user('id');
        $group->is_active = 1;
        $group->created = GMT_DATETIME;
        $group->modified = GMT_DATETIME;
        if ($this->Groups->save($group)) {
            $res['success']['status_code'] = 1;
            $res['success']['message'] = 'Group Created Successfully!';
        } else {
            $res['error']['status_code'] = 0;
            $res['error']['message'] = 'Error while creating group!';
        }

        $responseResult = json_encode($res);
        $this->response->type('json');
        $this->response->body($responseResult);
    }
}
