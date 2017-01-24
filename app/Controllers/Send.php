<?php
/**
 * @author captain-redbeard
 * @since 06/12/16
 */
namespace Redbeard\Controllers;

use Redbeard\Core\Database;

class Send extends Controller
{
    public function index()
    {
        $to_guid = $_POST['g'];
        $conversation_guid = $_POST['c'];
        $message = $_POST['m'];
        
        if ($message !== null && strlen(trim($message)) > 0) {
            if ($to_guid !== null) {
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
