<?php
/*
 *
 * Details:
 * PHP Messenger.
 *
 * Modified: 08-Dec-2016
 * Made Date: 04-Dec-2016
 * Author: Hosvir
 *
 */
namespace Messenger\Controllers;

use Messenger\Core\Functions;

class Login extends Controller
{
    public function index()
    {
        $this->startSession();
        
        if (isset($_SESSION[USESSION])) {
            $this->redirect('conversations');
        }

        $this->view(
            'login',
            [
                'page' => 'login',
                'page_title' => 'Login - ' . SITE_NAME,
                'username' => '',
                'token' => $_SESSION['token'],
                'error' => ''
            ]
        );
    }
    
    public function authenticate()
    {
        $error = $this->authenticateUser();
        
        if ($error == 0) {
            $this->redirect('conversations');
        } else {
            $this->view(
                'login',
                [
                    'page' => 'login',
                    'page_title' => 'Login to ' . SITE_NAME,
                    'username' => htmlspecialchars($_POST['username']),
                    'token' => $_SESSION['token'],
                    'error' => $this->getErrorMessage($error)
                ]
            );
        }
    }
    
    private function authenticateUser()
    {
        if ($this->checkToken()) {
            $user = $this->model('User');

            return $user->login(
                $_POST['username'],
                $_POST['password'],
                $_POST['passphrase'],
                $_POST['mfa']
            );
        } else {
            return -1;
        }
    }

    /*
     *
     * Get the error message.
     *
     */
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
