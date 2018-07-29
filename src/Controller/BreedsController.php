<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * Breeds Controller
 *
 * @property \App\Model\Table\BreedsTable $Breeds
 *
 * @method \App\Model\Entity\Breed[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class BreedsController extends AppController
{

    /**
     * Index method
     *
     * @return \Cake\Http\Response|void
     */
    public function index()
    {
        $this->paginate = [
            'contain' => ['Pets']
        ];
        $breeds = $this->paginate($this->Breeds);

        $this->set(compact('breeds'));
    }

    /**
     * View method
     *
     * @param string|null $id Breed id.
     * @return \Cake\Http\Response|void
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $breed = $this->Breeds->get($id, [
            'contain' => ['Pets', 'Profiles']
        ]);

        $this->set('breed', $breed);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $breed = $this->Breeds->newEntity();
        if ($this->request->is('post')) {
            $breed = $this->Breeds->patchEntity($breed, $this->request->getData());
            if ($this->Breeds->save($breed)) {
                $this->Flash->success(__('The breed has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The breed could not be saved. Please, try again.'));
        }
        $pets = $this->Breeds->Pets->find('list', ['limit' => 200]);
        $this->set(compact('breed', 'pets'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Breed id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $breed = $this->Breeds->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $breed = $this->Breeds->patchEntity($breed, $this->request->getData());
            if ($this->Breeds->save($breed)) {
                $this->Flash->success(__('The breed has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The breed could not be saved. Please, try again.'));
        }
        $pets = $this->Breeds->Pets->find('list', ['limit' => 200]);
        $this->set(compact('breed', 'pets'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Breed id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $breed = $this->Breeds->get($id);
        if ($this->Breeds->delete($breed)) {
            $this->Flash->success(__('The breed has been deleted.'));
        } else {
            $this->Flash->error(__('The breed could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
