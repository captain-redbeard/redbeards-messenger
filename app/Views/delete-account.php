            <div class="cover-wrapper">
                <form method="POST" action="<?=BASE_HREF;?>/settings/delete">
                    <table class="single-table">
                        <thead>
                            <tr>
                                <th>Delete Account</th>
                            </tr>
                        </thead>

                        <tbody>
                            <tr>
                                <td>
                                    <p>
                                        Deleting your account is permanent, there is <strong>no recovery</strong>. If you are sure you wish to delete your account, enter your password then press delete.
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
                                    <input type="hidden" name="token" value="<?=$data['token'];?>">
                                    <a class="raw-button blue-button w49 fl" href="<?=BASE_HREF;?>/settings" tabindex="2">Return</a>
                                    <input class="raw-button red-button w49 fr" type="submit" name="submit" title="Submit" tabindex="3" value="Delete">
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
