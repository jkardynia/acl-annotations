<?php
namespace jkardynia\Annotations\Permissions\Acl\Parser;

use Doctrine\Common\Annotations\Reader;
use Zend\Permissions\Acl\Acl;
use jkardynia\Annotations\Permissions\Acl\Acl as AclAnnot;
use Zend\Permissions\Acl\Resource\GenericResource as Resource;

/**
 * AclParser
 *
 * @author Jarosław Kardynia
 */
class AclParser {
    
    /**
     * @var Reader 
     */
    private $reader = null;
    
    /**
     * @var string 
     */
    private $annotationClass = 'jkardynia\Annotations\Permissions\Acl\Acl';
    
    /**
     * @var Acl 
     */
    private $acl = null;
    
    public function __construct(Acl $acl, Reader $reader){
        $this->acl = $acl;
        $this->reader = $reader;
    }
    
    /**
     *
     * @param mixed $aclResourceObject
     * @return Acl 
     */
    public function extractAcl($aclResourceObject, $user){
        $reflectionObject = new \ReflectionObject($aclResourceObject);
        
        foreach ($reflectionObject->getMethods() as $reflectionMethod) {
            $annotation = $this->reader->getMethodAnnotation($reflectionMethod, $this->annotationClass);
            
            if (null !== $annotation) {
                $type = $this->getType($annotation);
                $roleName = "admin";//$user->getRole();
                $resourceName = $this->getResourceName($reflectionMethod);

                $this->acl->addResource($resourceName);
                $this->acl->setRule(Acl::OP_ADD, $type, $roleName, $resourceName);
            }
        }
        
        return $this->acl;
    }
    
    private function getResourceName(\ReflectionMethod $reflectionMethod){
        return "jkardynia_Annotations_Permissions_Acl_Parser_AclResourceMock::".$reflectionMethod->getName();
    }
    
    private function getType(AclAnnot $annotation){
        if($annotation->getType() === AclAnnot::ALLOW_ACCESS_TYPE_NAME){
            return Acl::TYPE_ALLOW;
        }

        return Acl::TYPE_DENY;
    }
}

