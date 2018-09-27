<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\Datasource\ConnectionManager;

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
                for ($i = 0; $i < count($profiles); $i++) {
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
    public function searchProfile()
    {
        $res['error'] = [];
        $res['success'] = [];
        $res['result'] = [];

        $request = $this->request->getData();
        $keyword = '';

        if (!$request['profile_id']) {
            $res['error']['status_code'] = 0;
            $res['error']['message'] = 'Empty Request!';
            echo json_encode($res);
            exit;
        }
        $keyword = $request['keyword'];
        $profile_id = $request['profile_id'];

        $this->loadModel('Friends');
        $requester = $this->Format->getProfileId($profile_id);
        $sql = "SELECT * FROM friends WHERE status IN (1,2,4) AND (requester_id=$requester OR receiver_id=$requester)";
        $connection = ConnectionManager::get('default');
        $results = $connection->execute($sql)->fetchAll('assoc');
        $ids[] = $requester;
        foreach ($results as $k => $val) {
            $ids[] = $val['requester_id'];
            $ids[] = $val['receiver_id'];
        }
        //print_r($ids);exit;
        $profiles = $this->Profiles->find('all', [
            'conditions' => ['Profiles.pet_name LIKE' => "%$keyword%", 'Profiles.id NOT IN' => array_unique($ids)],
            // 'fields' => ['Profiles.pet_name', 'Profiles.id'],
        ])->toArray();
        if (isset($profiles) && !empty($profiles)) {
            for ($i = 0; $i < count($profiles); $i++) {
                $data[$i]['id'] = $profiles[$i]['uniq_id'];
                $data[$i]['name'] = $profiles[$i]['pet_name'];
            }
            $res['result'] = $data;
        } else {
            $res['error']['status_code'] = 0;
            $res['error']['message'] = 'No result found!';
        }
        $responseResult = json_encode($res);
        $this->response->type('json');
        $this->response->body($responseResult);
    }
    public function sendRequest()
    {
        $res['error'] = [];
        $res['success'] = [];
        $res['result'] = [];

        $request = $this->request->getData();
        $st = $request['status'];
        $this->loadModel('Friends');

        $requester = $this->Format->getProfileId($request['profile_id']);
        $receiver = $this->Format->getProfileId($request['receiver_id']);
        $actioner = $this->Format->getProfileId($request['profile_id']);
        switch ($st) {
            case 1:
                $isExist = $this->Friends->find('all', [
                    'conditions' => ['OR' => [
                        'requester_id IN' => array($requester, $receiver),
                    ],
                        [
                            'receiver_id IN' => array($requester, $receiver)],
                    ],
                ])->first();
                if (!empty($isExist)) {
                    if ($isExist['status'] == 3 || $isExist['status'] == 5) {
                        $isExist->status = $st;
                        $isExist->modified = GMT_DATETIME;
                        if ($this->Friends->save($isExist)) {
                            $res['success']['status_code'] = 1;
                            $res['success']['message'] = 'Request Sent.';
                        } else {
                            $res['error']['status_code'] = 0;
                            $res['error']['message'] = 'Unable to send request.';
                        }
                    } else if ($isExist['status'] == 2) {
                        $res['error']['status_code'] = 0;
                        $res['error']['message'] = 'Pet is already friend.';
                    } else if ($isExist['status'] == 1) {
                        $res['error']['status_code'] = 0;
                        $res['error']['message'] = 'Request already sent.';
                    } else if ($isExist['status'] == 4) {
                        $res['error']['status_code'] = 0;
                        $res['error']['message'] = 'Profile is blocked by user.';
                    }
                } else {
                    $friend = $this->Friends->newEntity();
                    $friend = $this->Friends->patchEntity($friend, $request);

                    $friend->requester_id = $requester;
                    $friend->receiver_id = $receiver;
                    $friend->actioner_id = $requester;
                    $friend->status = $st;
                    $friend->created = GMT_DATETIME;
                    $friend->modified = GMT_DATETIME;
                    if ($this->Friends->save($friend)) {
                        $res['success']['status_code'] = 1;
                        $res['success']['message'] = 'Request Sent.';
                    } else {
                        $res['error']['status_code'] = 0;
                        $res['error']['message'] = 'Unable to send request.';
                    }
                }
                break;
            case 2:
                $friend = $this->Friends->find('all', [
                    'conditions' => ['requester_id' => $requester, 'receiver_id' => $receiver],
                ])->first();
                if ($friend) {
                    $friend->status = $st;
                    $friend->modified = GMT_DATETIME;
                    if ($this->Friends->save($friend)) {
                        $res['success']['status_code'] = 1;
                        $res['success']['message'] = 'Request accepted.';
                    } else {
                        $res['error']['status_code'] = 0;
                        $res['error']['message'] = 'Pleas try again.';
                    }
                } else {
                    $res['error']['status_code'] = 0;
                    $res['error']['message'] = 'No Request or Request canceled by sender.';
                }
                break;
            case 3:
                $friend = $this->Friends->find('all', [
                    'conditions' => ['requester_id' => $requester, 'receiver_id' => $receiver],
                ])->first();
                if ($friend) {
                    $friend->status = $st;
                    $friend->modified = GMT_DATETIME;
                    if ($this->Friends->save($friend)) {
                        $res['success']['status_code'] = 1;
                        $res['success']['message'] = 'Request rejected.';
                    } else {
                        $res['error']['status_code'] = 0;
                        $res['error']['message'] = 'Pleas try again.';
                    }
                } else {
                    $res['error']['status_code'] = 0;
                    $res['error']['message'] = 'No Request or Request canceled by sender.';
                }
                break;
            case 4:
                $friend = $this->Friends->find('all', [
                    'conditions' => ['requester_id' => $requester, 'receiver_id' => $receiver],
                ])->first();
                if ($friend) {
                    $friend->status = $st;
                    $friend->modified = GMT_DATETIME;
                    if ($this->Friends->save($friend)) {
                        $res['success']['status_code'] = 1;
                        $res['success']['message'] = 'Profile blocked.';
                    } else {
                        $res['error']['status_code'] = 0;
                        $res['error']['message'] = 'Pleas try again.';
                    }
                } else {
                    $res['error']['status_code'] = 0;
                    $res['error']['message'] = 'No Request or Request canceled by sender.';
                }
                break;
            case 5:
                $friend = $this->Friends->find('all', [
                    'conditions' => ['requester_id' => $requester, 'receiver_id' => $receiver],
                ])->first();
                if ($friend) {
                    $friend->status = $st;
                    $friend->modified = GMT_DATETIME;
                    if ($this->Friends->save($friend)) {
                        $res['success']['status_code'] = 1;
                        $res['success']['message'] = 'Request canceled.';
                    } else {
                        $res['error']['status_code'] = 0;
                        $res['error']['message'] = 'Pleas try again.';
                    }
                } else {
                    $res['error']['status_code'] = 0;
                    $res['error']['message'] = 'No Request or Request canceled by sender.';
                }
                break;

        }
        $responseResult = json_encode($res);
        $this->response->type('json');
        $this->response->body($responseResult);
    }

    public function newRequests()
    {
        $res['error'] = [];
        $res['success'] = [];
        $res['result'] = [];

        $request = $this->request->getData();
        $profile_id = $request['profile_id'];
        $receiver = $this->Format->getProfileId($profile_id);

        $this->loadModel('Friends');
        $friends = $this->Friends->find('list', [
            'conditions' => ['receiver_id' => $receiver, 'status' => 1],
            'keyField' => 'id',
            'valueField' => 'requester_id',
        ])->toArray();

        if (!empty($friends)) {
            $profiles = $this->Profiles->find('all', [
                'conditions' => ['Profiles.id IN' => $friends],
            ])->toArray();
            if (isset($profiles) && !empty($profiles)) {
                for ($i = 0; $i < count($profiles); $i++) {
                    $data[$i]['id'] = $profiles[$i]['uniq_id'];
                    $data[$i]['name'] = $profiles[$i]['pet_name'];
                }
                $res['result'] = $data;
            } else {
                $res['error']['status_code'] = 0;
                $res['error']['message'] = 'No new request found!';
            }
        } else {
            $res['error']['status_code'] = 0;
            $res['error']['message'] = 'No new request found!';
        }
        // print_r($friends);exit;
        $responseResult = json_encode($res);
        $this->response->type('json');
        $this->response->body($responseResult);
    }
    public function sentRequests()
    {
        $res['error'] = [];
        $res['success'] = [];
        $res['result'] = [];

        $request = $this->request->getData();
        $profile_id = $request['profile_id'];
        $requester = $this->Format->getProfileId($profile_id);

        $this->loadModel('Friends');
        $friends = $this->Friends->find('list', [
            'conditions' => ['requester_id' => $requester, 'status' => 1],
            'keyField' => 'id',
            'valueField' => 'receiver_id',
        ])->toArray();
        if (!empty($friends)) {
            $profiles = $this->Profiles->find('all', [
                'conditions' => ['Profiles.id IN' => $friends],
            ])->toArray();
            if (isset($profiles) && !empty($profiles)) {
                for ($i = 0; $i < count($profiles); $i++) {
                    $data[$i]['id'] = $profiles[$i]['uniq_id'];
                    $data[$i]['name'] = $profiles[$i]['pet_name'];
                }
                $res['result'] = $data;
            } else {
                $res['error']['status_code'] = 0;
                $res['error']['message'] = 'No request sent!';
            }
        } else {
            $res['error']['status_code'] = 0;
            $res['error']['message'] = 'No request sent!';
        }
        // print_r($friends);exit;
        $responseResult = json_encode($res);
        $this->response->type('json');
        $this->response->body($responseResult);
    }
    public function friendList()
    {
        $res['error'] = [];
        $res['success'] = [];
        $res['result'] = [];

        $request = $this->request->getData();
        if (!isset($request['profile_id'])) {
            $res['error']['status_code'] = 0;
            $res['error']['message'] = 'Empty Request!';
            echo json_encode($res);
            exit;
        }
        $profile_id = $request['profile_id'];
        $requester = $this->Format->getProfileId($profile_id);

        $this->loadModel('Friends');
        $friends = $this->Friends->find('all', [
            'conditions' => ['status' => 2, 'OR' => [
                'requester_id' => $requester, 'receiver_id' => $requester,
            ],
            ],

        ])->toArray();
        $friend_ids = array();
        foreach ($friends as $friend) {
            if ($friend['requester_id'] != $requester) {
                $friend_ids[] = $friend['requester_id'];
                //  $friend_ids[] = $friend['receiver_id'];
            }
            if ($friend['receiver_id'] != $requester) {
                // $friend_ids[] = $friend['requester_id'];
                $friend_ids[] = $friend['receiver_id'];
            }
        }
        if (!empty($friend_ids)) {
            $profiles = $this->Profiles->find('all', [
                'conditions' => ['Profiles.id IN' => $friend_ids],
            ])->toArray();
            if (isset($profiles) && !empty($profiles)) {
                for ($i = 0; $i < count($profiles); $i++) {
                    $data[$i]['id'] = $profiles[$i]['uniq_id'];
                    $data[$i]['name'] = $profiles[$i]['pet_name'];
                }
                $res['result'] = $data;
            } else {
                $res['error']['status_code'] = 0;
                $res['error']['message'] = 'No friends in friend list!';
            }
        } else {
            $res['error']['status_code'] = 0;
            $res['error']['message'] = 'No friends in friend list!';
        }
        $responseResult = json_encode($res);
        $this->response->type('json');
        $this->response->body($responseResult);
    }
}
