<?php
/**
 *
 * Details:
 * PHP Messenger.
 *
 * Modified: 08-Dec-2016
 * Made Date: 07-Dec-2016
 * Author: Hosvir
 *
 */
namespace Messenger\Controllers;

use Messenger\Core\Database;

class Accept extends Controller
{
    public function index($guid)
    {
        $this->isLoggedIn();
        $_SESSION['request'] = $guid;
        $this->requiresLogin();
        
        $request = $this->model('Request');
        $error = $request->accept($guid);
        
        if ($error == 0) {
            $this->redirect('contacts');
        }
        
        $this->view(
            'accept-requests',
            [
                'page' => 'accept-requests',
                'page_title' => 'Accept Request - ' . SITE_NAME,
                'error' => $this->getErrorMessage($error)
            ]
        );
    }
    
    private function getErrorMessage($code)
    {
        switch ($code) {
            case 1:
                return "You can't add yourself???";
            case 2:
                return "Request expired or invalid.";
            default:
                return "Unknown error.";
        }
    }
}
