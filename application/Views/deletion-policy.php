            <div class="box-wrapper">
                <form class="box-form" method="POST" action="<?=$data['BASE_HREF'];?>/deletion/enable">
                    <h1>Setup Deletion Policy</h1>
                    
                    <p>Your account can be scheduled to delete automatically based on the deletion policy set below.</p>
                    <p>The time is based from your last login, so if you wish to keep your account active, just login before the set time.</p>
                    <p>When your account is deleted, all your corresponding data is deleted. There is <strong>no recovery.</strong></p>
                    
                    <select class="glow w100" name="expire" tabindex="2" required>
                        <option value="-1" selected disabled>Select Expire Time (days)</option>
                        <?php foreach ($data['days'] as $day) { ?>
                        
                        <option value="<?=$day;?>"<?php if ($day === $data['expire']) echo " selected"; ?>><?=$day;?> days</option>
                        <?php } ?>
                        
                    </select>
                    
                    <input class="raw-button blue-outline w100" type="submit" name="submit" title="Submit" tabindex="3" value="Enable">
                    <input type="hidden" name="token" value="<?=$data['token'];?>">
                    
                    <br>
                    <div class="message-error"><?=$data['error'];?></div>
                    
                    <a href="<?=$data['BASE_HREF'];?>/settings">Return</a>
                </form>
            </div>
