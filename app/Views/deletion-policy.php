            <div class="cover-wrapper">
                <form method="POST" action="<?=$data['BASE_HREF'];?>/deletion/enable">
                    <table class="single-table">
                        <thead>
                            <tr>
                                <th>Setup Deletion Policy</th>
                            </tr>
                        </thead>

                        <tbody>
                            <tr>
                                <td>
                                    <p>Your account can be scheduled to delete automatically based on the deletion policy set below.</p>
                                    <p>The time is based from your last login, so if you wish to keep your account active, just login before the set time.</p>
                                    <p>When your account is deleted, all your corresponding data is deleted. There is <strong>no recovery.</strong></p>
                                </td>
                            </tr>
                            
                            <tr>
                                <td>
                                    <select class="glow w100" name="expire" tabindex="2" required>
                                        <option value="-1" selected disabled>Select Expire Time (days)</option>
                                        <?php foreach ($data['days'] as $day) { ?>
                                            
                                        <option value="<?=$day;?>"<?php if ($day === $data['expire']) echo " selected"; ?>><?=$day;?> days</option>
                                        <?php } ?>
                                        
                                    </select>
                                </td>
                            </tr>
                        </tbody>

                        <tfoot>
                            <tr>
                                <td>
                                    <input type="hidden" name="token" value="<?=$data['token'];?>">
                                    <input class="raw-button blue-outline w100" type="submit" name="submit" title="Submit" tabindex="3" value="Enable">
                                    
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
