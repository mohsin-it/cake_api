<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * Profiles Controller
 *
 * @property \App\Model\Table\ProfilesTable $Profiles
 *
 * @method \App\Model\Entity\Profile[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class ProfilesController extends AppController
{

    public $helpers = array('Html', 'Form');
    public function initialize()
    {
        parent::initialize();
        $this->loadComponent('Format');
        $this->autoRender = false;
    }

    // Apis Starts

    public function addProfile()
    {
        $res['error'] = [];
        $res['success'] = [];
        $res['result'] = [];

        if (array_key_exists('Create Profile', $GLOBALS['roleAccess']) && $GLOBALS['roleAccess']['Create Profile']['isAllowed'] == 1) {
            $request = $this->request->getData();
            $profile = $this->Profiles->newEntity($request);
            $profile = $this->Profiles->patchEntity($profile, $request);
            if (!trim($profile->pet_name)) {
                $res['error']['status_code'] = 0;
                $res['error']['message'] = 'Pet Name is required!';
                echo json_encode($res);
                exit;
            }
            $query = $this->Profiles->find('all')
                ->where(['Profiles.owner_id' => $this->Auth->user('id')]);
            $existing_profile = $query->count();
            if ($existing_profile >= ALLOWED_PROFILE) {
                $res['error']['status_code'] = 0;
                $res['error']['message'] = 'Maximum profile creation limit reached!';
                $responseResult = json_encode($res);
                echo $responseResult;
                exit;
            }
            $profile->uniq_id = $this->Format->generateUniqNumber();
            $profile->owner_id = $this->Auth->user('id');
            $profile->created = GMT_DATETIME;
            $profile->modified = GMT_DATETIME;
            if ($this->Profiles->save($profile)) {
                $res['success']['status_code'] = 1;
                $res['success']['message'] = 'Profile Created Successfully!';
            } else {
                $res['error']['status_code'] = 0;
                $res['error']['message'] = 'Error while creating profile!';
            }
        } else {
            $res['error']['status_code'] = 0;
            $res['error']['message'] = 'Action not allowed!';
        }
        $responseResult = json_encode($res);
        $this->response->type('json');
        $this->response->body($responseResult);
    }
    public function profileList()
    {
        $res['error'] = [];
        $res['success'] = [];
        $res['result'] = [];
        if (array_key_exists('View Profile', $GLOBALS['roleAccess']) && $GLOBALS['roleAccess']['View Profile']['isAllowed'] == 1) {
            $profiles = $this->Profiles->find('all', [
                'keyField' => 'uniq_id',
                'valueField' => 'pet_name',
                'conditions' => ['Profiles.owner_id' => $this->Auth->user('id')],
                // 'fields' => ['Profiles.pet_name', 'Profiles.id'],
            ])->toArray();
            if (isset($profiles) && !empty($profiles)) {
                for($i=0;$i<count($profiles);$i++){
                    $data[$i]['id'] = $profiles[$i]['uniq_id'];
                    $data[$i]['name'] = $profiles[$i]['pet_name'];
                }
                $res['result'] = $data;
            } else {
                $res['error']['status_code'] = 0;
                $res['error']['message'] = 'NO data found!';
            }
        } else {
            $res['error']['status_code'] = 0;
            $res['error']['message'] = 'Action not allowed!';
        }
        $responseResult = json_encode($res);
        $this->response->type('json');
        $this->response->body($responseResult);
    }
    public function viewProfile($uniq_id = null)
    {
        $res['error'] = [];
        $res['success'] = [];
        $res['result'] = [];
        if (!trim($uniq_id)) {
            $res['error']['status_code'] = 0;
            $res['error']['message'] = 'Empty request!';
            echo json_encode($res);
            exit;
        }
        if (array_key_exists('View Profile', $GLOBALS['roleAccess']) && $GLOBALS['roleAccess']['View Profile']['isAllowed'] == 1) {
            $profile = $this->Profiles->find('all', [
                'contain' => ['Users', 'Pets', 'Breeds'],
                'conditions' => ['Profiles.uniq_id' => trim($uniq_id), 'Profiles.owner_id' => $this->Auth->user('id')],
                // 'fields' => ['Profiles.pet_name', 'Profiles.id'],
            ])->first();
            if (isset($profile) && !empty($profile)) {
                $res['result']['Pet'] = $profile;
            } else {
                $res['error']['status_code'] = 0;
                $res['error']['message'] = 'NO profile found!';
            }
        } else {
            $res['error']['status_code'] = 0;
            $res['error']['message'] = 'Action not allowed!';
        }
        $responseResult = json_encode($res);
        $this->response->type('json');
        $this->response->body($responseResult);
    }

}
