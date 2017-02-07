<?php
/**
 * @author captain-redbeard
 * @since 04/12/16
 */
namespace Redbeard\Controllers;

use Redbeard\Core\Functions;

class Login extends Controller
{
    public function __construct()
    {
        if ($this->isLoggedIn()) {
            $this->redirect('conversations');
        }
    }
    
    public function index($username = '', $error = '')
    {
        $this->view(
            ['login'],
            [
                'page' => 'login',
                'page_title' => 'Login to ' . $this->config('site.name'),
                'username' => htmlspecialchars($username),
                'token' => $_SESSION['token'],
                'error' => $this->getErrorMessage($error)
            ]
        );
    }
    
    public function authenticate($parameters)
    {
        $error = $this->authenticateUser($parameters);
        
        if ($error === 0) {
            $this->redirect('conversations');
        } else {
            $this->index($_POST['username'], $error);
        }
    }
    
    private function authenticateUser($parameters)
    {
        if ($this->checkToken()) {
            $user = $this->model('User');
            
            return $user->login(
                $parameters['username'],
                $parameters['password'],
                $parameters['passphrase'],
                $parameters['mfa']
            );
        } else {
            return -1;
        }
    }
    
    private function getErrorMessage($code)
    {
        switch ($code) {
            case -1:
                return 'Invalid token.';
            case 1:
                return 'Username must be at least 1 character.';
            case 2:
                return 'Username must be less than 64 characters.';
            case 3:
                return 'Password must be greater than 8 characters.';
            case 4:
                return 'Password must be less than 256 characters.';
            
            case 10:
                return 'Incorrect password.';
            case 11:
                return 'MFA Failed.';
            case 12:
                return 'To many login attempts, try again later.';
            case 13:
                return 'Username not found.';
            default:
                return '';
        }
    }
}
