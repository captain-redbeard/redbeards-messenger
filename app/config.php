<?php
/**
 * @author captain-redbeard
 * @since 05/02/17
 */

return [
    'app' => [
        'base_dir' =>               __DIR__,
        'timezone' =>               'UTC',
        'user_session' =>           'redbeard_user',
        'password_cost' =>          12,
        'max_login_attempts' =>     5,
        'secure_cookies' =>         true,
        'token_expire_time' =>      900,
        'path' =>                   '\\Redbeard\\',
        'default_controller' =>     'Login',
        'default_method' =>         'index',
        'conversation_max_length' => 100
    ],
    
    'database' => [
        'rdbms' =>                  'mysql',
        'hostname' =>               'localhost',
        'database' =>               'messenger',
        'username' =>               '',
        'password' =>               '',
        'charset'  =>               'utf8mb4',
    ],
    
    'site' => [
        'name' =>                   'Redbeards Messenger',
        'theme_color' =>            '4aa3df'
    ],
    
    'keys' => [
        'store_local' =>            true,
        'ppk_public_folder' =>      '/keys/public/',
        'ppk_private_folder' =>     '/keys/private/',
        's3_access_key' =>          '',
        's3_secret_key' =>          '',
        'bucket' =>                 ''
    ]
];
