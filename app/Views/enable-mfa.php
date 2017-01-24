            <div class="cover-wrapper">
                <form method="POST" action="<?=BASE_HREF;?>/mfa/activate">
                    <table class="single-table">
                        <thead>
                            <tr>
                                <th>Enable MFA</th>
                            </tr>
                        </thead>

                        <tbody>
                            <tr>
                                <td>
                                    <p>If your virtual MFA application supports scanning QR codes, scan the following image.</p>
                                    <div class="qr-wrapper">
                                        <img src="<?="data:image/png;base64," . base64_encode($data['qr_code']->get());?>" alt="">
                                    </div>
                                </td>
                            </tr>

                            <tr>
                                <td>
                                    <div class="message-info"><?=$data['secret_key'];?></div>
                                    <p>After the application is configured, enter two consecutive authentication codes in the boxes below and click Activate.</p>
                                </td>
                            </tr>

                            <tr>
                                <td>
                                    <input class="glow w100" type="text" name="code1" title="Code 1" tabindex="1" placeholder="Code 1">
                                </td>
                            </tr>

                            <tr>
                                <td>
                                    <input class="glow w100" type="text" name="code2" title="Code 2" tabindex="2" placeholder="Code 2">
                                </td>
                            </tr>
                        </tbody>

                        <tfoot>
                            <tr>
                                <td>
                                    <input type="hidden" name="token" value="<?=$data['token'];?>">
                                    <input class="raw-button blue-outline w100" type="submit" name="submit" title="Submit" tabindex="4" value="Activate">
                                    <?php if ($data['error'] !== '') { ?>
                                        
                                    <br/>
                                    <div class="message-error"><?=$data['error'];?></div>
                                    <?php } ?>
                                    
                                </td> 
                            </tr>

                            <tr><td></td></tr>
                            <tr><td></td></tr>

                            <tr>
                                <td>
                                    <a href="<?=BASE_HREF;?>/conversations">Return</a>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </form>
            </div>
