<?php
/**
 * @author captain-redbeard
 * @since 05/12/16
 */
namespace Messenger\Controllers;

use Redbeard\Crew\Controller;

class Logout extends Controller
{
    public function index()
    {
        $this->logout();
        $this->redirect('login');
    }
}
