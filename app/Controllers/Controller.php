<?php
/*
 *
 * Details:
 * PHP Messenger.
 *
 * Modified: 07-Dec-2016
 * Made Date: 04-Nov-2016
 * Author: Hosvir
 *
 */
namespace Messenger\Controllers;

use Messenger\Core\Functions;
use Messenger\Core\Session;

class Controller
{
    public function model($model)
    {
        $model = '\\Messenger\\Models\\' . $model;
        return new $model;
    }

    public function isLoggedIn()
    {
        if (!isset($_SESSION)) {
            Session::start();
        }
        
        return Session::loginCheck();
    }

    public function requiresLogin()
    {
        if (!$this->isLoggedIn()) {
            header('Location: ' . str_replace("index.php", "login", Functions::getUrl()));
        }
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
