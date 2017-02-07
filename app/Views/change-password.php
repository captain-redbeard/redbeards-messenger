            <div class="cover-wrapper">
                <form method="POST" action="<?=$data['BASE_HREF'];?>/settings/reset">
                    <table class="single-table">
                        <thead>
                            <tr>
                                <th>Change Password</th>
                            </tr>
                        </thead>

                        <tbody>
                            <tr>
                                <td>
                                    <input class="glow w100" type="password" name="password" title="Password" tabindex="1" placeholder="Password" autofocus>
                                </td>
                            </tr>

                            <tr>
                                <td>
                                    <input class="glow w100" type="password" name="npassword" title="New password" tabindex="2" placeholder="New password">
                                </td>
                            </tr>

                            <tr>
                                <td>
                                    <input class="glow w100" type="password" name="cpassword" title="Confirm password" tabindex="3" placeholder="Confirm password">
                                </td>
                            </tr>
                        </tbody>

                        <tfoot>
                            <tr>
                                <td>
                                    <input type="hidden" name="token" value="<?=$data['token'];?>">
                                    <input class="raw-button blue-outline w100" type="submit" name="submit" title="Submit" tabindex="4" value="Save">
                                    
                                    <br/>
                                    <div class="message-error"><?=$data['error'];?></div>
                                </td>
                            </tr>

                            <tr><td></td></tr>
                            <tr><td></td></tr>

                            <tr>
                                <td>
                                    <a href="<?=$data['BASE_HREF'];?>/settings">Return</a>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </form>
            </div>
