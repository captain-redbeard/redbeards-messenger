<?php
/*
 *
 * Details:
 * PHP Messenger.
 *
 * Modified: 07-Dec-2016
 * Made Date: 05-Dec-2016
 * Author: Hosvir
 *
 */
namespace Messenger\Controllers;

use \DateTimeZone;
use Messenger\Core\Database;

class Settings extends Controller
{
    public function __construct()
    {
        $this->requiresLogin();
    }
    
    public function index()
    {
        $this->view(
            'settings',
            [
                'page' => 'settings',
                'page_title' => 'Settings - ' . SITE_NAME,
                'user' => $_SESSION[USESSION],
                'timezones' => DateTimeZone::listIdentifiers(DateTimeZone::ALL),
                'error' => ''
            ]
        );
    }

    public function update()
    {
        $error = $_SESSION[USESSION]->update(
            $_POST['username'],
            $_POST['timezone']
        );

        if ($error == 0) {
            //Success
            header('Location: ../settings');
        } else {
            $this->view(
                'settings',
                [
                    'page' => 'settings',
                    'page_title' => 'Settings - ' . SITE_NAME,
                    'user' => $_SESSION[USESSION],
                    'timezones' => DateTimeZone::listIdentifiers(DateTimeZone::ALL),
                    'error' => $this->getErrorMessage($error)
                ]
            );
        }
    }

    public function reset()
    {
        if (isset($_POST['password'])) {
            $error = $_SESSION[USESSION]->resetPassword(
                $_POST['password'],
                $_POST['npassword'],
                $_POST['cpassword']
            );
            
            if ($error == 0) {
                //Success
                header('Location: ../settings');
            }
        } else {
            $error = '';
        }
        
        $this->view(
            'change-password',
            [
                'page' => 'change-password',
                'page_title' => 'Change Password - ' . SITE_NAME,
                'error' => $error != '' ? $this->getErrorMessage($error) : $error
            ]
        );
    }

    public function delete()
    {
        if (isset($_POST['password'])) {
            $error = $_SESSION[USESSION]->delete(
                $_POST['password']
            );
            
            if ($error == 0) {
                //Success
                header('Location: ../logout');
            }
        } else {
            $error = '';
        }
        
        $this->view(
            'delete-account',
            [
                'page' => 'delete-account',
                'page_title' => 'Delete Account - ' . SITE_NAME,
                'error' => ''
            ]
        );
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
                return "Username must be less than 64 characters.";
            case 2:
                return "Username is already taken.";
            case 3:
                return "Failed to save settings.";
            case 4:
                return "Username must be at least 1 character.";
            case 10:
                return "Passwords don't match.";
            case 11:
                return "Password must be greater than 8 characters.";
            case 12:
                return "Incorrect password.";
            case 13:
                return "Failed to find user, contact support.";
            case 14:
                return "Failed to update user, contact support.";
            case 20:
                return "Failed to delete messages, contact support.";
            case 21:
                return "Failed to delete conversations, contact support.";
            case 22:
                return "Failed to delete contacts, contact support.";
            case 23:
                return "Failed to delete account, contact support.";
            default:
                return "Unknown error.";
        }
    }
}
