<?php
/*
 *
 * Details:
 * PHP Messenger.
 *
 * Modified: 07-Dec-2016
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
                'error' => ''
            ]
        );
    }

    public function add()
    {
        if (isset($_POST['expire'])) {
            $request = $this->model('Request');
            $error = $request->add($_POST['requestname'], $_POST['expire']);
            if (!is_numeric($error)) {
                $url = $error;
                $error = '';
            }
        } else {
            $url = '';
            $error = '';
        }
        
        $this->view(
            'add-contact',
            [
                'page' => 'add-contact',
                'page_title' => 'Add Contact - ' . SITE_NAME,
                'expire_times' => [1, 6, 12, 24, 48],
                'url' => $url,
                'error' => $error != '' ? $this->getErrorMessage($error) : $error
            ]
        );
    }

    public function delete($guid)
    {
        $request = $this->model('Request');
        $request->delete($guid);
        header('Location: ../requests');
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
                return "Expire must be a number.";
            case 2:
                return "Failed to add contact request, contact support.";
            default:
                return "Unknown error.";
        }
    }
}
