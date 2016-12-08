            <div class="cover-wrapper">
                <form>
                    <table class="multi-table">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>URL</th>
                                <th>Expires</th>
                                <th>Action</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php foreach ($data['requests'] as $request) { ?>

                            <tr>
                                <td><?php echo $request->request_name; ?></td>
                                <td><?php echo $request->url; ?></td>
                                <td class="text-right">
                                    <?php echo $request->expire_time; ?>
                                    
                                </td>
                                <td class="text-right">
                                    <a href="<?php echo BASE_HREF; ?>/requests/delete/<?php echo $request->request_guid; ?>">
                                        <div class="grow idc" alt="Delete" title="Delete Request"></div>
                                    </a>
                                </td>
                            </tr>
                            <?php }?>
                            
                        </tbody>

                        <tfoot>
                            <tr><td></td></tr>
                            <tr><td></td></tr>

                            <tr>
                                <td>
                                    <a href="<?php echo BASE_HREF; ?>/conversations">Return</a>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </form>
            </div>
