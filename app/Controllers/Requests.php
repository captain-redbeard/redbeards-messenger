<?php
/**
 *
 * Details:
 * PHP Messenger.
 *
 * Modified: 10-Dec-2016
 * Made Date: 07-Dec-2016
 * Author: Hosvir
 *
 */
namespace Messenger\Controllers;

use Messenger\Core\Database;

class Requests extends Controller
{
    public function __construct()
    {
        $this->requiresLogin();
    }
    
    public function index()
    {
        $request = $this->model('Request');
        $requests = $request->getRequests();
        
        $this->view(
            'existing-requests',
            [
                'page' => 'existing-requests',
                'page_title' => 'Requests - ' . SITE_NAME,
                'requests' => $requests,
                'token' => $_SESSION['token'],
                'error' => ''
            ]
        );
    }
    
    public function add($parameters = null)
    {
        $url = '';
        
        if ($parameters !== null) {
            $error = $this->addRequest($parameters);
            if (!is_numeric($error)) {
                $url = $error;
                $error = '';
            }
        } else {
            $error = '';
        }
        
        $this->view(
            'add-contact',
            [
                'page' => 'add-contact',
                'page_title' => 'Add Contact - ' . SITE_NAME,
                'expire_times' => [1, 6, 12, 24, 48],
                'url' => $url != '' ? $url : '',
                'token' => $_SESSION['token'],
                'error' => $error != '' ? $this->getErrorMessage($error) : $error
            ]
        );
    }
    
    private function addRequest($parameters)
    {
        if ($this->checkToken()) {
            $request = $this->model('Request');
            
            return $request->add(
                $parameters['requestname'],
                $parameters['expire']
            );
        } else {
            return -1;
        }
    }
    
    public function delete($guid)
    {
        $request = $this->model('Request');
        $request->delete($guid);
        $this->redirect('requests');
    }
    
    private function getErrorMessage($code)
    {
        switch ($code) {
            case -1:
                return "Invalid token.";
            case 1:
                return "Expire must be a number.";
            case 2:
                return "Failed to add contact request, contact support.";
            default:
                return "Unknown error.";
        }
    }
}
