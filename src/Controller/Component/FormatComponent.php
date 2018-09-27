<?php
namespace App\Controller\Component;

use Cake\Controller\Component;
use Cake\Mailer\Email;
use Cake\ORM\TableRegistry;
use Cake\Routing\Router;
use Cake\Auth\DefaultPasswordHasher;

/**
 * Fomrmat component
 */
class FormatComponent extends Component
{

    /**
     * Default configuration.
     *
     * @var array
     */
    protected $_defaultConfig = [];

    public function sendConfirmEmail($token, $user)
    {
        $url = Router::Url(['controller' => 'users', 'action' => 'confirmEmail'], true) . '/' . $token;
        $email = new Email();
        $email->transport('gmail');
        $email->template('confirm_registration');
        $email->emailFormat('html');
        $email->from('patelmohsin6562@gmail.com');
        $email->to($user->email, $user->first_name);
        $email->subject('Verify Account');
        $email->viewVars(['url' => $url, 'name' => $user->first_name . ' ' . $user->last_name]);
        if ($email->send()) {
            return 1;
        } else {
            return 0;
        }
    }
    public function generateUniqNumber()
    {

        $uniq_id = md5(rand());
        return $uniq_id;
    }
    public function getProfileId($uniq_id = null)
    {
        $this->Profiles = TableRegistry::get('Profiles');
        $profile = $this->Profiles->findByUniq_id($uniq_id)->select('id')->first();
        if (!$profile['id'] || $profile['id'] < 1) {
            return null;
        } else {
            return $profile['id'];
        }
    }
    public function checkPassword($reqpassword,$password)
    {
        if (strlen($password) > 0) {
            $hashPswdObj = new DefaultPasswordHasher;
            $hashpswd = $hashPswdObj->check($reqpassword,$password);
            return $hashpswd;
        }
    }
}
