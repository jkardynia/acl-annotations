<?php
namespace jkardynia\Zend\Controller\Plugin;

use \Zend\Mvc\Controller\Plugin\AbstractPlugin;
use \Zend\Permissions\Acl\Role\GenericRole;
use \jkardynia\Zend\Controller\Exception\AccessDeniedException;
use \Zend\Permissions\Acl\Role\RoleInterface;
use \Zend\Mvc\MvcEvent;
use \Zend\Mvc\Controller\AbstractController;
use \Zend\Config\Reader\ReaderInterface;

/**
 * Acl plugin
 *
 * @author Jarosław Kardynia
 * 
 */
class Acl extends AbstractPlugin{
    
    /**
     * @var \Zend\Permissions\Acl\Acl 
     */
    private $zendAcl = null;
    
    public function __construct(\Zend\Permissions\Acl\Acl $acl){
        
        $this->zendAcl = $acl;
    }
    
    public function checkAccess(MvcEvent $event, RoleInterface $userRole){
        
        $resourceName = $this->getResourceName($event);

        if(false === $this->zendAcl->isAllowed($userRole, $resourceName)){
            throw new AccessDeniedException("Access for role '".$userRole->getRoleId()."' is denied for controller '". $resourceName ."'.");
        }
        return true;
    }
    
    private function getResourceName(MvcEvent $event){

        $controller = $event->getTarget();
        $routeMatch = $event->getRouteMatch();
        
        if ($routeMatch) {
            $action = $routeMatch->getParam('action', 'not-found');
            $methodName = \Zend\Mvc\Controller\AbstractController::getMethodFromAction($action);

            return get_class($controller).'::'.$methodName;
        }
        
        return '';
    }
    
    /**
     * @return \Zend\Permissions\Acl\Acl 
     */
    public function getAcl(){
        return $this->zendAcl;
    }
}
