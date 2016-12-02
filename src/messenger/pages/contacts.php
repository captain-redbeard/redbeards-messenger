<?php
/**
 * 
 * Details:
 * PHP Messenger.
 * 
 * Modified: 02-Dec-2016
 * Made Date: 27-Nov-2016
 * Author: Hosvir
 * 
 * */
include(dirname(__FILE__) . "/../includes/login_auth.inc.php");

//Get contacts
$contacts = QB::select("SELECT contact_guid, contact_alias, made_date, (SELECT username FROM users WHERE user_guid = contact_guid) AS username FROM contacts WHERE user_guid = ?;",
						array($_SESSION[USESSION]->user_guid),
						$mysqli);
?>

			<div class="cover-wrapper">				
				<form method="POST" action="">
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
							<?php foreach($contacts as $contact) { ?>
								
							<tr>
								<td><a href="conversations/new/<?php echo $contact['contact_guid']; ?>"><?php echo $contact['contact_alias']; ?></a></td>
								<td><a href="conversations/new/<?php echo $contact['contact_guid']; ?>"><?php echo $contact['username']; ?></a></td>
								<td class="text-right"><?php echo convert_time($contact['made_date']); ?></td>
								<td>
									<a href="edit-contact/<?php echo $contact['contact_guid']; ?>">
										<div class="grow ie" alt="Edit" title="Edit Contact"></div>
									</a>
									&nbsp;
									<a href="delete-contact/<?php echo $contact['contact_guid']; ?>">
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
									<a href="conversations">Return</a>
								</td>
							</tr>
						</tfoot>
					</table>
				</form>
			</div>
