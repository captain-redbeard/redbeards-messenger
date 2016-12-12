<?php
/**
 *
 * Details:
 * PHP Messenger.
 *
 * Modified: 10-Dec-2016
 * Made Date: 04-Nov-2016
 * Author: Hosvir
 *
 */
namespace Messenger\Core;

use Messenger\Core\Functions;

class Router
{
    protected $controller = '\\Messenger\\Controllers\\Login';
    protected $method = 'index';
    protected $parameters = [];
    
    public function route($get, $post)
    {
        //Get parsed url
        $url = $this->parseUrl($get);
        
        //Set controller
        $url = $this->setController($url);
        $this->controller = new $this->controller();
        
        //Set method
        $url = $this->setMethod($url);
        
        //Set parameters
        $this->parameters = $url;
        if ($post != null) {
            array_push($this->parameters, $post);
        }
        
        //Call controller->method
        call_user_func_array([$this->controller, $this->method], $this->parameters);
    }
    
    private function parseUrl($get)
    {
        if (isset($get['url'])) {
            return explode(
                '/',
                Functions::cleanInput(
                    filter_var(
                        filter_var(
                            rtrim($get['url'], '/'),
                            FILTER_SANITIZE_URL
                        ),
                        FILTER_SANITIZE_FULL_SPECIAL_CHARS
                    ),
                    2
                )
            );
        }
        
        return [];
    }
    
    private function setController($url)
    {
        if (isset($url[0])) {
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
        }
        
        return $url;
    }
    
    private function setMethod($url)
    {
        if (isset($url[1]) && method_exists($this->controller, $url[1])) {
            $reflection_method = new \ReflectionMethod($this->controller, $url[1]);
            if ($reflection_method->isPublic()) {
                $this->method = $url[1];
                unset($url[1]);
            }
        }
        
        return $url;
    }
}
