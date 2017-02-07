<?php
/**
 * @author captain-redbeard
 * @since 09/12/16
 */
namespace Redbeard\Controllers;

class Deletion extends Controller
{
    public function __construct()
    {
        $this->requiresLogin();
    }
    
    public function enable($parameters = null)
    {
        if ($parameters !== null) {
            $error = $this->updateDeletion($parameters);
            
            if ($error === 0) {
                $this->redirect('settings');
            }
        } else {
            $error = '';
        }
        
        $this->view(
            ['deletion-policy'],
            [
                'page' => 'deletion-policy',
                'page_title' => 'Deletion Policy - ' . $this->config('site.name'),
                'days' => [7,14,30,60,90],
                'expire' => $_SESSION[$this->config('app.user_session')]->expire,
                'token' => $_SESSION['token'],
                'error' => $this->getErrorMessage($error)
            ]
        );
    }
        
    public function disable()
    {
        $_SESSION[$this->config('app.user_session')]->updateExpire(0);
        $this->redirect('settings');
    }
    
    private function updateDeletion($parameters)
    {
        if ($this->checkToken()) {
            return $_SESSION[$this->config('app.user_session')]->updateExpire($parameters['expire']);
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
                return 'Expire must be a number.';
            default:
                return '';
        }
    }
}
