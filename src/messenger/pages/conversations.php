<?php
/**
 * 
 * Details:
 * PHP Messenger.
 * 
 * Modified: 03-Dec-2016
 * Made Date: 25-Nov-2016
 * Author: Hosvir
 * 
 * */
include(dirname(__FILE__) . "/../includes/loginauth.php");

//Check for request
if (isset($_SESSION['request'])) {
    header('Location: accept-request/' . $_SESSION['request']);
}

//Update last load
QueryBuilder::update(
    "UPDATE users SET last_load = NOW() WHERE user_guid = ?;",
    array($_SESSION[USESSION]->user_guid)
);


//Conversation class
include(dirname(__FILE__) . "/../includes/conversation.php");

//Check for post
if (isset($_POST['m']) && strlen(trim($_POST['m'])) > 0) {
    $result = explode(":", Conversation::sendMessage($guid, $cguid, $_POST['m']));

    switch ($result[0]) {
        case 0: //Success
            //Redirect
            header('Location: ../../conversations/' . $guid . '/' . $result[1] . '#l');
            break;
        case 1:
            $error = "No conversation selected.";
            break;
    }
}

//Check for new conversation
if (isset($menu) && $menu == "new") {
    $newconversation = QueryBuilder::select(
        "SELECT contact_guid, made_date,
            (SELECT username FROM users WHERE user_guid = contact_guid) AS username
            FROM contacts
            WHERE contact_guid = ?
            AND user_guid = ?;",
        array(
            $guid,
            $_SESSION[USESSION]->user_guid
        )
    );
} elseif (isset($cguid)) {
    $messages = Conversation::getMessages($cguid);
}

//Get conversations
$conversations = Conversation::getConversations();
?>

            <div class="a">
                <div class="b">
                    <div class="c">
                        <a href="settings">
                            <div class="g is" alt="Settings" title="Settings"></div>
                        </a>

                        <div class="fr">
                        <a href="add-contact">
                            <div class="g ia" alt="Add Contact" title="Add Contact"></div>
                        </a>
                    </div>
                </div>

                <div class="d">
                    <a class="v gb y" href="contacts">Start Conversation</a>

                    <a class="x" href="contacts">
                        <div class="g ic" alt="Contacts" title="Contacts"></div>
                    </a>
                </div>

                <div class="e" id="h">
                    <?php if(isset($newconversation) && count($newconversation) > 0) { ?>
                    <div class="f k">
                        <div class="i"><?php echo $newconversation[0]['username']; ?></div>
                        <div class="j fr"><?php echo convertTime(date('Y-m-d H:i:s'), true); ?></div>
                        <div class="z"></div>
                    </div>
                    <?php } ?>

                    <?php foreach($conversations as $conversation) { ?>
                    <div class="f <?php if(isset($cguid) && $conversation['conversation_guid'] == $cguid) echo "k"; ?>">
                        <a href="conversations/<?php echo $conversation['contact_guid'] . "/" . $conversation['conversation_guid']; ?>#l">
                            <div class="i"><?php if($conversation['contact_alias'] != "") echo $conversation['contact_alias'] . " - "; echo $conversation['username']; ?></div>
                        </a>
                        <div class="cc j fr" data-md="<?php echo $conversation['made_date']; ?>">
                            <?php echo convertTime($conversation['made_date'], true); ?>
                            &nbsp;
                    
                            <a href="delete-conversation/<?php echo $conversation['conversation_guid']; ?>">
                                <div class="g id" alt="Delete Conversation" title="Delete Conversation" style="width: 10px; height: 10px;"></div>
                            </a>
                        </div>

                        <div class="z"></div>
                    </div>
                    
                    <?php } ?>
                </div>
                </div>

                <div class="l">
                    <div class="m">
                        <a href="logout">
                            <div class="g il fr" alt="Logout" title="Logout"></div>
                        </a>

                        <div class="z"></div>
                    </div>

                    <div class="n">
                        <?php if (isset($messages)) {
                                foreach ($messages as $message) { 
                                    if ($message['user2_guid'] == $_SESSION[USESSION]->user_guid && $message['direction'] == 1) $sent = true; else $sent = false;
                        ?>

                        <div class="o <?php echo $sent ? "fr" : "fl"; ?>">
                            <div class="q <?php echo $sent ? "s" : "r"; ?>">
                                <?php echo $message['message']; ?>
                            </div>

                            <div class="z j aa <?php echo $sent ? "fr" : "fl"; ?>" data-md="<?php echo $message['made_date']; ?>">
                                <?php echo nicetime($message['made_date']); ?>
                            </div>
                        </div>
                        <div class="p"></div>
                        <?php } } ?>

                        <div id="l"></div>
                    </div>
                </div>

                <form method="POST" action="">
                    <div class="t">
                        <textarea class="u" name="m" id="m" placeholder="Enter your message here." autofocus <?php if(!isset($menu) || $menu != "new") echo "onkeyup=\"if(event.keyCode == 13 && !event.shiftKey) c();\""; ?>></textarea>
                        <input class="v w" type="submit" name="submit" value="Send" <?php if(!isset($menu) || $menu != "new") echo "onclick=\"return c();\""; ?>>
                    </div>
                </form>
            </div>

            <div class="h" id="c"><?php echo isset($cguid) ? $cguid : ""; ?></div>
            <div class="h" id="g"><?php echo isset($guid) ? $guid : ""; ?></div>
