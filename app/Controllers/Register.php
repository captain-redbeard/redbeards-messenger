<?php
/*
 *
 * Details:
 * PHP Messenger.
 *
 * Modified: 08-Dec-2016
 * Made Date: 05-Dec-2016
 * Author: Hosvir
 *
 */
namespace Messenger\Controllers;

use \DateTimeZone;
use Messenger\Core\Database;

class Register extends Controller
{
    public function index()
    {
        $this->startSession();
        
        $this->view(
            'register',
            [
                'page' => 'register',
                'page_title' => 'Register to ' . SITE_NAME,
                'timezones' => DateTimeZone::listIdentifiers(DateTimeZone::ALL),
                'timezone' => '',
                'token' => $_SESSION['token'],
                'error' => ''
            ]
        );
    }

    /*
     *
     * Register user.
     *
     */
    public function user()
    {
        $error = $this->registerUser();

        if ($error == 0) {
            $this->redirect('conversations');
        } else {
            $this->view(
                'register',
                [
                    'page' => 'register',
                    'page_title' => 'Register to ' . SITE_NAME,
                    'timezones' => DateTimeZone::listIdentifiers(DateTimeZone::ALL),
                    'timezone' => htmlspecialchars($_POST['timezone']),
                    'username' => htmlspecialchars($_POST['username']),
                    'token' => $_SESSION['token'],
                    'error' => $this->getErrorMessage($error)
                ]
            );
        }
    }
    
    private function registerUser()
    {
        if ($this->checkToken()) {
            $user = $this->model('User');

            return $user->register(
                $_POST['username'],
                $_POST['password'],
                $_POST['passphrase'],
                $_POST['timezone']
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
                return "Username is already taken.";
            case 5:
                return "Failed to create PPK.";
            case 6:
                return "Failed to create user, contact support.";
            case 7:
                return "You must select a Timezone.";
            case 8:
                return "Username must be at least 1 character.";
            default:
                return "Unknown error.";
        }
    }
}
