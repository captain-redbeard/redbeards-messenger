<?php
/**
 * @author captain-redbeard
 * @since 20/01/17
 */
namespace Redbeard\Controllers;

use Redbeard\Core\Session;
use Redbeard\Core\Functions;

class Controller
{
    public function model($model)
    {
        $model = APP_PATH . 'Models\\' . $model;
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
        
        if (isset($_POST['token']) && $_POST['token'] === $_SESSION['token']) {
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
    
    public function view($view = [], $data = [], $raw = false)
    {
        if (!$raw) {
            require_once '../app/Views/template/header.php';
        }
        
        foreach ($view as $v) {
            require_once '../app/Views/' . $v . '.php';
        }
        
        if (!$raw) {
            require_once '../app/Views/template/footer.php';
        }
    }
}
