<?php
/**
 * @author captain-redbeard
 * @since 04/12/16
 */
namespace Redbeard\Controllers;

use Redbeard\Core\Functions;

class Login extends Controller
{
    //TEST FOR VUNRABLE
    public function index($username = '', $error = '')
    {
        $this->startSession();
        
        if (isset($_SESSION[USESSION])) {
            $this->redirect('conversations');
        }
        
        $this->view(
            ['login'],
            [
                'page' => 'login',
                'page_title' => 'Login - ' . SITE_NAME,
                'username' => htmlspecialchars($username),
                'token' => $_SESSION['token'],
                'error' => $error !== '' ? $this->getErrorMessage($error) : $error
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
                return "Invalid token.";
            case 1:
                return "No password entered.";
            case 2:
                return "Password must be greater than 8 characters.";
            case 3:
                return "Username must be less than 64 characters.";
            case 4:
                return "Username not found.";
            case 5:
                return "To many login attempts, try again later.";
            case 6:
                return "Incorrect password.";
            case 7:
                return "MFA Failed.";
            default:
                return "Unknown error.";
        }
    }
}
