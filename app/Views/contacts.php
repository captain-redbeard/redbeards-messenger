            <div class="cover-wrapper">
                <form>
                    <table class="multi-table">
                        <thead>
                            <tr>
                                <th>Alias</th>
                                <th>Username</th>
                                <th>Made date</th>
                                <th>Action</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php foreach ($data['contacts'] as $contact) { ?>

                            <tr>
                                <td><a href="<?=$data['BASE_HREF'];?>/conversations/new/<?=$contact->contact_guid;?>"><?=$contact->alias;?></a></td>
                                <td><a href="<?=$data['BASE_HREF'];?>/conversations/new/<?=$contact->contact_guid;?>"><?=$contact->username;?></a></td>
                                <td class="text-right"><?=$contact->getMadeDate();?></td>
                                <td class="text-right">
                                    <a href="<?=$data['BASE_HREF'];?>/contacts/edit/<?=$contact->contact_guid;?>">
                                        <div class="grow ie" alt="Edit" title="Edit Contact"></div>
                                    </a>
                                    &nbsp;
                                    <a href="<?=$data['BASE_HREF'];?>/contacts/delete/<?=$contact->contact_guid;?>">
                                        <div class="grow idc" alt="Delete" title="Delete Contact"></div>
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
                                    <a href="<?=$data['BASE_HREF'];?>/conversations">Return</a>
                                    <input type="hidden" name="token" value="<?=$data['token'];?>">
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                    
                    <div class="message-error"><?=$data['error'];?></div>
                    
                </form>
            </div>
