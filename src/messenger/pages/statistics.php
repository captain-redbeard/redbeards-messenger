<?php
/**
 * 
 * Details:
 * PHP Messenger.
 * 
 * Modified: 03-Dec-2016
 * Made Date: 02-Nov-2016
 * Author: Hosvir
 * 
 * */

$statistics = QueryBuilder::select(
    "SELECT
        (SELECT COUNT(user_id) FROM users) AS user_count,
        (SELECT COUNT(message_id) FROM messages) AS message_count,
        (SELECT COUNT(conversation_id) FROM conversations) AS conversation_count;",
    array()
);
?>

            <div class="cover-wrapper">
                <form>
                    <table class="multi-table">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Value</th>
                            </tr>
                        </thead>

                        <tbody class="text-center">
                            <tr>
                                <td>Users</td>
                                <td><strong><?php echo $statistics[0]['user_count']; ?></strong></td>
                            </tr>

                            <tr>
                                <td>Conversations</td>
                                <td><strong><?php echo number_format($statistics[0]['conversation_count'] > 1 ? ($statistics[0]['conversation_count'] / 2) : $statistics[0]['conversation_count']); ?></strong></td>
                            </tr>

                            <tr>
                                <td>Messages</td>
                                <td><strong><?php echo number_format($statistics[0]['message_count'] > 1 ? ($statistics[0]['message_count'] / 2) : $statistics[0]['message_count']); ?></strong></td>
                            </tr>
                        </tbody>

                        <tfoot>
                        </tfoot>
                    </table>
                </form>
            </div>
