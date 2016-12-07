            <div class="cover-wrapper">
                <form method="POST" action="">
                    <table class="single-table">
                        <thead>
                            <tr>
                                <th>Error</th>
                            </tr>
                        </thead>

                        <tbody>
                            <tr>
                                <td>
                                    <?php if($data['error'] != '') { ?>
                                        
                                    <br/>
                                    <div class="message-error"><?php echo $data['error']; ?></div>
                                    
                                    <?php } ?>

                                </td> 
                            </tr>
                        </tbody>

                        <tfoot>
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
