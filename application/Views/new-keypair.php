            <div class="box-wrapper">
                <form class="box-form" method="POST" action="<?=$data['BASE_HREF'];?>/settings/newkeypair">
                    <h1>New Keypair</h1>
                    
                    <p>
                         Generating a new keypair will delete your old keypair, conversations and messages.
                    </p>
                    
                    <p>
                        If you are sure you wish to generate a new keypair enter your password and a new passphrase then press <strong>Generate</strong>.
                    </p>
                    
                    <input class="glow w100" type="password" name="password" title="Password" tabindex="1" placeholder="Password">
                    
                    <input class="glow w100" type="password" name="passphrase" title="Private key passphrase" tabindex="2" placeholder="Private key passphrase (optional)">
                    
                    <input class="raw-button blue-outline w100" type="submit" name="submit" title="Submit" tabindex="4" value="Generate">
                    <input type="hidden" name="token" value="<?=$data['token'];?>">
                    
                    <br>
                    <div class="message-error"><?=$data['error'];?></div>
                    
                    <a href="<?=$data['BASE_HREF'];?>/settings">Return</a>
                </form>
            </div>
