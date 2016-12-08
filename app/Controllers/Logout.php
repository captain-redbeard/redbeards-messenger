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

class Logout extends Controller
{
    public function index()
    {
        $this->logout();
        $this->redirect('login');
    }
}
