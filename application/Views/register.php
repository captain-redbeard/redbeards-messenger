            <div class="box-wrapper">
                <form class="box-form" method="POST" action="<?=$data['BASE_HREF'];?>/register/user">
                    <input class="glow w100" type="text" name="username" title="Username" tabindex="1" placeholder="Username" autofocus value="<?=$data['username'];?>" required>

                    <input class="glow w100" type="password" name="password" title="Password" tabindex="2" placeholder="Password" required>
                    
                    <input class="glow w100" type="password" name="passphrase" title="Private key passphrase" tabindex="3" placeholder="Private key passphrase">
                    
                    <select class="glow w100" name="timezone" tabindex="4" required>
                        <option value="-1" selected disabled>Select Timezone</option>
                        <?php foreach ($data['timezones'] as $tz) { ?>
                        
                            <option value="<?=$tz;?>"<?php if ($tz === $data['timezone']) echo " selected"; ?>><?=$tz;?></option>
                        <?php } ?>
                        
                     </select>
                    
                    <input type="hidden" name="token" value="<?=$data['token'];?>">
                    <input class="raw-button blue-outline w100" type="submit" name="submit" title="Submit" tabindex="5" value="Register">
                    
                    <br>
                    <div class="message-error"><?=$data['error'];?></div>
                    
                    <p class="message">
                        Already registered? <a class="font-color-blue" href="<?=$data['BASE_HREF'];?>/login">Login</a>
                    </p>
                </form>
            </div>
