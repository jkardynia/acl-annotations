<?php
namespace jkardynia\Annotations\Permissions\Acl\Parser;

use Doctrine\Common\Annotations\Reader;
use Zend\Permissions\Acl\Acl;
use jkardynia\Annotations\Permissions\Acl\Acl as AclAnnot;
use Zend\Permissions\Acl\Resource\GenericResource as Resource;
use jkardynia\Annotations\Permissions\Acl\Resource as ResourceAnnot;

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
      
    public function __construct(Reader $reader){
        $this->reader = $reader;
    }
    
    /**
     *
     * @param mixed $aclResourceObject
     * @return array 
     */
    public function extractAcl($aclResourceObject){
        $reflectionObject = new \ReflectionObject($aclResourceObject);
        $extractedAclParams = array();
        
        foreach ($reflectionObject->getMethods() as $reflectionMethod) {
            $annotation = $this->reader->getMethodAnnotation($reflectionMethod, $this->annotationClass);
            
            if (null !== $annotation) {
                $type = $this->getType($annotation);
                $rolesNames = $annotation->getRoles();
                $resourceName = $this->getResourceName($reflectionObject, $reflectionMethod);
                
                $resource = new ResourceAnnot($resourceName);
                $resource->setOperator(Acl::OP_ADD);
                $resource->setRoles($rolesNames);
                $resource->setType($type);
                
                $extractedAclParams[] = $resource;
            }
        }
        
        return $extractedAclParams;
    }
    
    private function getResourceName(\ReflectionObject $reflectionObject, \ReflectionMethod $reflectionMethod){
        return $reflectionObject->getName()."::".$reflectionMethod->getName();
    }
    
    private function getType(AclAnnot $annotation){
        if($annotation->getType() === AclAnnot::ALLOW_ACCESS_TYPE_NAME){
            return Acl::TYPE_ALLOW;
        }

        return Acl::TYPE_DENY;
    }
}

