            <div class="box-wrapper">
                <form class="box-form" method="POST" action="<?=$data['BASE_HREF'];?>/login/authenticate">
                    <input class="glow w100" type="text" name="username" title="Username" tabindex="1" placeholder="Username" autofocus value="<?=$data['username'];?>" required>
                    
                    <input class="glow w100" type="password" name="password" title="Password" tabindex="2" placeholder="Password" required>
                    
                    <input class="glow w100" type="password" name="passphrase" title="Private key passphrase" tabindex="3" placeholder="Private key passphrase">
                    
                    <input class="glow w100" type="text" name="mfa" title="MFA Code" tabindex="4" placeholder="MFA Code">
                    
                    <input type="hidden" name="token" value="<?=$data['token'];?>">
                    <input class="raw-button blue-outline w100" type="submit" name="submit" title="Submit" tabindex="5" value="Login">
                    
                    <div class="message-error"><?=$data['error'];?></div>
                    
                    <p class="message">
                        New here? <a href="<?=$data['BASE_HREF'];?>/register">Register</a>
                    </p>
                </form>
            </div>
