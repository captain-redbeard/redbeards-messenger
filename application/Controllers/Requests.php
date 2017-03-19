<?php
/**
 * @author captain-redbeard
 * @since 07/12/16
 */
namespace Messenger\Controllers;

use Redbeard\Crew\Controller;
use Redbeard\Crew\Utils\Strings;

class Requests extends Controller
{
    public function __construct()
    {
        //Check logged in
        $this->requiresLogin();
        
        //Check token
        $this->requiresToken('requests');
    }
    
    public function index($error = '')
    {
        //Get requests
        $requests = $this->model('Request')->getRequests();
        
        $this->view(
            ['existing-requests'],
            [
                'page' => 'existing-requests',
                'page_title' => 'Requests - ' . $this->config('site.name'),
                'requests' => $requests,
                'token' => $_SESSION['token'],
                'error' => $error
            ]
        );
    }
    
    public function add($parameters = ['url' => '', 'error' => ''])
    {
        $this->view(
            ['add-contact'],
            [
                'page' => 'add-contact',
                'page_title' => 'Add Contact - ' . $this->config('site.name'),
                'expire_times' => [1, 6, 12, 24, 48],
                'url' => $parameters['url'],
                'token' => $_SESSION['token'],
                'error' => $parameters['error']
            ]
        );
    }
    
    public function addRequest($parameters = [])
    {
        $url = '';
        
        //Attempt add
        $error = $this->model('Request')->add(
            $parameters['requestname'],
            $parameters['expire']
        );
        
        if (Strings::contains('accept', $error)) {
            $parameters['url'] = $error;
            $parameters['error'] = '';
            $this->add($parameters);
        } else {
            $parameters['url'] = '';
            $parameters['error'] = $error;
            $this->add($parameters);
        }
    }
    
    public function delete($guid)
    {
        //Attempt delete
        $error = $this->model('Request')->delete($guid);
        
        if ($error === 0) {
            $this->redirect('requests');
        }
        
        $this->index($error);
    }
}
