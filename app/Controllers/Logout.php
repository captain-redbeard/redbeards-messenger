<?php
/**
 * @author captain-redbeard
 * @since 05/12/16
 */
namespace Redbeard\Controllers;

class Logout extends Controller
{
    public function index()
    {
        $this->logout();
        $this->redirect('login');
    }
}
