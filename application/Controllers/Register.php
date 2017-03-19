<?php
/**
 * @author captain-redbeard
 * @since 05/12/16
 */
namespace Messenger\Controllers;

use \DateTimeZone;
use Redbeard\Crew\Controller;

class Register extends Controller
{
    public function __construct()
    {
        $this->startSession();
        
        //Check token
        $this->requiresToken('register');
    }
    
    public function index($parameters = ['timezone' => '', 'username' => '', 'error' => ''])
    {
        $this->view(
            ['register'],
            [
                'page' => 'register',
                'page_title' => 'Register to ' . $this->config('site.name'),
                'timezones' => DateTimeZone::listIdentifiers(DateTimeZone::ALL),
                'timezone' => htmlspecialchars($parameters['timezone']),
                'username' => htmlspecialchars($parameters['username']),
                'token' => $_SESSION['token'],
                'error' => $parameters['error']
            ]
        );
    }
    
    public function user($parameters)
    {
        $error = $this->model('User')->register(
            $parameters['username'],
            $parameters['password'],
            $parameters['password'],
            $parameters['passphrase'],
            $parameters['timezone']
        );
        
        if ($error === 0) {
            $this->redirect('conversations');
        } else {
            $parameters['error'] = $error;
            $this->index($parameters);
        }
    }
}
