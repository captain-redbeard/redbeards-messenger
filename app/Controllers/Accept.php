<?php
/**
 * @author captain-redbeard
 * @since 07/12/16
 */
namespace Redbeard\Controllers;

use Redbeard\Core\Database;

class Accept extends Controller
{
    public function __construct()
    {
        if ($this->isLoggedIn()) {
            $_SESSION['request'] = $guid;
        }
        
        $this->requiresLogin();
    }
    
    public function index($guid)
    {
        $request = $this->model('Request');
        $error = $request->accept($guid);
        
        if ($error === 0) {
            $this->redirect('contacts');
        }
        
        $this->view(
            ['accept-request'],
            [
                'page' => 'accept-request',
                'page_title' => 'Accept Request - ' . $this->config('site.name'),
                'error' => $this->getErrorMessage($error)
            ]
        );
    }
    
    private function getErrorMessage($code)
    {
        switch ($code) {
            case 1:
                return 'You can\'t add yourself???';
            case 2:
                return 'Request expired or invalid.';
            default:
                return '';
        }
    }
}
