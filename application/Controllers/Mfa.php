<?php
/**
 * @author captain-redbeard
 * @since 06/12/16
 */
namespace Messenger\Controllers;

use Redbeard\Crew\Controller;

class Mfa extends Controller
{
    public function __construct()
    {
        //Check logged in
        $this->requiresLogin();
        
        //Check token
        $this->requiresToken('mfa');
    }
    
    public function enable($parameters = ['error' => ''])
    {
        $this->view(
            ['enable-mfa'],
            [
                'page' => 'enable-mfa',
                'page_title' => 'Enable MFA - ' . $this->config('site.name'),
                'qr_code' => $this->getUser()->getQrCode(),
                'secret_key' => $this->getUser()->secret_key,
                'token' => $_SESSION['token'],
                'error' => $parameters['error']
            ]
        );
    }
    
    public function disable()
    {
        $_SESSION[$this->config('app.user_session')]->disableMfa();
        $this->redirect('settings');
    }
    
    public function activate($parameters)
    {
        $error = $this->getUser()->enableMfa(
            $parameters['code1'],
            $parameters['code2']
        );
        
        if ($error === 0) {
            $this->redirect('settings');
        } else {
            $parameters['error'] = $error;
            $this->enable($parameters);
        }
    }
}
