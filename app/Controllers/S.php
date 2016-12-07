<?php
/**
 * 
 * Details:
 * PHP Messenger.
 * 
 * Modified: 07-Dec-2016
 * Made Date: 06-Dec-2016
 * Author: Hosvir
 * 
 * */
namespace Messenger\Controllers;

use Messenger\Core\Database;

class S extends Controller
{   
    public function index()
    {        
        $to_guid = $_POST['g'];
        $conversation_guid = $_POST['c'];
        $message = $_POST['m'];
        
        if ($message != null && strlen(trim($message)) > 0) {
            if ($to_guid != null) {
                $mess = $this->model('Message');
                $mess->send(
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
