<?php
/*
 *
 * Details:
 * PHP Messenger.
 *
 * Modified: 05-Dec-2016
 * Made Date: 04-Dec-2016
 * Author: Hosvir
 *
 */
namespace Messenger\Controllers;

use Messenger\Core\Database;

class Login extends Controller
{
    public function index()
    {
        $this->view(
            'login',
            [
                'page' => 'login',
                'page_title' => 'Login - ' . SITE_NAME,
                'username' => '',
                'error' => ''
            ]
        );
    }
    
    public function authenticate()
    {
        $user = $this->model('User');
        $error = $user->login(
            $_POST['username'],
            $_POST['password'],
            $_POST['passphrase'],
            $_POST['mfa']
        );

        if ($error == 0) {
            //Success
            header('Location: ../conversations');
        } else {
            $this->view(
                'login',
                [
                    'page' => 'login',
                    'page_title' => 'Login to ' . SITE_NAME,
                    'username' => htmlspecialchars($_POST['username']),
                    'error' => $this->getErrorMessage($error)
                ]
            );
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
