            <div class="box-wrapper sbw">
                <form class="box-form" method="POST" action="<?=$data['BASE_HREF'];?>/requests/add-request">
                    <h1>Add Contact</h1>
                    
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
                    
                    <a href="<?=$data['BASE_HREF'];?>/requests">View existing requests</a>
                    <br>
                    <br>
                    
                    <input class="glow w100" type="text" name="requestname" title="Request name" tabindex="1" placeholder="Request name (optional)" autofocus>
                    
                    <select class="glow w100" name="expire" tabindex="2">
                        <option value="-1" selected disabled>Select Expire Time (hours)</option>
                        <?php foreach ($data['expire_times'] as $tz) { ?>

                        <option value="<?=$tz;?>"><?=$tz;?> hour<?php if($tz > 1) echo "s"; ?></option>
                        <?php } ?>
                    
                    </select>
                    
                    <input class="raw-button blue-outline w100" type="submit" name="submit" title="Submit" tabindex="3" value="Get URL">
                    <input type="hidden" name="token" value="<?=$data['token'];?>">
                    <?php if($data['url'] != '') { ?>
                    
                    <br>
                    <p class="small-text center"><strong>Share the below URL with the desired person.</strong></p>
                    <div class="message-info"><?=$data['url'];?></div>
                        
                    <?php } ?>

                    <br>
                    <div class="message-error"><?=$data['error'];?></div>
                    
                    <a href="<?=$data['BASE_HREF'];?>/conversations">Return</a>
                </form>
            </div>
