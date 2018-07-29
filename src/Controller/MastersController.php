<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * Masters Controller
 *
 *
 * @method \App\Model\Entity\Master[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class MastersController extends AppController
{

    // All The Masters
    public function initialize()
    {
        parent::initialize();
        $this->loadComponent('Format');
        $this->autoRender = false;
        $this->Auth->allow();
    }

    public function getPets()
    {

        $res['error'] = [];
        $res['success'] = [];
        $res['result'] = [];
        $this->loadModel('Pets');
        $pets = $this->Pets->find('list', [
            'keyField' => 'id',
            'valueField' => 'name',
        ])->toArray();
        if ($pets && !empty($pets)) {
            $res['result']['Pets'] = $pets;
        } else {
            $res['error']['status_code'] = 0;
            $res['error']['message'] = 'No data found';
        }
        $responseResult = json_encode($res);
        $this->response->type('json');
        $this->response->body($responseResult);
    }
    public function getBreeds($pet_id = null)
    {

        $res['error'] = [];
        $res['success'] = [];
        $res['result'] = [];
        $this->loadModel('Breeds');
        if (!trim($pet_id)) {
            $breeds = $this->Breeds->find('list', [
                'keyField' => 'id',
                'valueField' => 'name',
            ])->toArray();
        } else {
            $breeds = $this->Breeds->find('list', [
                'keyField' => 'id',
                'valueField' => 'name',
                'conditions' => ['Breeds.pet_id' => trim($pet_id)],
            ])->toArray();
        }
        if ($breeds && !empty($breeds)) {
            $res['result']['Breeds'] = $breeds;
        } else {
            $res['error']['status_code'] = 0;
            $res['error']['message'] = 'No data found';
        }
        $responseResult = json_encode($res);
        $this->response->type('json');
        $this->response->body($responseResult);
    }
    public function getProfession($id = null)
    {

        $res['error'] = [];
        $res['success'] = [];
        $res['result'] = [];
        $this->loadModel('Professions');
        if (!trim($id)) {
            $professions = $this->Professions->find('list', [
                'keyField' => 'id',
                'valueField' => 'name',
            ])->toArray();
        } else {
            $professions = $this->Professions->find('list', [
                'keyField' => 'id',
                'valueField' => 'name',
                'conditions' => ['Professions.id' => trim($id)],
            ])->toArray();
        }
        if ($professions && !empty($professions)) {
            $res['result']['Breeds'] = $professions;
        } else {
            $res['error']['status_code'] = 0;
            $res['error']['message'] = 'No data found';
        }
        $responseResult = json_encode($res);
        $this->response->type('json');
        $this->response->body($responseResult);
    }
}
