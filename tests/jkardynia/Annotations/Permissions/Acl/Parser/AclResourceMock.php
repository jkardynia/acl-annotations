<?php
namespace jkardynia\Annotations\Permissions\Acl\Parser;

use jkardynia\Annotations\Permissions\Acl\Acl;
/**
 * AclResourceMock
 *
 * @author Jarosław Kardynia
 */
class AclResourceMock {
    
    /**
     * @var \Zend\Permissions\Acl\Acl 
     */
    private $acl = null;
    
    public function __construct(\Zend\Permissions\Acl\Acl $acl){
        $this->acl = $acl;
    }


    /**
     * @Acl("Allow", roles="admin") 
     */
    public function doSomeAdminStuff(){
        
    }
    
    /**
     * @Acl("Deny", roles="guest") 
     */
    public function doSomeGuestStuff(){
        
    }
}
