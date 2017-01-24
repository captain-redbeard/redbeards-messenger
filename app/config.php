<?php
/**
 * @author captain-redbeard
 * @since 20/01/17
 */

//Database
getenv("DB_HOSTNAME") != null ? define("DB_HOSTNAME", getenv("DB_HOSTNAME")) : define("DB_HOSTNAME", "localhost");
getenv("DB_DATABASE") != null ? define("DB_DATABASE", getenv("DB_DATABASE")) : define("DB_DATABASE", "messenger");
getenv("DB_USERNAME") != null ? define("DB_USERNAME", getenv("DB_USERNAME")) : define("DB_USERNAME", "");
getenv("DB_PASSWORD") != null ? define("DB_PASSWORD", getenv("DB_PASSWORD")) : define("DB_PASSWORD", "");
getenv("DB_CHARSET") != null ? define("DB_CHARSET", getenv("DB_CHARSET")) : define("DB_CHARSET", "utf8mb4");

//Keys
define("STORE_KEYS_LOCAL", true);
define("PPK_PUBLIC_FOLDER", "/keys/public/");
define("PPK_PRIVATE_FOLDER", "/keys/private/");

//S3, if store keys local = false
getenv("S3_ACCESS_KEY") != null ? define("S3_ACCESS_KEY", getenv("S3_ACCESS_KEY")) : define("S3_ACCESS_KEY", "");
getenv("S3_SECRET_KEY") != null ? define("S3_SECRET_KEY", getenv("S3_SECRET_KEY")) : define("S3_SECRET_KEY", "");
getenv("KEY_BUCKET") != null ? define("KEY_BUCKET", getenv("KEY_BUCKET")) : define("KEY_BUCKET", "");

//App
define("BASE_DIR", __DIR__);
define("SITE_NAME", "Redbeards Messenger");
define("TIMEZONE", "Australia/Brisbane");
define("USESSION", "redbeard_user-SHfnWp8e2i7");
define("PW_COST", 12);
define("SECURE", true);
define("APP_PATH", "\\Redbeard\\");
define("DEFAULT_CONTROLLER", "Login");
define("DEFAULT_METHOD", "index");
define("MAX_LOGIN_ATTEMPTS", 5);
define("CONVERSATION_MAX_LENGTH", 100);
