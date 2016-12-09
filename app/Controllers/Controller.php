<?php
/**
 *
 * Details:
 * PHP Messenger.
 *
 * Modified: 08-Dec-2016
 * Made Date: 04-Nov-2016
 * Author: Hosvir
 *
 */
namespace Messenger\Controllers;

use Messenger\Core\Session;
use Messenger\Core\Functions;

class Controller
{
    public function model($model)
    {
        $model = '\\Messenger\\Models\\' . $model;
        return new $model;
    }
    
    public function startSession()
    {
        Session::start();
        
        if (!isset($_SESSION['token']) || (isset($_SESSION['token']) && (time() - $_SESSION['token_time'])) < 300) {
            $_SESSION['token'] = Functions::generateRandomString(32);
            $_SESSION['token_time'] = time();
        }
    }
    
    public function checkToken()
    {
        $this->startSession();
        if (isset($_POST['token']) && $_POST['token'] == $_SESSION['token']) {
            return true;
        } else {
            return false;
        }
    }
    
    public function isLoggedIn()
    {
        $this->startSession();
        return Session::loginCheck();
    }
    
    public function requiresLogin()
    {
        if (!$this->isLoggedIn()) {
            $this->redirect('login');
        }
    }
    
    public function redirect($page)
    {
        header('Location: ' . BASE_HREF . '/' . $page);
    }
    
    public function logout()
    {
        Session::kill();
    }
    
    public function view($view, $data = [])
    {
        require_once '../app/Views/template/header.php';
        require_once '../app/Views/' . $view . '.php';
        require_once '../app/Views/template/footer.php';
    }
}
