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
        $pets = $this->Pets->find('all', [
            'keyField' => 'id',
            'valueField' => 'name',
        ])->toArray();
        if ($pets && !empty($pets)) {
            for($i=0;$i<count($pets);$i++){
                $data[$i]['id'] = $pets[$i]['id'];
                $data[$i]['name'] = $pets[$i]['name'];
            }
            $res['result'] = $data;
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
            $breeds = $this->Breeds->find('all', [
                'keyField' => 'id',
                'valueField' => 'name',
            ])->toArray();
        } else {
            $breeds = $this->Breeds->find('all', [
                'keyField' => 'id',
                'valueField' => 'name',
                'conditions' => ['Breeds.pet_id' => trim($pet_id)],
            ])->toArray();
        }
        if ($breeds && !empty($breeds)) {
            for($i=0;$i<count($breeds);$i++){
                $data[$i]['id'] = $breeds[$i]['id'];
                $data[$i]['name'] = $breeds[$i]['name'];
            }
            $res['result'] = $data;
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
            $professions = $this->Professions->find('all', [
                'keyField' => 'id',
                'valueField' => 'name',
            ])->toArray();
        } else {
            $professions = $this->Professions->find('all', [
                'keyField' => 'id',
                'valueField' => 'name',
                'conditions' => ['Professions.id' => trim($id)],
            ])->toArray();
        }
        if ($professions && !empty($professions)) {
            for($i=0;$i<count($professions);$i++){
                $data[$i]['id'] = $professions[$i]['id'];
                $data[$i]['name'] = $professions[$i]['name'];
            }
            $res['result'] = $data;
        } else {
            $res['error']['status_code'] = 0;
            $res['error']['message'] = 'No data found';
        }
        $responseResult = json_encode($res);
        $this->response->type('json');
        $this->response->body($responseResult);
    }
}
