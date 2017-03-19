            <div class="box-wrapper mw800">
                <form class="box-form mw800">
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
                    </table>
                    
                    <div class="message-error"><?=$data['error'];?></div>
                    
                    <a href="<?=$data['BASE_HREF'];?>/requests/add">Return</a>
                </form>
            </div>
