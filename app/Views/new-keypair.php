            <div class="cover-wrapper">
                <form method="POST" action="<?=BASE_HREF;?>/settings/newkeypair">
                    <table class="single-table">
                        <thead>
                            <tr>
                                <th>New Keypair</th>
                            </tr>
                        </thead>

                        <tbody>
                            <tr>
                                <td>
                                    <p>
                                        Generating a new keypair will delete your old keypair, conversations and messages.
                                    </p>
                                    
                                    <p>
                                        If you are sure you wish to generate a new keypair enter your password and a new passphrase then press <strong>Generate</strong>.
                                    </p>
                                </td>
                            </tr>
                            
                            <tr>
                                <td>
                                    <input class="glow w100" type="password" name="password" title="Password" tabindex="1" placeholder="Password">
                                </td>
                            </tr>
                            
                            <tr>
                                <td>
                                    <input class="glow w100" type="password" name="passphrase" title="Private key passphrase" tabindex="2" placeholder="Private key passphrase (optional)">
                                </td>
                            </tr>

                            <tr>
                                <td>
                                    <input type="hidden" name="token" value="<?=$data['token'];?>">
                                    <a class="raw-button blue-button w49 fl" href="<?=BASE_HREF;?>/settings" tabindex="3">Return</a>
                                    <input class="raw-button red-button w49 fr" type="submit" name="submit" title="Submit" tabindex="4" value="Generate">
                                </td>
                            </tr>
                        </tbody>

                        <tfoot>
                            <tr>
                                <td>
                                    <?php if ($data['error'] !== '') { ?>
                                        
                                    <br/>
                                    <div class="message-error"><?=$data['error'];?></div>
                                    <?php } ?>
                                    
                                </td> 
                            </tr>
                        </tfoot>
                    </table>
                </form>
            </div>
