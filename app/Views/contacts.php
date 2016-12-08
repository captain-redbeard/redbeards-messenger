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
                                <td><a href="<?php echo BASE_HREF; ?>/conversations/new/<?php echo $contact->contact_guid; ?>"><?php echo $contact->alias; ?></a></td>
                                <td><a href="<?php echo BASE_HREF; ?>/conversations/new/<?php echo $contact->contact_guid; ?>"><?php echo $contact->username; ?></a></td>
                                <td class="text-right"><?php echo $contact->getMadeDate(); ?></td>
                                <td class="text-right">
                                    <a href="<?php echo BASE_HREF; ?>/contacts/edit/<?php echo $contact->contact_guid; ?>">
                                        <div class="grow ie" alt="Edit" title="Edit Contact"></div>
                                    </a>
                                    &nbsp;
                                    <a href="<?php echo BASE_HREF; ?>/contacts/delete/<?php echo $contact->contact_guid; ?>">
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
                                    <a href="<?php echo BASE_HREF; ?>/conversations">Return</a>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </form>
            </div>
