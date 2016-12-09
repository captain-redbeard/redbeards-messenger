<?php
/**
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
                'token' => $_SESSION['token'],
                'error' => ''
            ]
        );
    }
    
    public function update()
    {
        $error = $this->updateSettings();
        
        if ($error == 0) {
            $this->redirect('settings');
        } else {
            $this->view(
                'settings',
                [
                    'page' => 'settings',
                    'page_title' => 'Settings - ' . SITE_NAME,
                    'user' => $_SESSION[USESSION],
                    'timezones' => DateTimeZone::listIdentifiers(DateTimeZone::ALL),
                    'token' => $_SESSION['token'],
                    'error' => $this->getErrorMessage($error)
                ]
            );
        }
    }
    
    private function updateSettings()
    {
        if ($this->checkToken()) {
            return $_SESSION[USESSION]->update(
                $_POST['username'],
                $_POST['timezone']
            );
        } else {
            return -1;
        }
    }
    
    public function reset()
    {
        if (isset($_POST['password'])) {
            $error = $this->resetPassword();
            
            if ($error == 0) {
                $this->redirect('settings');
            }
        } else {
            $error = '';
        }
        
        $this->view(
            'change-password',
            [
                'page' => 'change-password',
                'page_title' => 'Change Password - ' . SITE_NAME,
                'token' => $_SESSION['token'],
                'error' => $error != '' ? $this->getErrorMessage($error) : $error
            ]
        );
    }
    
    private function resetPassword()
    {
        if ($this->checkToken()) {
            return $_SESSION[USESSION]->resetPassword(
                $_POST['password'],
                $_POST['npassword'],
                $_POST['cpassword']
            );
        } else {
            return -1;
        }
    }
    
    public function delete()
    {
        if (isset($_POST['password'])) {
            $error = $this->deleteAccount();
            
            if ($error == 0) {
                $this->redirect('logout');
            }
        } else {
            $error = '';
        }
        
        $this->view(
            'delete-account',
            [
                'page' => 'delete-account',
                'page_title' => 'Delete Account - ' . SITE_NAME,
                'token' => $_SESSION['token'],
                'error' => $error != '' ? $this->getErrorMessage($error) : $error
            ]
        );
    }
    
    private function deleteAccount()
    {
        if ($this->checkToken()) {
            return $_SESSION[USESSION]->delete(
                $_POST['password']
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
