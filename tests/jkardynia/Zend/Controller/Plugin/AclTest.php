<?php
namespace jkardynia\Zend\Controller\Plugin;

require_once "ControllerMock.php";

use \Zend\Permissions\Acl\Role\GenericRole;
use \jkardynia\Zend\Controller\Exception\AccessDeniedException;
use \Zend\Mvc\MvcEvent;
use \Zend\Mvc\Controller\AbstractController;
use \jkardynia\Zend\Permissions\Acl\AclItemsCollector;

/**
 * AclTest
 *
 * @author Jarosław Kardynia
 */
class AclTest extends \PHPUnit_Framework_TestCase{
    
    /** @test */
    public function accessToActionShouldBeAllowed(){
        //todo
    }
    
    /** 
     * @test 
     * expcetedException AccessDeniedException;
     */
    public function deniedAccessRaisesException(){
        $this->markTestSkipped('Implementation of geting action name is not provided.');
        
        $acl = new \Zend\Permissions\Acl\Acl();
        $guestRole = new GenericRole("guest");
        
        $acl->addRole($guestRole);
        
        $controllerMock = new ControllerMock();
        $event = new MvcEvent('EventTest', $controllerMock);
        
        $collector = new AclItemsCollector($acl);
        $collector->addEntriesFromResourceClass(get_class($controllerMock));
        
        $plugin = new Acl($acl);
        $plugin->checkAccess($event, $guestRole);
    }
    
    /** @test */
    public function notSpecifiedAccessIsDeniedByDefault(){
        //todo
    }
}
