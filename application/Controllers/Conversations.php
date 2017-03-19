<?php
/**
 * @author captain-redbeard
 * @since 05/12/16
 */
namespace Messenger\Controllers;

use Redbeard\Crew\Controller;
use Redbeard\Crew\Utils\Dates;

class Conversations extends Controller
{
    public function __construct()
    {
        //Check logged in
        $this->requiresLogin();
        
        //Check token
        $this->requiresToken('conversations');
        
        //Check for request
        if (isset($_SESSION['request'])) {
            $this->redirect('accept/' . $_SESSION['request']);
        }
    }
    
    public function index($menu = null, $guid = null, $cguid = null)
    {
        $conversation = $this->model('Conversation');
        $message = $this->model('Message');
        $_SESSION[$this->config('app.user_session')]->updateLastLoad();
        
        if ($menu !== null && $menu === 'new') {
            $newconversation = $conversation->getNew($guid);
        } else {
            $newconversation = null;
        }
        
        $this->view(
            ['conversations'],
            [
                'page' => 'conversations',
                'page_title' => $this->config('site.name'),
                'conversations' => $conversation->getConversations(),
                'messages' => $message->getMessages($cguid),
                'newconversation' => $newconversation,
                'currenttime' => Dates::convertDateTime(date('Y-m-d H:i:s'), true),
                'guid' => htmlspecialchars($guid),
                'cguid' => htmlspecialchars($cguid),
                'menu' => htmlspecialchars($menu),
                'user' => $_SESSION[$this->config('app.user_session')],
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
        
        if ($guid != null) {
            $error = $conversation->delete($guid);
        }
        
        $this->redirect('conversations');
    }
}
