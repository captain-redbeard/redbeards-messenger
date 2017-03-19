            <div class="box-wrapper">
                <form class="box-form p25" method="POST" action="<?=$data['BASE_HREF'];?>/settings/update">
                    <h1>Settings</h1>
                            
                    <input class="glow w100" type="text" name="username" title="Username" tabindex="1" placeholder="Username" value="<?=$data['user']->username;?>" required>
                               
                    <select class="glow w100" name="timezone" tabindex="2" required>
                    <?php foreach ($data['timezones'] as $tz) { ?>
                        
                        <option value="<?=$tz;?>"<?php if ($tz === $data['user']->timezone) echo " selected"; ?>><?=$tz;?></option>
                    <?php } ?>
                    
                    </select>
                    
                    <?php if ($data['user']->mfa_enabled) { ?>
                    
                    <a class="raw-button red-button w49 fl" href="<?=$data['BASE_HREF'];?>/mfa/disable">Disable MFA</a>
                    <?php } else { ?>
                    
                    <a class="raw-button blue-button w49 fl" href="<?=$data['BASE_HREF'];?>/mfa/enable">Enable MFA</a>
                    <?php } ?>
                    
                    <a class="raw-button blue-button w49 fr" href="<?=$data['BASE_HREF'];?>/settings/reset">Reset Password</a>
                    
                    <?php if ($data['user']->expire > 0) { ?>
                    
                    <a class="raw-button red-button w100" href="<?=$data['BASE_HREF'];?>/deletion/disable">Disable Deletion Policy (<?=$data['user']->expire . " days";?>)</a>
                    <?php } else { ?>
                    
                    <a class="raw-button blue-button w100" href="<?=$data['BASE_HREF'];?>/deletion/enable">Enable Deletion Policy</a>
                    <?php } ?>
                    
                    <a class="raw-button red-button w100" href="<?=$data['BASE_HREF'];?>/settings/newkeypair">Generate new Keypair</a>
                    
                    <a class="raw-button red-button w100" href="<?=$data['BASE_HREF'];?>/settings/delete">Delete Account</a>
                    
                    <input class="raw-button blue-outline w100" type="submit" name="submit" title="Submit" tabindex="3" value="Save">
                    <input type="hidden" name="token" value="<?=$data['token'];?>">
                    
                    <br>
                    <div class="message-error"><?=$data['error'];?></div>
                    
                    <a href="<?=$data['BASE_HREF'];?>/conversations">Return</a>
                </form>
            </div>
