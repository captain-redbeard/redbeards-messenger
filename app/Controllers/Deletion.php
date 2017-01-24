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
                'page_title' => 'Deletion Policy - ' . SITE_NAME,
                'days' => [7,14,30,60,90],
                'expire' => $_SESSION[USESSION]->expire,
                'token' => $_SESSION['token'],
                'error' => $error != '' ? $this->getErrorMessage($error) : $error
            ]
        );
    }
        
    public function disable()
    {
        $_SESSION[USESSION]->updateExpire(0);
        $this->redirect('settings');
    }
    
    private function updateDeletion($parameters)
    {
        if ($this->checkToken()) {
            return $_SESSION[USESSION]->updateExpire($parameters['expire']);
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
                return "Expire must be a number.";
        }
    }
}
