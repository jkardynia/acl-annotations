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
    public function accessToActionShouldBeGrantedForAllowedUser(){
        
        // given
        $acl = $this->getZendAcl();
        $mvcEvent = $this->getMvcEventMock('allowed-for-admin');
        
        $collector = new AclItemsCollector($acl);
        $collector->addEntriesFromResourceClass(get_class($mvcEvent->getTarget()));
        
        $plugin = new Acl($acl);
        $currentUserRole = new GenericRole("admin");
        
        // then
        $this->assertTrue($plugin->checkAccess($mvcEvent, $currentUserRole));
    }
    
    /** 
     * @test 
     * @expectedException \jkardynia\Zend\Controller\Exception\AccessDeniedException
     */
    public function deniedAccessRaisesException(){

        // given
        $acl = $this->getZendAcl();
        $mvcEvent = $this->getMvcEventMock('denied');
        
        $collector = new AclItemsCollector($acl);
        $collector->addEntriesFromResourceClass(get_class($mvcEvent->getTarget()));
        
        $plugin = new Acl($acl);
        $currentUserRole = new GenericRole("guest");
        
        // when
        $plugin->checkAccess($mvcEvent, $currentUserRole);
    }
    
    /** 
     * @test
     * @expectedException \Zend\Permissions\Acl\Exception\InvalidArgumentException
     */
    public function notSpecifiedAccessForActionRaisesException(){
        // given
        $acl = $this->getZendAcl();
        $mvcEvent = $this->getMvcEventMock('not-annotated');
        
        $collector = new AclItemsCollector($acl);
        $collector->addEntriesFromResourceClass(get_class($mvcEvent->getTarget()));
        
        $plugin = new Acl($acl);
        $currentUserRole = new GenericRole("guest");
        
        // when
        $plugin->checkAccess($mvcEvent, $currentUserRole);
    }
    
    /** 
     * @test 
     * @expectedException \jkardynia\Zend\Controller\Exception\AccessDeniedException
     * @expectedExceptionMessage Access for role 'guest' is denied for controller 'jkardynia\Zend\Controller\Plugin\ControllerMock::allowedForAdminAction'
     */
    public function accessAllowedToAdminIsDeniedForGuest(){
        
        // given
        $acl = $this->getZendAcl();
        $mvcEvent = $this->getMvcEventMock('allowed-for-admin');
        
        $collector = new AclItemsCollector($acl);
        $collector->addEntriesFromResourceClass(get_class($mvcEvent->getTarget()));
        
        $plugin = new Acl($acl);
        $currentUserRoleAdmin = new GenericRole("admin");
        $currentUserRoleGuest = new GenericRole("guest");
        
        // then
        $this->assertTrue($plugin->checkAccess($mvcEvent, $currentUserRoleAdmin));
        $plugin->checkAccess($mvcEvent, $currentUserRoleGuest);
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
        $acl->addRole(new GenericRole("admin", "guest"));
        
        return $acl;
    }
}
