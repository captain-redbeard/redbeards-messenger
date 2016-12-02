<?php
/**
 * Details:
 * PHP Messenger.
 * 
 * Modified: 02-Dec-2016
 * Date: 02-Dec-2016
 * Author: Hosvir
 * 
 * */
class Conversation {
	
	/**
	 * 
	 * Send message.
	 * 
	 * Details:
	 * Gets both parties public keys by GUID, encrypts the message and adds a record to both users.
	 * If no conversation has been provided it will create a new one.
	 * 
	 * @param: $tguid 	- to guid
	 * @param: $cguid 	- conversation guid
	 * @param: $message - message to send
	 * @param: $mysqli	- MySQL connection
	 * 
	 * @returns: result code
	 * 
	 * */
	public static function sendMessage($tguid, $cguid, $message, $mysqli) {
		$message = clean_input($message, 0);
		$cguid = $cguid != null ? $cguid : generateRandomString(32);
		
		//Contact set
		if($tguid != null) {
			include(dirname(__FILE__) . "/../includes/ppk.php");
			
			if(!STORE_KEYS_LOCAL) {
				include(dirname(__FILE__) . "/../thirdparty/S3.php");
				S3::setAuth(S3_ACCESS_KEY, S3_SECRET_KEY);
				$tpubkey = S3::getObject(KEY_BUCKET, $tguid . ".pem");
				$upubkey = S3::getObject(KEY_BUCKET, $_SESSION[USESSION]->user_guid . ".pem");
			}else{
				$tpubkey = file_get_contents(dirname(__FILE__) . "/../keys/public/" . $tguid . ".pem");
				$upubkey = file_get_contents(dirname(__FILE__) . "/../keys/public/" . $_SESSION[USESSION]->user_guid . ".pem");
			}
			
			//Encrypt
			$tmessage = PPK::encrypt($message, null, STORE_KEYS_LOCAL ? $tpubkey : $tpubkey->body);
			$umessage = PPK::encrypt($message, null, STORE_KEYS_LOCAL ? $upubkey : $upubkey->body);
			
			//Get conversation
			$conv = QB::select("SELECT conversation_guid FROM conversations WHERE user_guid = ? AND contact_guid = ?;", array($_SESSION[USESSION]->user_guid, $tguid), $mysqli);
			if(count($conv) < 1) {
				//Add new conversations
				QB::insert("INSERT INTO conversations (conversation_guid, contact_guid, user_guid) VALUES (?,?,?);", array($cguid, $_SESSION[USESSION]->user_guid, $tguid), $mysqli);
				QB::insert("INSERT INTO conversations (conversation_guid, contact_guid, user_guid) VALUES (?,?,?);", array($cguid, $tguid, $_SESSION[USESSION]->user_guid), $mysqli);
			}else if($cguid != $conv[0]['conversation_guid']) {
				$cguid = $conv[0]['conversation_guid'];
			}
			
			//Insert
			QB::insert("INSERT INTO messages (conversation_guid, user1_guid, user2_guid, direction, message) VALUES (?,?,?,?,?);", array($cguid, $tguid, $_SESSION[USESSION]->user_guid, 0, $tmessage), $mysqli);
			QB::insert("INSERT INTO messages (conversation_guid, user1_guid, user2_guid, direction, message) VALUES (?,?,?,?,?);", array($cguid, $tguid, $_SESSION[USESSION]->user_guid, 1, $umessage), $mysqli);

			return 0 . ":" . $cguid;
		}else {
			return 1;
		}
	}
	
	/**
	 * 
	 * Get messages.
	 * 
	 * Details:
	 * Get messages for the current session user from the specified conversation.
	 * 
	 * @param: $cguid 		- conversation guid
	 * @param: $mysqli		- MySQL connection
	 * @param: $madedate	- Made date grater than this
	 * 
	 * @returns: Decrypted message array
	 * 
	 * */
	public static function getMessages($cguid, $mysqli, $madedate = null) { 
		//Get messages		
		if($madedate == null) {
			$messages = QB::select("SELECT user1_guid, user2_guid, direction, message, made_date FROM messages WHERE (user1_guid = ? AND direction = 0 OR user2_guid = ? AND direction = 1) AND conversation_guid = ? ORDER BY made_date DESC LIMIT ?;",
									array($_SESSION[USESSION]->user_guid, $_SESSION[USESSION]->user_guid, $cguid, CONVERSATION_MAX_LENGTH),
									$mysqli);
		}else {
			$messages = QB::select("SELECT user1_guid, user2_guid, direction, message, made_date FROM messages WHERE (user1_guid = ? AND direction = 0 OR user2_guid = ? AND direction = 1) AND conversation_guid = ? AND made_date > ? ORDER BY made_date DESC LIMIT ?;",
									array($_SESSION[USESSION]->user_guid, $_SESSION[USESSION]->user_guid, $cguid, $madedate, CONVERSATION_MAX_LENGTH),
									$mysqli);
		}
		
		//Get private key	
		include_once(dirname(__FILE__) . "/../includes/ppk.php");	

		if(!STORE_KEYS_LOCAL) {
			include_once(dirname(__FILE__) . "/../thirdparty/S3.php");
			S3::setAuth(S3_ACCESS_KEY, S3_SECRET_KEY);
			$uprvkey = S3::getObject(KEY_BUCKET, $_SESSION[USESSION]->user_guid . ".key");
		}else{
			$uprvkey = file_get_contents(dirname(__FILE__) . "/../keys/private/" . $_SESSION[USESSION]->user_guid . ".key");
		}
		
		//Decrypt messages
		for($i = 0; $i < count($messages); $i++) {
			$messages[$i]['message'] = str_replace("---newline---", "<br/>", PPK::decrypt($messages[$i]['message'], null, $_SESSION[USESSION]->passphrase, STORE_KEYS_LOCAL ? $uprvkey : $uprvkey->body));
		}
		
		//Retrun decrypted message array
		return array_reverse($messages);
	}
	
	/**
	 * 
	 * Get conversations.
	 * 
	 * Details:
	 * Get all conversations for the current session user.
	 * 
	 * @param: $mysqli	- MySQL connection
	 * @param: $madedate	- Made date grater than this
	 * 
	 * @returns: Conversations array
	 * 
	 * */
	public static function getConversations($mysqli, $madedate = null) {
		//Get conversations
		if($madedate == null) {
			$conversations = QB::select("SELECT conversation_guid, contact_guid, 
									(SELECT username FROM users WHERE user_guid = contact_guid) AS username, 
									(SELECT contact_alias FROM contacts WHERE contact_guid = conversations.contact_guid AND user_guid = conversations.user_guid) AS contact_alias, 
									(SELECT made_date FROM messages WHERE conversation_guid = conversations.conversation_guid AND (user1_guid = ? OR user2_guid = ?) ORDER BY made_date DESC LIMIT 1) AS made_date
									
									FROM conversations 
									
									WHERE user_guid = ?
									
									ORDER BY made_date DESC;", 
									array($_SESSION[USESSION]->user_guid, $_SESSION[USESSION]->user_guid, $_SESSION[USESSION]->user_guid), 
									$mysqli);
		}else {
			$conversations = QB::select("SELECT conversation_guid, contact_guid, 
									(SELECT username FROM users WHERE user_guid = contact_guid) AS username, 
									(SELECT contact_alias FROM contacts WHERE contact_guid = conversations.contact_guid AND user_guid = conversations.user_guid) AS contact_alias, 
									(SELECT made_date FROM messages WHERE conversation_guid = conversations.conversation_guid AND (user1_guid = ? OR user2_guid = ?) ORDER BY made_date DESC LIMIT 1) AS made_date
									
									FROM conversations 
									
									WHERE user_guid = ?
									AND made_date > ? 
									
									ORDER BY made_date DESC;", 
									array($_SESSION[USESSION]->user_guid, $_SESSION[USESSION]->user_guid, $_SESSION[USESSION]->user_guid, $madedate), 
									$mysqli);
		}
								
		//Return conversation array
		return $conversations;
	}
	
}
?>
