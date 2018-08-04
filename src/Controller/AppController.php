<?php
/**
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link      https://cakephp.org CakePHP(tm) Project
 * @since     0.2.9
 * @license   https://opensource.org/licenses/mit-license.php MIT License
 */
namespace App\Controller;

use Cake\Controller\Controller;
use Cake\Event\Event;

/**
 * Application Controller
 *
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 *
 * @link https://book.cakephp.org/3.0/en/controllers.html#the-app-controller
 */
class AppController extends Controller
{

    /**
     * Initialization hook method.
     *
     * Use this method to add common initialization code like loading components.
     *
     * e.g. `$this->loadComponent('Security');`
     *
     * @return void
     */
    public function initialize()
    {
        parent::initialize();

        $this->loadComponent('RequestHandler');
        $this->loadComponent('Flash');
        $this->loadComponent('Auth', [
            'loginAction' => [
                'plugin' => false,
                'controller' => 'Users',
                'action' => 'login',
            ],
            'authError' => 'You are not authorized to access that location.',
            'flash' => [
                'element' => 'error',
            ],
            'authenticate' => [
                'Form' => [
                    'userModel' => 'Users',
                    'fields' => [
                        'username' => 'email',
                        'password' => 'password',
                    ],
                    'scope' => array('Users.is_verified' => 1),
                ],
            ],
        ]);

        /*
         * Enable the following components for recommended CakePHP security settings.
         * see https://book.cakephp.org/3.0/en/controllers/components/security.html
         */
        // $this->loadComponent('Security');
       // $this->loadComponent('Csrf');
        $GLOBALS['roleAccess'] = [];
        if ($this->Auth->user()) {
            $this->loadModel('Actions');
            $this->loadModel('RoleActions');
            $query = $this->Actions->find('all', [
                'join' => [
                    'RoleActions' => [
                        'table' => 'role_actions',
                        'type' => 'LEFT',
                        'conditions' => 'RoleActions.action_id = Actions.id',
                    ],
                ],
                'conditions' => ['Actions.is_active' => 1, 'RoleActions.role_id' => $this->Auth->user('role_id')],
                'fields' => ['Actions.action', 'RoleActions.role_id', 'RoleActions.action_id', 'RoleActions.is_allowed'],
            ])->toList();
            if (isset($query) && !empty($query)) {
                foreach ($query as $key => $val) {
                    $roleAccess[$val->action]['isAllowed'] = $val->RoleActions['is_allowed'];
                }
                $GLOBALS['roleAccess'] = $roleAccess;
                $this->set('roleAccess', $roleAccess);
            }
        }
    }
    public function beforeRender(Event $event)
    {
        if (!array_key_exists('_serialize', $this->viewVars) &&
            in_array($this->response->type(), ['application/json', 'application/xml'])
        ) {
            $this->set('_serialize', true);
        }
    }
    public function isAuthorized($user = null)
    {
        return true;
    }
}
