<?php
/**
 * @author captain-redbeard
 * @since 05/12/16
 */
namespace Redbeard\Controllers;

use Redbeard\Core\Database;

class Contacts extends Controller
{
    public function __construct()
    {
        $this->requiresLogin();
    }
    
    public function index($error = '')
    {
        $contact = $this->model('Contact');
        
        $this->view(
            ['contacts'],
            [
                'page' => 'contacts',
                'page_title' => 'Contacts - ' . $this->config('site.name'),
                'contacts' => $contact->getContacts(),
                'error' => $this->getErrorMessage($error)
            ]
        );
    }
    
    public function edit($guid, $parameters = null)
    {
        $contact = $this->model('Contact');
        
        if ($parameters !== null && $parameters['alias'] !== '') {
            $error = $this->editContact($contact, $guid, $parameters);
            
            if ($error === 0) {
                $this->redirect('contacts');
            }
        } else {
            $error = '';
        }
        
        $this->view(
            ['edit-contact'],
            [
                'page' => 'edit-contact',
                'page_title' => 'Edit Contact - ' . $this->config('site.name'),
                'contact' => $contact->getByGuid($guid),
                'guid' => htmlspecialchars($guid),
                'token' => $_SESSION['token'],
                'error' => $this->getErrorMessage($error)
            ]
        );
    }

    public function delete($guid)
    {
        $contact = $this->model('Contact');
        
        if ($guid != null) {
            $error = $this->deleteContact($contact, $guid);
            
            if ($error === 0) {
                $this->redirect('contacts');
            }
        } else {
            $error = '';
        }
        
        $this->index($error);
    }
    
    private function editContact($contact, $guid, $parameters)
    {
        if ($this->checkToken()) {
            $selected_contact = $contact->getByGuid($guid);
            return $selected_contact->setAlias($parameters['alias']);
        } else {
            return -1;
        }
    }
    
    private function deleteContact($contact, $guid)
    {
        $selected_contact = $contact->getByGuid($guid);
        return $selected_contact->delete($guid);
    }
    
    private function getErrorMessage($code)
    {
        switch ($code) {
            case -1:
                return 'Invalid token.';
            case 1:
                return 'Alias must be less than 64 characters.';
            case 2:
                return 'Failed to save contact, contact support.';
            case 10:
                return 'Failed to delete messages, contact support.';
            case 11:
                return 'Failed to delete conversations, contact support.';
            case 12:
                return 'Failed to delete contacts, contact support.';
            default:
                return '';
        }
    }
}
