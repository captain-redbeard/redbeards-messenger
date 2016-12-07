            <div class="cover-wrapper">
                <form method="POST" action="">
                    <table class="single-table">
                        <thead>
                            <tr>
                                <th>Edit Contact</th>
                            </tr>
                        </thead>

                        <tbody>
                            <tr>
                                <td>
                                    <input class="glow w100" type="text" name="alias" title="Alias" tabindex="1" placeholder="Alias" value="<?php echo $data['contact']->alias; ?>">
                                </td>
                            </tr>

                            <tr>
                                <td>
                                    <input class="glow disabled w100" type="text" name="username" title="Username" tabindex="2" placeholder="Username" value="<?php echo $data['contact']->username; ?>" disabled>
                                </td>
                            </tr>
                        </tbody>

                        <tfoot>
                            <tr>
                                <td>
                                    <input class="raw-button blue-outline w100" type="submit" name="submit" title="Submit" tabindex="4" value="Save">
                                    <?php if($data['error'] != '') { ?>
                                        
                                    <br/>
                                    <div class="message-error"><?php echo $data['error']; ?></div>
                                    <?php } ?>
                                    
                                </td> 
                            </tr>

                            <tr><td></td></tr>
                            <tr><td></td></tr>

                            <tr>
                                <td>
                                    <a href="conversations">Return</a>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </form>
            </div>
