<?php
/**
 * 
 * Details:
 * PHP Messenger.
 * 
 * Modified: 03-Dec-2016
 * Made Date: 27-Nov-2016
 * Author: Hosvir
 * 
 * */
include(dirname(__FILE__) . "/../includes/loginauth.php");

//Get existing requests
$requests = QueryBuilder::select(
    "SELECT request_guid, request_name, expire, DATE_ADD(made_date, INTERVAL expire HOUR) as expiretime FROM contact_requests WHERE user_guid = ?;",
    array($_SESSION[USESSION]->user_guid)
);
?>

            <div class="cover-wrapper">
                <form method="POST" action="">
                    <table class="multi-table">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>URL</th>
                                <th>Expires</th>
                                <th>Action</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php foreach($requests as $request) { ?>

                            <tr>
                                <td><?php echo $request['request_name']; ?></td>
                                <td><?php echo str_replace("index.php", "accept-request", getURL()) . "/" . $request['request_guid']; ?></td>
                                <td class="text-right">
                                    <?php 
                                    $expirein = nicetime($request['expiretime']);
                                    echo contains("ago", $expirein) ? "Expired" : $expirein;
                                    ?>
                                </td>
                                <td><a class="raw-button red-button" href="delete-request/<?php echo $request['request_guid']; ?>">Delete</a></td>
                            </tr>
                            <?php }?>
                        </tbody>

                        <tfoot>
                            <tr><td></td></tr>
                            <tr><td></td></tr>

                            <tr>
                                <td>
                                    <a href="conversations">Return</a>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </form>
            </div>
