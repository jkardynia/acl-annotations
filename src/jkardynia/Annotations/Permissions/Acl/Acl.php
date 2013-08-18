<?php
namespace jkardynia\Annotations\Permissions\Acl;

/**
 * Acl annotation
 *
 * @author Jarosław Kardynia
 */
class Acl {
    
    const ALLOW_ACCESS_TYPE_NAME = "Allow";
    const DENY_ACCESS_TYPE_NAME = "Deny";
    
    private $type = "";
    
    public function __construct($params){
        
        $this->checkForRequiredParameters($params);
        $this->checkIfParamsAreCorrect($params);
        
        $this->type = $params["value"];
    }
    
    private function checkForRequiredParameters($params){
        if(false === isset($params["value"]) || 
                false === isset($params["roles"])){
            throw new \InvalidArgumentException("There is missing some required params.");
        }
    }
    
    private function checkIfParamsAreCorrect($params){
        $type = $params["value"];
        
        if(false === in_array($type, array(self::ALLOW_ACCESS_TYPE_NAME, 
            self::DENY_ACCESS_TYPE_NAME))){
            throw new \InvalidArgumentException("Wrong ACL type.");
        }
    }
}

