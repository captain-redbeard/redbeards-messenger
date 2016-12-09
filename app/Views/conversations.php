            <div class="a">
                <div class="b">
                    <div class="c">
                        <a id="us" href="<?php echo BASE_HREF; ?>/settings">
                            <div class="g is" alt="Settings" title="Settings"></div>
                        </a>
                        
                        <div class="fr">
                        <a id="ac" href="<?php echo BASE_HREF; ?>/requests/add">
                            <div class="g ia" alt="Add Contact" title="Add Contact"></div>
                        </a>
                    </div>
                </div>
                    
                <div class="d">
                    <a class="v gb y" href="<?php echo BASE_HREF; ?>/contacts">Start Conversation</a>
                    
                    <a class="x" href="<?php echo BASE_HREF; ?>/contacts">
                        <div class="g ic" alt="Contacts" title="Contacts"></div>
                    </a>
                </div>
                    
                <div class="e" id="h">
                    <?php if ($data['newconversation'] != null && count($data['newconversation']) > 0) { ?>
                        
                    <div class="f k">
                        <div class="i"><?php echo $data['newconversation'][0]['username']; ?></div>
                        <div class="j fr"><?php echo $data['currenttime']; ?></div>
                        <div class="z"></div>
                    </div>
                    <?php } ?>
                    <?php foreach ($data['conversations'] as $conversation) { ?>
                        
                    <div class="f <?php if ($data['cguid'] && $conversation->conversation_guid == $data['cguid']) echo "k"; ?>">
                        <a href="<?php echo BASE_HREF; ?>/conversations/display/<?php echo $conversation->contact_guid . "/" . $conversation->conversation_guid; ?>#l">
                            <div class="i"><?php if ($conversation->alias != "") echo $conversation->alias . " - "; echo $conversation->username; ?></div>
                        </a>
                        <div class="cc j fr" data-md="<?php echo $conversation->made_date; ?>">
                            <?php echo $conversation->getMadeDate(); ?>
                            &nbsp;
                            
                            <a href="<?php echo BASE_HREF; ?>/conversations/delete/<?php echo $conversation->conversation_guid; ?>">
                                <div class="g id ac" alt="Delete Conversation" title="Delete Conversation"></div>
                            </a>
                        </div>
                        
                        <div class="z"></div>
                    </div>
                    
                    <?php } ?>
                    
                </div>
                </div>
                
                <div class="l">
                    <div class="m">
                        <a id="ul" href="<?php echo BASE_HREF; ?>/logout">
                            <div class="g il fr" alt="Logout" title="Logout"></div>
                        </a>
                        
                        <div class="z"></div>
                    </div>
                    
                    <div class="n">
                        <?php if ($data['messages'] != null) {
                                foreach ($data['messages'] as $message) { 
                                    if ($message->user2_guid == $_SESSION[USESSION]->user_guid && $message->direction == 1) $sent = true; else $sent = false;
                        ?>
                        
                        <div class="o <?php echo $sent ? "fr" : "fl"; ?>">
                            <div class="q <?php echo $sent ? "s" : "r"; ?>">
                                <?php echo $message->message; ?>
                                
                            </div>
                            
                            <div class="z j aa <?php echo $sent ? "fr" : "fl"; ?>" data-md="<?php echo $message->made_date; ?>">
                                <?php echo $message->getMadeDate(); ?>
                                
                            </div>
                        </div>
                        <div class="p"></div>
                        <?php } } elseif ($data['guid'] == null) { ?>
                        
                        <h3 class="ab">Start a new conversation or select an existing one on the left.</h3>
                        <?php } ?>
                        
                        <div id="l"></div>
                    </div>
                </div>
                
                <form method="POST" action="<?php echo BASE_HREF; ?>/conversations/send" id="s">
                    <div class="t">
                        <input type="hidden" name="token" value="<?php echo $data['token']; ?>">
                        <textarea class="u" name="m" id="m" placeholder="Enter your message here."<?php if ($data['guid'] == null) echo " disabled"; else echo " autofocus"; ?>></textarea>
                        <input class="v w" type="submit" name="ssubmit" id="z" value="Send"<?php if ($data['guid'] == null) echo " disabled"; ?>>
                        <input type="hidden" name="tg" value="<?php echo $data['guid']; ?>">
                    </div>
                </form>
            </div>
            
            <div class="h" id="c"><?php echo $data['cguid'] != null ? $data['cguid'] : ""; ?></div>
            <div class="h" id="g"><?php echo $data['guid'] != null ? $data['guid'] : ""; ?></div>
