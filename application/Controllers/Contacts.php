<?php
/**
 * @author captain-redbeard
 * @since 05/12/16
 */
namespace Messenger\Controllers;

use Redbeard\Crew\Controller;

class Contacts extends Controller
{
    public function __construct()
    {
        //Check logged in
        $this->requiresLogin();
        
        //Check token
        $this->requiresToken('contacts');
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
                'token' => $_SESSION['token'],
                'error' => $error
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
                'error' => $error
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
}
