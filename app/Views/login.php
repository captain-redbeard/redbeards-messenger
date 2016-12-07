            <div class="cover-wrapper">
                <form method="POST" action="login/authenticate">
                    <table class="single-table">
                        <thead>
                            <tr>
                                <th>Login</th>
                            </tr>
                        </thead>

                        <tbody>
                            <tr>
                                <td>
                                    <input class="glow w100" type="text" name="username" title="Username" tabindex="1" placeholder="Username" autofocus value="<?php echo $data['username']; ?>">
                                </td>
                            </tr>

                            <tr>
                                <td>
                                    <input class="glow w100" type="password" name="password" title="Password" tabindex="2" placeholder="Password">
                                </td>
                            </tr>

                            <tr>
                                <td>
                                    <input class="glow w100" type="password" name="passphrase" title="Private key passphrase" tabindex="3" placeholder="Private key passphrase">
                                </td>
                            </tr>

                            <tr>
                                <td>
                                    <input class="glow w100" type="text" name="mfa" title="MFA Code" tabindex="4" placeholder="MFA Code (if enabled)">
                                </td>
                            </tr>
                        </tbody>

                        <tfoot>
                            <tr>
                                <td>
                                    <input class="raw-button blue-outline w100" type="submit" name="submit" title="Submit" tabindex="5" value="Submit">
                                    <?php if ($data['error'] != '') { ?>
                                    <br/>
                                    <div class="message-error"><?php echo $data['error']; ?></div>
                                    <?php } ?>

                                </td> 
                            </tr>

                            <tr><td></td></tr>
                            <tr><td></td></tr>

                            <tr>
                                <td>
                                    <a href="register">Register</a>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </form>
            </div>
