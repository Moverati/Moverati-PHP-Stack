<?php
namespace Core\Engine\Payment;

class Util {
    /**
     * 
     * @param Object $object The class to apply the "constructor" options
     * @param array $options Array of options to set
     */
    public static function universalConstructor($object, array $options = array()) {
        foreach($options as $key => $value) {
            $method = "set{$key}";
            
            if(method_exists($object, $method) || (method_exists($object, '__call') && property_exists($object, $key)))
                call_user_func(array($object, $method), $value);
            else
                throw new \InvalidArgumentException("Invalid property '{$key}'");
        }
    }
}