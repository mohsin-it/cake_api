<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * RoleActions Controller
 *
 * @property \App\Model\Table\RoleActionsTable $RoleActions
 *
 * @method \App\Model\Entity\RoleAction[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class RoleActionsController extends AppController
{

    /**
     * Index method
     *
     * @return \Cake\Http\Response|void
     */
    public function index()
    {
        $this->paginate = [
            'contain' => ['Roles', 'Actions']
        ];
        $roleActions = $this->paginate($this->RoleActions);

        $this->set(compact('roleActions'));
    }

    /**
     * View method
     *
     * @param string|null $id Role Action id.
     * @return \Cake\Http\Response|void
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $roleAction = $this->RoleActions->get($id, [
            'contain' => ['Roles', 'Actions']
        ]);

        $this->set('roleAction', $roleAction);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $roleAction = $this->RoleActions->newEntity();
        if ($this->request->is('post')) {
            $roleAction = $this->RoleActions->patchEntity($roleAction, $this->request->getData());
            if ($this->RoleActions->save($roleAction)) {
                $this->Flash->success(__('The role action has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The role action could not be saved. Please, try again.'));
        }
        $roles = $this->RoleActions->Roles->find('list', ['limit' => 200]);
        $actions = $this->RoleActions->Actions->find('list', ['limit' => 200]);
        $this->set(compact('roleAction', 'roles', 'actions'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Role Action id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $roleAction = $this->RoleActions->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $roleAction = $this->RoleActions->patchEntity($roleAction, $this->request->getData());
            if ($this->RoleActions->save($roleAction)) {
                $this->Flash->success(__('The role action has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The role action could not be saved. Please, try again.'));
        }
        $roles = $this->RoleActions->Roles->find('list', ['limit' => 200]);
        $actions = $this->RoleActions->Actions->find('list', ['limit' => 200]);
        $this->set(compact('roleAction', 'roles', 'actions'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Role Action id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $roleAction = $this->RoleActions->get($id);
        if ($this->RoleActions->delete($roleAction)) {
            $this->Flash->success(__('The role action has been deleted.'));
        } else {
            $this->Flash->error(__('The role action could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
