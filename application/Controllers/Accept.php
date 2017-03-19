<?php
/**
 * @author captain-redbeard
 * @since 07/12/16
 */
namespace Messenger\Controllers;

use Redbeard\Crew\Controller;

class Accept extends Controller
{
    public function __construct()
    {
        if ($this->isLoggedIn()) {
            $_SESSION['request'] = $guid;
        }
        
        //Check logged in
        $this->requiresLogin();
        
        //Check token
        $this->requiresToken('accept');
    }
    
    public function index($guid)
    {
        $error = $this->model('Request')->accept($guid);
        
        if ($error === 0) {
            $this->redirect('contacts');
        }
        
        $this->view(
            ['accept-request'],
            [
                'page' => 'accept-request',
                'page_title' => 'Accept Request - ' . $this->config('site.name'),
                'token' => $_SESSION['token'],
                'error' => $error
            ]
        );
    }
}
