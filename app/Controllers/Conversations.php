<?php
/*
 *
 * Details:
 * PHP Messenger.
 *
 * Modified: 08-Dec-2016
 * Made Date: 05-Dec-2016
 * Author: Hosvir
 *
 */
namespace Messenger\Controllers;

use Messenger\Core\Functions;
use Messenger\Core\Database;

class Conversations extends Controller
{
    public function __construct()
    {
        $this->requiresLogin();
    }
    
    public function index($menu = null, $guid = null, $cguid = null)
    {
        if (isset($_SESSION['request'])) {
            $this->redirect('accept/' . $_SESSION['request']);
        }
        
        $conversation = $this->model('Conversation');
        $message = $this->model('Message');
        $_SESSION[USESSION]->updateLastLoad();

        if ($menu != null && $menu == 'new') {
            $newconversation = $conversation->getNew($guid);
        } else {
            $newconversation = null;
        }
        
        $this->view(
            'conversations',
            [
                'page' => 'conversations',
                'page_title' => SITE_NAME,
                'conversations' => $conversation->getConversations(),
                'messages' => $message->getMessages($cguid),
                'newconversation' => $newconversation,
                'currenttime' => Functions::convertTime(date('Y-m-d H:i:s'), true),
                'guid' => htmlspecialchars($guid),
                'cguid' => htmlspecialchars($cguid),
                'menu' => htmlspecialchars($menu),
                'token' => $_SESSION['token']
            ]
        );
    }

    public function send()
    {
        $message = $this->model('Message');
        $result = explode(":", $message->send($_POST['tg'], null, $_POST['m']));

        switch ($result[0]) {
            case 0:
                $this->redirect('conversations/display/' . $_POST['tg'] . '/' . $result[1] . '#l');
                break;
            case 1:
                $this->redirect('conversations');
                break;
        }
    }

    public function delete($guid = null)
    {
        $conversation = $this->model('Conversation');
        $conversation->delete($guid);
        $this->redirect('conversations');
    }
}
