            <div class="cover-wrapper">
                <form method="POST" action="<?=$data['BASE_HREF'];?>/settings/update">
                    <table class="single-table">
                        <thead>
                            <tr>
                                <th>Settings</th>
                            </tr>
                        </thead>
                        
                        <tbody>
                            <tr>
                                <td>
                                    <input class="glow w100" type="text" name="username" title="Username" tabindex="1" placeholder="Username" value="<?=$data['user']->username;?>" required>
                                </td>
                            </tr>
                            
                            <tr>
                                <td>
                                    <select class="glow w100" name="timezone" tabindex="2" required>
                                        <?php foreach ($data['timezones'] as $tz) { ?>
                                            
                                        <option value="<?=$tz;?>"<?php if ($tz === $data['user']->timezone) echo " selected"; ?>><?=$tz;?></option>
                                        <?php } ?>
                                        
                                    </select>
                                </td>
                            </tr>
                            
                            <tr>
                                <td>
                                    <?php if ($data['user']->mfa_enabled == -1) { ?>
                                    
                                    <a class="raw-button red-button w49 fl" href="<?=$data['BASE_HREF'];?>/mfa/disable">Disable MFA</a>
                                    <?php } else { ?>
                                    
                                    <a class="raw-button blue-button w49 fl" href="<?=$data['BASE_HREF'];?>/mfa/enable">Enable MFA</a>
                                    <?php } ?>
                                    
                                    <a class="raw-button blue-button w49 fr" href="<?=$data['BASE_HREF'];?>/settings/reset">Reset Password</a>
                                </td>
                            </tr>
                            
                            <tr>
                                <td>
                                    <?php if ($data['user']->expire > 0) { ?>
                                    
                                    <a class="raw-button red-button w100" href="<?=$data['BASE_HREF'];?>/deletion/disable">Disable Deletion Policy (<?=$data['user']->expire . " days";?>)</a>
                                    <?php } else { ?>
                                    
                                    <a class="raw-button blue-button w100" href="<?=$data['BASE_HREF'];?>/deletion/enable">Enable Deletion Policy</a>
                                    <?php } ?>
                                    
                                </td>
                            </tr>
                            
                            <tr><td></td></tr>
                            <tr><td></td></tr>
                            
                            <tr>
                                <td>
                                    <a class="raw-button red-button w100" href="<?=$data['BASE_HREF'];?>/settings/newkeypair">Generate new Keypair</a>
                                </td>
                            </tr>
                            
                            <tr>
                                <td>
                                    <a class="raw-button red-button w100" href="<?=$data['BASE_HREF'];?>/settings/delete">Delete Account</a>
                                </td>
                            </tr>
                            
                            <tr><td></td></tr>
                            <tr><td></td></tr>
                        </tbody>
                        
                        <tfoot>
                            <tr>
                                <td>
                                    <input type="hidden" name="token" value="<?=$data['token'];?>">
                                    <input class="raw-button blue-outline w100" type="submit" name="submit" title="Submit" tabindex="3" value="Save">
                                    
                                    <br/>
                                    <div class="message-error"><?=$data['error'];?></div>
                                </td>
                            </tr>
                            
                            <tr><td></td></tr>
                            <tr><td></td></tr>
                            
                            <tr>
                                <td>
                                    <a href="<?=$data['BASE_HREF'];?>/conversations">Return</a>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </form>
            </div>
