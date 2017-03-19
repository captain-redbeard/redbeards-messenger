            <div class="box-wrapper">
                <form class="box-form" method="POST" action="<?=$data['BASE_HREF'];?>/settings/reset">
                    <h1>Change Password</h1>
                    
                    <input class="glow w100" type="password" name="password" title="Password" tabindex="1" placeholder="Password" autofocus>
                    
                    <input class="glow w100" type="password" name="npassword" title="New password" tabindex="2" placeholder="New password">
                    
                    <input class="glow w100" type="password" name="cpassword" title="Confirm password" tabindex="3" placeholder="Confirm password">
                    
                    <input class="raw-button blue-outline w100" type="submit" name="submit" title="Submit" tabindex="4" value="Save">
                    <input type="hidden" name="token" value="<?=$data['token'];?>">
                    
                    <br>
                    <div class="message-error"><?=$data['error'];?></div>
                    
                    <a href="<?=$data['BASE_HREF'];?>/settings">Return</a>
                </form>
            </div>
