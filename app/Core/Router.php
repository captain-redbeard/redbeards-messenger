<?php
/*
 *
 * Details:
 * PHP Messenger.
 *
 * Modified: 07-Dec-2016
 * Made Date: 04-Nov-2016
 * Author: Hosvir
 *
 */
namespace Messenger\Core;

class Router
{
    protected $controller = '\\Messenger\\Controllers\\Login';
    protected $method = 'index';
    protected $parameters = array();
    
    public function __construct()
    {
        $url = $this->parseUrl();
        $url[0] = str_replace(
            ' ',
            '',
            ucwords(
                str_replace(
                    '-',
                    ' ',
                    strtolower($url[0])
                )
            )
        );

        //Check for controller
        if (file_exists('../app/Controllers/' . $url[0] . '.php')) {
            $this->controller = '\\Messenger\\Controllers\\' . $url[0];
            unset($url[0]);
        }
        
        //New controller object
        $this->controller = new $this->controller();
        
        //Check for method
        if (isset($url[1]) && method_exists($this->controller, $url[1])) {
            $this->method = $url[1];
            unset($url[1]);
        }

        //Set parameters
        $this->parameters = $url ? array_values($url) : [];
        call_user_func_array(array($this->controller, $this->method), $this->parameters);
    }
    
    protected function parseUrl()
    {
        if (isset($_GET['url'])) {
            return $url = explode(
                '/',
                filter_var(
                    filter_var(
                        rtrim($_GET['url'], '/'),
                        FILTER_SANITIZE_URL
                    ),
                    FILTER_SANITIZE_FULL_SPECIAL_CHARS
                )
            );
        }
    }
}
