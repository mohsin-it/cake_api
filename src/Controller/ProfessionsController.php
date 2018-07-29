<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * Professions Controller
 *
 * @property \App\Model\Table\ProfessionsTable $Professions
 *
 * @method \App\Model\Entity\Profession[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class ProfessionsController extends AppController
{

    /**
     * Index method
     *
     * @return \Cake\Http\Response|void
     */
    public function index()
    {
        $professions = $this->paginate($this->Professions);

        $this->set(compact('professions'));
    }

    /**
     * View method
     *
     * @param string|null $id Profession id.
     * @return \Cake\Http\Response|void
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $profession = $this->Professions->get($id, [
            'contain' => ['UserDetails']
        ]);

        $this->set('profession', $profession);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $profession = $this->Professions->newEntity();
        if ($this->request->is('post')) {
            $profession = $this->Professions->patchEntity($profession, $this->request->getData());
            if ($this->Professions->save($profession)) {
                $this->Flash->success(__('The profession has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The profession could not be saved. Please, try again.'));
        }
        $this->set(compact('profession'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Profession id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $profession = $this->Professions->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $profession = $this->Professions->patchEntity($profession, $this->request->getData());
            if ($this->Professions->save($profession)) {
                $this->Flash->success(__('The profession has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The profession could not be saved. Please, try again.'));
        }
        $this->set(compact('profession'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Profession id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $profession = $this->Professions->get($id);
        if ($this->Professions->delete($profession)) {
            $this->Flash->success(__('The profession has been deleted.'));
        } else {
            $this->Flash->error(__('The profession could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
