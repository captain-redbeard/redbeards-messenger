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
    public function __construct()
    {
        $this->startSession();
    }
    
    public function index($timezone = '', $username = '', $error = '')
    {
        $this->view(
            ['register'],
            [
                'page' => 'register',
                'page_title' => 'Register to ' . $this->config('site.name'),
                'timezones' => DateTimeZone::listIdentifiers(DateTimeZone::ALL),
                'timezone' => htmlspecialchars($timezone),
                'username' => htmlspecialchars($username),
                'token' => $_SESSION['token'],
                'error' => $this->getErrorMessage($error)
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
                return 'Username must be at least 1 character.';
            case 2:
                return 'Username must be less than 64 characters.';
            case 3:
                return 'Password must be greater than 8 characters.';
            case 4:
                return 'Password must be less than 256 characters.';
            
            case 10:
                return 'You must select a Timezone.';
            case 11:
                return 'Username is already taken.';
            case 12:
                return 'Failed to create user, contact support.';
            case 13:
                return 'Failed to create PPK.';
            default:
                return '';
        }
    }
}
