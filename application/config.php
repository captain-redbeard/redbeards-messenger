<?php
/**
 * @author captain-redbeard
 * @since 05/02/17
 */

return [
    'app' => [
        'base_dir' =>                   __DIR__,
        'config_directory' =>           __DIR__ . '/Config/',
        'timezone' =>                   'UTC',
        'user_session' =>               'messenger_user',
        'password_cost' =>              12,
        'max_login_attempts' =>         5,
        'secure_cookies' =>             false,
        'token_expire_time' =>          900,
        'system_path' =>                '\\Redbeard\\Crew\\',
        'path' =>                       '\\Messenger\\',
        'default_controller' =>         'Login',
        'default_method' =>             'index',
        'conversation_max_length' =>    100,
        'time_limit' =>                 120,
    ]
];
