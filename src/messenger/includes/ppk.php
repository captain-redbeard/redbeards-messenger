<?php
/**
 * Details:
 * This class is to provide static methods to easily use Public Private 
 * key encryption / decryption.
 * 
 * Modified: 28-Nov-2016
 * Date: 12-Nov-2016
 * Author: Hosvir
 * 
 * */
class PPK {
	
	/**
	 * Generate a public private key pair.
	 * NOTE: Supply folder/keyname
	 * 
	 * @usage: PPK::generateKeyPair("mypublickey","mypriavtekey");
	 * 
	 * @returns: Boolean
	 * */
	public static function generateKeyPair($publicName,$privateName,$returnKeys = false, $passphrase = null, $keyBits = 4096){
		$privateKey = openssl_pkey_new(array(
			'private_key_bits' => $keyBits,  
			'private_key_type' => OPENSSL_KEYTYPE_RSA, 
			'encrypted' => true
		));
		
		//Save the private key to private.key file. Never share this file with anyone.
		if(!$returnKeys){
			openssl_pkey_export_to_file($privateKey, $privateName . ".key", $passphrase);
		}else{
			openssl_pkey_export($privateKey, $pKeyOut, $passphrase);
		}
		 
		//Generate the public key for the private key
		$a_key = openssl_pkey_get_details($privateKey);
		
		//Save the public key in public.key file. Send this file to anyone who want to send you the encrypted data.
		if(!$returnKeys) file_put_contents($publicName . ".pem", $a_key['key']);
		 
		//Free the private Key.
		openssl_free_key($privateKey);
		
		if(!$returnKeys){
			return self::testKeys($publicName, $privateName, $passphrase);
		}else{
			return array($a_key['key'], $pKeyOut);
		}
	}
	
	/**
	 * Encrypt the data with the supplied public key.
	 * NOTE: Supply folder/keyname
	 * 
	 * @usage: PPK::encrypt("some data", "mypublickey");
	 * 
	 * @returns: Encrypted data
	 * */
	public static function encrypt($plaintext, $publicName, $pem = null){
		//Compress the data to be sent
		$plaintext = gzcompress($plaintext);
		 
		//Get the public Key of the recipient
		if($pem == null){
			$publicKey = openssl_pkey_get_public(file_get_contents($publicName . ".pem"));
		}else{
			$publicKey = openssl_pkey_get_public($pem);
		}
		$a_key = openssl_pkey_get_details($publicKey);
		 
		//Encrypt the data in small chunks and then combine and send it.
		$chunkSize = ceil($a_key['bits'] / 8) - 11;
		$output = "";
		 
		while($plaintext){
			$chunk = substr($plaintext, 0, $chunkSize);
			$plaintext = substr($plaintext, $chunkSize);
			$encrypted = "";
			if(!openssl_public_encrypt($chunk, $encrypted, $publicKey)) die('Failed to encrypt data.');
			$output .= $encrypted;
		}
		
		//Free the key
		openssl_free_key($publicKey);
		
		return $output;
	}
	
	/**
	 * Decrypt the data with the supplied private key.
	 * NOTE: Supply folder/keyname
	 * 
	 * @usage: PPK::encrypt("encrypteddata", "myprivatekey");
	 * 
	 * @returns: Decrypted data
	 * */
	public static function decrypt($encrypted, $privateName, $passphrase = null, $key = null){
		if($key == null){
			if(!$privateKey = openssl_pkey_get_private(file_get_contents($privateName . ".key"), $passphrase)) die('Private Key failed, check your passphrase.');
		}else{
			if(!$privateKey = openssl_pkey_get_private($key, $passphrase)) die('Private Key failed, check your passphrase.');
		}
		$a_key = openssl_pkey_get_details($privateKey);
		 
		//Decrypt the data in the small chunks
		$chunkSize = ceil($a_key['bits'] / 8);
		$output = "";
		 
		while($encrypted){
			$chunk = substr($encrypted, 0, $chunkSize);
			$encrypted = substr($encrypted, $chunkSize);
			$decrypted = "";
			if(!openssl_private_decrypt($chunk, $decrypted, $privateKey)) die('Failed to decrypt data.');
			$output .= $decrypted;
		}
		
		//Free the key
		openssl_free_key($privateKey);
		 
		//Uncompress the unencrypted data.
		$output = gzuncompress($output);
		
		return $output;
	}
	
	/**
	 * Internal test to ensure the keys work.
	 * 
	 * @returns: Boolean
	 * */
	private static function testKeys($publicName, $privateName, $passphrase = null) {
		$raw = "Hi there, my name is slim shady.";
		$encrypted = self::encrypt($raw, $publicName);
		$decrypted = self::decrypt($encrypted, $privateName, $passphrase);
		
		return ($raw == $decrypted);
	}
}
?>
