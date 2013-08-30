<?php
namespace jkardynia\Zend\Controller\Plugin;

require_once "ControllerMock.php";

use \Zend\Permissions\Acl\Role\GenericRole;
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
        
        // given
        $acl = $this->getZendAcl();
        $mvcEvent = $this->getMvcEventMock('annotation-test2');
        
        $collector = new AclItemsCollector($acl);
        $collector->addEntriesFromResourceClass(get_class($mvcEvent->getTarget()));
        
        $plugin = new Acl($acl);
        $currentUserRole = new GenericRole("admin");
        
        // when
        $plugin->checkAccess($mvcEvent, $currentUserRole);
    }
    
    /** 
     * @test 
     * @expectedException \jkardynia\Zend\Controller\Exception\AccessDeniedException
     */
    public function deniedAccessRaisesException(){

        // given
        $acl = $this->getZendAcl();
        $mvcEvent = $this->getMvcEventMock('annotation-test1');
        
        $collector = new AclItemsCollector($acl);
        $collector->addEntriesFromResourceClass(get_class($mvcEvent->getTarget()));
        
        $plugin = new Acl($acl);
        $currentUserRole = new GenericRole("guest");
        
        // when
        $plugin->checkAccess($mvcEvent, $currentUserRole);
    }
    
    /** @test */
    public function notSpecifiedAccessIsDeniedByDefault(){
        //todo
    }
    
    /**
     * @return \Zend\Mvc\MvcEvent 
     */
    private function getMvcEventMock($actionName){
        $controllerMock = new ControllerMock();
        $event = new MvcEvent('EventTest', $controllerMock);

        $routeMatch = new \Zend\Mvc\Router\RouteMatch(array('action' => $actionName));
        $event->setRouteMatch($routeMatch);
        
        return $event;
    }
    
    /**
     * @return \Zend\Permissions\Acl\Acl 
     */
    private function getZendAcl(){
        $acl = new \Zend\Permissions\Acl\Acl();
        
        $acl->addRole(new GenericRole("guest"));
        $acl->addRole(new GenericRole("admin"));
        
        return $acl;
    }
}
