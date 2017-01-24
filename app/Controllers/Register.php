<?php
/**
 * @author captain-redbeard
 * @since 05/12/16
 */
namespace Redbeard\Controllers;

use \DateTimeZone;
use Redbeard\Core\Database;

class Register extends Controller
{
    public function index($timezone = '', $username = '', $error = '')
    {
        $this->startSession();
        
        $this->view(
            ['register'],
            [
                'page' => 'register',
                'page_title' => 'Register to ' . SITE_NAME,
                'timezones' => DateTimeZone::listIdentifiers(DateTimeZone::ALL),
                'timezone' => htmlspecialchars($timezone),
                'username' => htmlspecialchars($username),
                'token' => $_SESSION['token'],
                'error' => $error !== '' ? $this->getErrorMessage($error) : $error
            ]
        );
    }
    
    public function user($parameters)
    {
        $error = $this->registerUser($parameters);
        
        if ($error === 0) {
            $this->redirect('conversations');
        } else {
            $this->index($_POST['timezone'], $_POST['username'], $error);
        }
    }
    
    private function registerUser($parameters)
    {
        if ($this->checkToken()) {
            $user = $this->model('User');
            
            return $user->register(
                $parameters['username'],
                $parameters['password'],
                $parameters['passphrase'],
                $parameters['timezone']
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
