            <div class="cover-wrapper">
                <form>
                    <table class="multi-table">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Value</th>
                            </tr>
                        </thead>

                        <tbody class="text-center">
                            <tr>
                                <td>Users</td>
                                <td><strong><?php echo $data['user_count']; ?></strong></td>
                            </tr>

                            <tr>
                                <td>Conversations</td>
                                <td><strong><?php echo number_format($data['conversation_count'] > 1 ? ($data['conversation_count'] / 2) : $data['conversation_count']); ?></strong></td>
                            </tr>

                            <tr>
                                <td>Messages</td>
                                <td><strong><?php echo number_format($data['message_count'] > 1 ? ($data['message_count'] / 2) : $data['message_count']); ?></strong></td>
                            </tr>
                        </tbody>

                        <tfoot>
                        </tfoot>
                    </table>
                </form>
            </div>
