<?php
/**
 *
 * Details:
 * PHP Messenger.
 *
 * Modified: 08-Dec-2016
 * Made Date: 04-Nov-2016
 * Author: Hosvir
 *
 */
namespace Messenger\Core;

class Router
{
    protected $controller = '\\Messenger\\Controllers\\Login';
    protected $method = 'index';
    protected $parameters = [];
    
    public function route()
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
        
        if (file_exists('../app/Controllers/' . $url[0] . '.php')) {
            $this->controller = '\\Messenger\\Controllers\\' . $url[0];
            unset($url[0]);
        }
        
        $this->controller = new $this->controller();
        
        if (isset($url[1]) && method_exists($this->controller, $url[1])) {
            $reflection_method = new \ReflectionMethod($this->controller, $url[1]);
            if ($reflection_method->isPublic()) {
                $this->method = $url[1];
                unset($url[1]);
            }
        }
        
        $this->parameters = $url ? array_values($url) : [];
        call_user_func_array(array($this->controller, $this->method), $this->parameters);
    }
    
    private function parseUrl()
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
