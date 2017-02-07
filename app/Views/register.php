            <div class="cover-wrapper">
                <form method="POST" action="<?=$data['BASE_HREF'];?>/register/user">
                    <table class="single-table">
                        <thead>
                            <tr>
                                <th>Register</th>
                            </tr>
                        </thead>

                        <tbody>
                            <tr>
                                <td>
                                    <input class="glow w100" type="text" name="username" title="Username" tabindex="1" placeholder="Username" autofocus value="<?=$data['username'];?>" required>
                                </td>
                            </tr>

                            <tr>
                                <td>
                                    <input class="glow w100" type="password" name="password" title="Password" tabindex="2" placeholder="Password" required>
                                </td>
                            </tr>

                            <tr>
                                <td>
                                    <input class="glow w100" type="password" name="passphrase" title="Private key passphrase" tabindex="3" placeholder="Private key passphrase (optional)">
                                </td>
                            </tr>

                            <tr>
                                <td>
                                    <select class="glow w100" name="timezone" tabindex="4" required>
                                        <option value="-1" selected disabled>Select Timezone</option>
                                        <?php foreach ($data['timezones'] as $tz) { ?>

                                        <option value="<?=$tz;?>"<?php if ($tz === $data['timezone']) echo " selected"; ?>><?=$tz;?></option>
                                        <?php } ?>
                                        
                                    </select>
                                </td>
                            </tr>
                        </tbody>

                        <tfoot>
                            <tr>
                                <td>
                                    <input type="hidden" name="token" value="<?=$data['token'];?>">
                                    <input class="raw-button blue-outline w100" type="submit" name="submit" title="Submit" tabindex="5" value="Submit">
                                    
                                    <br/>
                                    <div class="message-error"><?=$data['error'];?></div>
                                </td>
                            </tr>

                            <tr><td></td></tr>
                            <tr><td></td></tr>

                            <tr>
                                <td>
                                    <a class="font-color-blue" href="<?=$data['BASE_HREF'];?>/login">Login</a>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </form>
            </div>
