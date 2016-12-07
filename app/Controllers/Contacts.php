<?php
/*
 *
 * Details:
 * PHP Messenger.
 *
 * Modified: 07-Dec-2016
 * Made Date: 05-Dec-2016
 * Author: Hosvir
 *
 */
namespace Messenger\Controllers;

use Messenger\Core\Database;

class Contacts extends Controller
{
    public function __construct()
    {
        $this->requiresLogin();
    }
    
    public function index()
    {
        $contact = $this->model('Contact');

        $this->view(
            'contacts',
            [
                'page' => 'contacts',
                'page_title' => 'Contacts - ' . SITE_NAME,
                'contacts' => $contact->getContacts(),
                'error' => ''
            ]
        );
    }

    public function edit($guid)
    {
        $contact = $this->model('Contact');

        if (isset($_POST['alias']) && $_POST['alias'] != '') {
            $selected_contact = $contact->getByGuid($guid);
            $error = $selected_contact->setAlias($_POST['alias']);

            if ($error == 0) {
                header('Location: ../contacts');
            }
        } else {
            $error = '';
        }
        
        $this->view(
            'edit-contact',
            [
                'page' => 'edit-contact',
                'page_title' => 'Edit Contact - ' . SITE_NAME,
                'contact' => $contact->getByGuid($guid),
                'error' => $error != '' ? $this->getErrorMessage($error) : $error
            ]
        );
    }

    public function delete($guid)
    {
        $contact = $this->model('Contact');
        $selected_contact = $contact->getContactByGuid($guid);
        $selected_contact->delete($guid);
        header('Location: ../contacts');
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
                return "Alias must be less than 64 characters.";
            case 2:
                return "Failed to save contact, contact support.";
            default:
                return "Unknown error.";
        }
    }
}
