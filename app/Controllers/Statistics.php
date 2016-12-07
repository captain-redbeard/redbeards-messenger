<?php
/*
 *
 * Details:
 * PHP Messenger.
 *
 * Modified: 05-Dec-2016
 * Made Date: 05-Dec-2016
 * Author: Hosvir
 *
 */
namespace Messenger\Controllers;

use Messenger\Core\Database;

class Statistics extends Controller
{
    public function index()
    {
        //Get statistics
        $statistics = Database::select(
            "SELECT
                (SELECT COUNT(user_id) FROM users) AS user_count,
                (SELECT COUNT(message_id) FROM messages) AS message_count,
                (SELECT COUNT(conversation_id) FROM conversations) AS conversation_count;",
            []
        );

        //Pass data to template
        $this->view(
            'statistics',
            [
                'page' => 'statistics',
                'page_title' => 'Statistics - ' . SITE_NAME,
                'error' => '',
                'user_count' => $statistics[0]['user_count'],
                'conversation_count' => $statistics[0]['conversation_count'],
                'message_count' => $statistics[0]['message_count']
            ]
        );
    }
}
