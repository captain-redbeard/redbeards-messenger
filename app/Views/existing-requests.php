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
                                <td><?=$request->request_name;?></td>
                                <td><?=$request->url;?></td>
                                <td class="text-right">
                                    <?=$request->expire_time;?>
                                    
                                </td>
                                <td class="text-right">
                                    <a href="<?=$data['BASE_HREF'];?>/requests/delete/<?=$request->request_guid;?>">
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
                                    <a href="<?=$data['BASE_HREF'];?>/requests/add">Return</a>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                    
                    <div class="message-error"><?=$data['error'];?></div>
                </form>
            </div>
