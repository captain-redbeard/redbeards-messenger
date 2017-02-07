<?php
/**
 * @author captain-redbeard
 * @since 05/12/16
 */
namespace Redbeard\Controllers;

use \DateTimeZone;

class Settings extends Controller
{
    public function __construct()
    {
        $this->requiresLogin();
    }
    
    public function index($error = '')
    {
        $this->view(
            ['settings'],
            [
                'page' => 'settings',
                'page_title' => 'Settings - ' . $this->config('site.name'),
                'user' => $_SESSION[$this->config('app.user_session')],
                'timezones' => DateTimeZone::listIdentifiers(DateTimeZone::ALL),
                'token' => $_SESSION['token'],
                'error' => $this->getErrorMessage($error)
            ]
        );
    }
    
    public function update($parameters = null)
    {
        if ($parameters !== null) {
            $error = $this->updateSettings($parameters);
            
            if ($error === 0) {
                $this->redirect('settings');
            }
        } else {
            $error = '';
        }
        
        $this->index($error);
    }
    
    private function updateSettings($parameters)
    {
        if ($this->checkToken()) {
            return $_SESSION[$this->config('app.user_session')]->update(
                $parameters['username'],
                $parameters['timezone']
            );
        } else {
            return -1;
        }
    }
    
    public function reset($parameters = null)
    {
        if ($parameters !== null) {
            $error = $this->resetPassword($parameters);
            
            if ($error === 0) {
                $this->redirect('settings');
            }
        } else {
            $error = '';
        }
        
        $this->view(
            ['change-password'],
            [
                'page' => 'change-password',
                'page_title' => 'Change Password - ' . $this->config('site.name'),
                'token' => $_SESSION['token'],
                'error' => $this->getErrorMessage($error)
            ]
        );
    }
    
    private function resetPassword($parameters)
    {
        if ($this->checkToken()) {
            return $_SESSION[$this->config('app.user_session')]->resetPassword(
                $parameters['password'],
                $parameters['npassword'],
                $parameters['cpassword']
            );
        } else {
            return -1;
        }
    }
    
    public function newkeypair($parameters = null)
    {
        if ($parameters !== null) {
            $error = $this->generateNewKeypair($parameters);
            
            if ($error === 0) {
                $this->redirect('settings');
            }
        } else {
            $error = '';
        }
        
        $this->view(
            ['new-keypair'],
            [
                'page' => 'new-keypair',
                'page_title' => 'New Keypair - ' . $this->config('site.name'),
                'token' => $_SESSION['token'],
                'error' => $this->getErrorMessage($error)
            ]
        );
    }
    
    private function generateNewKeypair($parameters)
    {
        if ($this->checkToken()) {
            return $_SESSION[$this->config('app.user_session')]->generateNewKeypair(
                $parameters['password'],
                $parameters['passphrase']
            );
        } else {
            return -1;
        }
    }
    
    public function delete($parameters = null)
    {
        if ($parameters !== null) {
            $error = $this->deleteAccount($parameters);
            
            if ($error === 0) {
                $this->redirect('logout');
            }
        } else {
            $error = '';
        }
        
        $this->view(
            ['delete-account'],
            [
                'page' => 'delete-account',
                'page_title' => 'Delete Account - ' . $this->config('site.name'),
                'token' => $_SESSION['token'],
                'error' => $this->getErrorMessage($error)
            ]
        );
    }
    
    private function deleteAccount($parameters)
    {
        if ($this->checkToken()) {
            return $_SESSION[$this->config('app.user_session')]->delete(
                $parameters['password']
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
            
            case 6:
                return 'Failed to create PPK.';
            
            case 10:
                return 'Failed to save settings.';
            case 11:
                return 'Username already taken.';
            case 12:
                return 'Passwords don\'t match.';
            case 13:
                return 'Incorrect password.';
            
            case 16:
                return 'Failed to find user, contact support.';
            case 17:
                return 'Failed to update user, contact support.';
            case 20:
                return 'Failed to delete messages, contact support.';
            case 21:
                return 'Failed to delete conversations, contact support.';
            case 22:
                return 'Failed to delete contacts, contact support.';
            case 23:
                return 'Failed to delete account, contact support.';
            default:
                return '';
        }
    }
}
