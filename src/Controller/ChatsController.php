<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\Datasource\ConnectionManager;

/**
 * Chats Controller
 *
 * @property \App\Model\Table\ChatsTable $Chats
 *
 * @method \App\Model\Entity\Chat[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class ChatsController extends AppController
{
    public $helpers = array('Html', 'Form');
    public function initialize()
    {
        parent::initialize();
        $this->loadComponent('Format');
        $this->autoRender = false;
    }

    public function postChat()
    {
        $res['error'] = [];
        $res['success'] = [];
        $res['result'] = [];

        $request = $this->request->getData();

        if (!$request) {
            $res['error']['status_code'] = 0;
            $res['error']['message'] = 'Empty Request!';
            echo json_encode($res);
            exit;
        }

        $sender = $this->Format->getProfileId($request['sender_id']);
        $receiver = $this->Format->getProfileId($request['receiver_id']);

        if (!isset($receiver) || !isset($sender)) {
            $res['error']['status_code'] = 0;
            $res['error']['message'] = 'Empty Request!';
            echo json_encode($res);
            exit;
        }
        $message = $request['message'];

        $chat = $this->Chats->newEntity();
        $chat = $this->Chats->patchEntity($chat, $request);

        $chat->sender_id = $sender;
        $chat->receiver_id = $receiver;
        $chat->message = $message;
        $chat->ip = $this->request->clientIp();
        $chat->created = GMT_DATETIME;
        $chat->modified = GMT_DATETIME;

        if ($this->Chats->save($chat)) {
            $res['success']['status_code'] = 1;
            $res['success']['message'] = 'Message Sent.';
        } else {
            $res['error']['status_code'] = 0;
            $res['error']['message'] = 'Unable to send message.';
        }

        $responseResult = json_encode($res);
        $this->response->type('json');
        $this->response->body($responseResult);
    }
    public function getChat()
    {
        $res['error'] = [];
        $res['success'] = [];
        $res['result'] = [];

        $request = $this->request->getData();

        if (!$request) {
            $res['error']['status_code'] = 0;
            $res['error']['message'] = 'Empty Request!';
            echo json_encode($res);
            exit;
        }

        $sender = $this->Format->getProfileId($request['sender_id']);
        $receiver = $this->Format->getProfileId($request['receiver_id']);

        if (!isset($receiver) || !isset($sender)) {
            $res['error']['status_code'] = 0;
            $res['error']['message'] = 'Empty Request!';
            echo json_encode($res);
            exit;
        }
        $sql = " SELECT Chat.*,Sender.pet_name,Sender.uniq_id FROM (SELECT * from chats  WHERE  ((sender_id=" . $sender . " AND  receiver_id=" . $receiver . ") OR (sender_id=" . $receiver . " AND  receiver_id=" . $sender . "))  ORDER BY created DESC) AS Chat LEFT JOIN profiles AS Sender ON (Chat.sender_id=Sender.id)  LEFT JOIN profiles AS Receiver ON (Chat.sender_id=Receiver.id) ORDER BY created ASC";
        $connection = ConnectionManager::get('default');
        $results = $connection->execute($sql)->fetchAll('assoc');
        $res['result'] = $results;

        $responseResult = json_encode($res);
        $this->response->type('json');
        $this->response->body($responseResult);
    }

}
