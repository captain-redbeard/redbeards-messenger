<?php
/**
 * @author captain-redbeard
 * @since 20/01/17
 */
namespace Redbeard\Core;

use Redbeard\Core\Config;
use Redbeard\Core\Functions;

class Router
{
    protected $controller = null;
    protected $method = null;
    protected $parameters = [];
    
    public function route($get, $post)
    {
        $this->controller = Config::get('app.path') . "Controllers\\" . Config::get('app.default_controller');
        $this->method = Config::get('app.default_method');
        
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
            $temp = Functions::cleanMethodName($url[0]);
            
            if (file_exists('../app/Controllers/' . $temp . '.php')) {
                $this->controller = Config::get('app.path') . 'Controllers\\' . $temp;
                unset($url[0]);
            }
        }
        
        return $url;
    }
    
    private function setMethod($url)
    {
        if (isset($url[1])) {
            $temp = Functions::cleanMethodName($url[1]);
            
            if (method_exists($this->controller, $temp)) {
                $reflection_method = new \ReflectionMethod($this->controller, $temp);
                if ($reflection_method->isPublic()) {
                    $this->method = $temp;
                    unset($url[1]);
                }
            }
        }
        
        return $url;
    }
}
