            <div class="box-wrapper">
                <form class="box-form" method="POST" action="<?=$data['BASE_HREF'];?>/settings/delete">
                    <h1>Delete Account</h1>
                    
                    <p>
                        Deleting your account is permanent, there is <strong>no recovery</strong>. If you are sure you wish to delete your account, enter your password then press delete.
                    </p>
                    
                    <input class="glow w100" type="password" name="password" title="Password" tabindex="1" placeholder="Password">
                    
                    <input class="raw-button blue-outline w100" type="submit" name="submit" title="Submit" tabindex="3" value="Delete">
                    <input type="hidden" name="token" value="<?=$data['token'];?>">
                    
                    <br>
                    <div class="message-error"><?=$data['error'];?></div>
                    
                    <a href="<?=$data['BASE_HREF'];?>/settings">Return</a>
                </form>
            </div>
