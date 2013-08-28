<?php
namespace jkardynia\Annotations\Permissions\Acl;

/**
 * Resource
 *
 * @author Jarosław Kardynia
 */
class Resource {
    
    /**
     * @var string 
     */
    private $name = '';
    
    /**
     * @var string 
     */
    private $operator = '';
    
    /**
     * @var string 
     */
    private $type = '';
    
    /**
     * @var array<string> 
     */
    private $roles = array();
    
    public function __construct($resourceName){
        $this->name = $resourceName;
    }
    
    /**
     * @return string 
     */
    public function getName(){
        return $this->name;
    }
    
    /**
     * @return string 
     */
    public function getOperator(){
        return $this->operator;
    }
    
    /**
     * @param string $operator
     */
    public function setOperator($operator){
        $this->operator = $operator;
    }
    
    /**
     * @return string 
     */
    public function getType(){
        return $this->type;
    }
    
    /**
     * @param string $type
     */
    public function setType($type){
        $this->type = $type;
    }
    
    /**
     * @return array 
     */
    public function getRoles(){
        return $this->roles;
    }
    
    /**
     * @param array $roles
     */
    public function setRoles($roles){
        $this->roles = $roles;
    }
}
