<?php
namespace jkardynia\Zend\Controller\Plugin;

use \Zend\Mvc\Controller\AbstractActionController;
use \jkardynia\Annotations\Permissions\Acl\Acl;

/**
 * ControllerMock
 *
 * @author Jarosław Kardynia
 */
class ControllerMock {//extends AbstractActionController{

    /**
     * @Acl("Deny", roles="guest") 
     */
    public function annotationTestAction(){
        // do something good
    }
}
