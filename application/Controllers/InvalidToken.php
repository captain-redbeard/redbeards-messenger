<?php
/**
 * @author captain-redbeard
 * @since 11/02/17
 */
namespace Messenger\Controllers;

use Redbeard\Crew\Controller;

class InvalidToken extends Controller
{
    public function __construct()
    {
        //Start session
        $this->startSession();
    }
    
    public function index($previousPage = null)
    {
        //View page
        $this->view(
            ['error/invalid-token'],
            [
                'page' => 'invalid-token',
                'page_title' => 'Invalid Token - ' . $this->config('site.name'),
                'page_description' => '',
                'page_keywords' => '',
                'previous_page' => $previousPage,
                'token' => $_SESSION['token']
            ],
            false
        );
    }
}
