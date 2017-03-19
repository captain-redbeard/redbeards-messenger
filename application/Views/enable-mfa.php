            <div class="box-wrapper sbw">
                <form class="box-form" method="POST" action="<?=$data['BASE_HREF'];?>/mfa/activate">
                    <h1>Enable MFA</h1>

                    <p>If your virtual MFA application supports scanning QR codes, scan the following image.</p>
                    <div class="qr-wrapper">
                        <img src="<?="data:image/png;base64," . base64_encode($data['qr_code']->get());?>" alt="">
                    </div>
                    
                    <div class="message-info"><?=$data['secret_key'];?></div>
                    <p>After the application is configured, enter two consecutive authentication codes in the boxes below and click Activate.</p>
                    
                    <input class="glow w100" type="text" name="code1" title="Code 1" tabindex="1" placeholder="Code 1">
                    
                    <input class="glow w100" type="text" name="code2" title="Code 2" tabindex="2" placeholder="Code 2">
                    
                    <input class="raw-button blue-outline w100" type="submit" name="submit" title="Submit" tabindex="4" value="Activate">
                    <input type="hidden" name="token" value="<?=$data['token'];?>">
                    
                    <br>
                    <div class="message-error"><?=$data['error'];?></div>
                    
                    <a href="<?=$data['BASE_HREF'];?>/settings">Return</a>
                </form>
            </div>
