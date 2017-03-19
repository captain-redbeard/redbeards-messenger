<?php
/**
 * @author captain-redbeard
 * @since 06/12/16
 */
namespace Messenger\Controllers;

use Redbeard\Crew\Controller;

class Send extends Controller
{
    public function __construct()
    {
        //Check logged in
        $this->requiresLogin();
    }
    
    public function index($parameters = [])
    {
        $to_guid = $parameters['g'];
        $conversation_guid = $parameters['c'];
        $message = $parameters['m'];
        
        if ($message !== null && strlen(trim($message)) > 0) {
            if ($to_guid !== null) {
                $this->model('Message')->send(
                    $to_guid,
                    $conversation_guid,
                    $message
                );
                
                echo 1;
            } else {
                echo 0;
            }
        }
    }
}
