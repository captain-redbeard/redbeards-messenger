# php-messenger
PHP Messenger is a lightweight secure instant messaging platform designed to put you in control of your data.
We accomplish this by utilising [Public Key Encryption](https://en.wikipedia.org/wiki/Public-key_cryptography) to create a unique key pair with a 4096 bit key on registration.
See [Website Security](#website-security) for more information about our security policies.

* Security orientated
* Be in control of your data
* Only add who you want
* Lightweight at less than [10KB on conversations screen](#lightweight)
* Works without JavaScript (although we do recommend using JavaScript for less data usage)
* No personal details
* No tracking
* No publicly available data
* No one can search your account
* No spam

---

### Official Demo Website
[fromdownunder.com.au](https://fromdownunder.com.au)

* Log files deleted every 15 minutes
* [Website tests](#website-tests)

**Note: as this is a demo website, all data will be wiped regularly.**

---

### Install (Proof of concept)
**Please understand this is still in a Proof of concept stage and the code base may change drastically.**

##### Amazon Web Services - ElasticBeanstalk
Config files have been created to automate the setup of the server located under .ebextensions.
You should read over these files carefully and update them as required.

##### Stand alone
The config files under .ebextensions should be referenced for server setup.

---

### Website Security
Passwords are hashed with PHPs [password_hash](http://php.net/manual/en/function.password-hash.php) function with the PASSWORD_DEFAULT constant to allow new algorithm changes as they are added to PHP.
The login process first checks [login attempts](#brute-force-protection) then verifies the password then checks if the password needs a rehash then checks if [MFA is enabled](#multi-factor-authentication) and if enabled checks 
the MFA code and finally creates a secure session.

##### Brute Force Protection
We protect against brute force password cracking by logging failed login attempts and limiting the login attempts to 5 per hour.

##### Multi-factor Authentication 
We support virtual [Multi-factor Authentication](https://en.wikipedia.org/wiki/Multi-factor_authentication) and you can enable it under settings. **We recommend using this option.**

##### Contact Requests
Contacts are added by sharing a unique URL with the person who you want to add. Once they go to the URL and login they will be added to your contact list and you to theirs.
These URLs are unique and will expire in the set time limit or when the request is used.

##### Delete Contact
Deleting a contact will remove all conversations and messages between the two parties. **Neither party retains any history, all deletions are final.**

##### Start Conversation
A new conversation will be started with the selected contact unless an existing conversation already exists, in which case the messages will be added to that conversation.
For each message sent the program will encrypt two messages, one with your own Public Key so you can see your sent message and one with the contacts Public Key so they can see your message.

##### Delete Conversation
Deletes all messages for both parties relating to this conversation. **Neither party retains any history, all deletions are final.**

##### Delete Account
Deleting your account will delete all messages, conversations and contacts followed by your **Public and Private keys** and finally your user will be deleted. **No party retains any history, all deletions are final.**

##### Deletion Policy
Setup a deletion policy for your account based on your last login date. **No party retains any history, all deletions are final.**

---

### Lightweight
The conversations screen has been designed with low data usage in mind.
We have accomplished this by using no libraries or bloat and writing front end resources in a minimal fashion while compressing content where possible.
If you have JavaScript enabled than we use AJAX calls with server side long polling to check for new conversations and messages. With no new conversations or messages this uses around **368 Bytes** (depending on headers) per two minutes.

---

### Website tests
The following tools have been a great help in server configuration.

* [Observatory by mozilla](https://observatory.mozilla.org/analyze.html?host=fromdownunder.com.au)
* [securityheaders.io](https://securityheaders.io/?q=https%3A%2F%2Ffromdownunder.com.au&followRedirects=on)
* [Qualys SSL Labs](https://www.ssllabs.com/ssltest/analyze.html?d=fromdownunder.com.au)
* [Google PageSpeed Insights](https://developers.google.com/speed/pagespeed/insights/?url=fromdownunder.com.au)
* [Detectify](https://detectify.com)

---

### Contact

* [Email](mailto::info@fromdownunder.com.au)
