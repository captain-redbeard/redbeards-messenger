            <div class="cover-wrapper">
                <form method="POST" action="<?php echo BASE_HREF; ?>/requests/add">
                    <table class="single-table">
                        <thead>
                            <tr>
                                <th>Add Contact</th>
                            </tr>
                        </thead>

                        <tbody>
                            <tr>
                                <td>
                                    <p>
                                        Contacts are added by sharing a <strong>unique URL</strong> with the person who you want to add. Once they go to the URL and <strong>login</strong> they will be added 
                                        to your contact list and you to theirs.
                                    </p>

                                    <p>
                                        These URLs are unique and will expire in the set time limit or when the request is used.
                                    </p>

                                    <p>
                                        Create a new request for each contact.
                                    </p>
                                </td>
                            </tr>

                            <tr>
                                <td class="center">
                                    <a href="<?php echo BASE_HREF; ?>/requests">View existing requests</a>
                                </td>
                            </tr>

                            <tr>
                                <td>
                                    <input class="glow w100" type="text" name="requestname" title="Request name" tabindex="1" placeholder="Request name (optional)" autofocus>
                                </td>
                            </tr>

                            <tr>
                                <td>
                                    <select class="glow w100" name="expire" tabindex="2">
                                        <option value="-1" selected disabled>Select Expire Time (hours)</option>
                                        <?php foreach ($data['expire_times'] as $tz) { ?>

                                        <option value="<?php echo $tz; ?>"><?php echo $tz; ?> hour<?php if($tz > 1) echo "s"; ?></option>
                                        <?php } ?>
                                        
                                    </select>
                                </td>
                            </tr>
                        </tbody>

                        <tfoot>
                            <tr>
                                <td>
                                    <input type="hidden" name="token" value="<?php echo $data['token']; ?>">
                                    <input class="raw-button blue-outline w100" type="submit" name="submit" title="Submit" tabindex="3" value="Get URL">
                                    <?php if($data['url'] != '') { ?>
                                        
                                    <br/>
                                    <p class="small-text center"><strong>Share the below URL with the desired person.</strong></p>
                                    <div class="message-info"><?php echo $data['url']; ?></div>
                                    
                                    <?php } ?>
                                    <?php if($data['error'] != '') { ?>
                                        
                                    <br/>
                                    <div class="message-error"><?php echo $data['error']; ?></div>
                                    <?php } ?>

                                </td> 
                            </tr>

                            <tr><td></td></tr>
                            <tr><td></td></tr>

                            <tr>
                                <td>
                                    <a href="<?php echo BASE_HREF; ?>/conversations">Return</a>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </form>
            </div>
