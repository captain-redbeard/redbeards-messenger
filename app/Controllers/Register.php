<?php
/**
 * 
 * Details:
 * PHP Messenger.
 * 
 * Modified: 05-Dec-2016
 * Made Date: 05-Dec-2016
 * Author: Hosvir
 * 
 * */
namespace Messenger\Controllers;

use \DateTimeZone;
use Messenger\Core\Database;

class Register extends Controller
{
    public function index()
    {
        $this->view(
            'register',
            [
                'page' => 'register',
                'page_title' => 'Register to ' . SITE_NAME,
                'timezones' => DateTimeZone::listIdentifiers(DateTimeZone::ALL),
                'timezone' => '',
                'error' => ''
            ]
        );
    }

    /**
     *
     * Register user.
     * 
     * */
    public function user()
    {
        $user = $this->model('User');
        $error = $user->register(
            $_POST['username'],
            $_POST['password'],
            $_POST['passphrase'],
            $_POST['timezone']
        );

        if ($error == 0) {
            //Success
            header('Location: ../conversations');
        } else {
            $this->view(
                'register',
                [
                    'page' => 'register',
                    'page_title' => 'Register to ' . SITE_NAME,
                    'timezones' => DateTimeZone::listIdentifiers(DateTimeZone::ALL),
                    'timezone' => $_POST['timezone'],
                    'username' => $_POST['username'],
                    'error' => $this->getErrorMessage($error)
                ]
            );
        }
    }

    /**
     *
     * Get the error message.
     * 
     * */
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
