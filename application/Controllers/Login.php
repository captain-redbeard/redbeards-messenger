<?php
/**
 * @author captain-redbeard
 * @since 04/12/16
 */
namespace Messenger\Controllers;

use Redbeard\Crew\Controller;

class Login extends Controller
{
    public function __construct()
    {
        if ($this->isLoggedIn()) {
            $this->redirect('conversations');
        }
        
        //Check token
        $this->requiresToken('login');
    }
    
    public function index($parameters = ['username' => '', 'error' => ''])
    {
        $this->view(
            ['login'],
            [
                'page' => 'login',
                'page_title' => 'Login to ' . $this->config('site.name'),
                'username' => htmlspecialchars($parameters['username']),
                'token' => $_SESSION['token'],
                'error' => htmlspecialchars($parameters['error'])
            ]
        );
    }
    
    public function authenticate($parameters)
    {
        $error = $this->model('User')->login(
            $parameters['username'],
            $parameters['password'],
            $parameters['passphrase'],
            $parameters['mfa']
        );
        
        if ($error === 0) {
            $this->redirect('conversations');
        } else {
            $parameters['error'] = $error;
            $this->index($parameters);
        }
    }
}
