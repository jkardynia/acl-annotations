<?php
namespace jkardynia\Annotations\Permissions\Acl\Parser;

require_once "AclResourceMock.php";

use Doctrine\Common\Annotations\Reader;
use Zend\Permissions\Acl\Acl;
use Doctrine\Common\Annotations\AnnotationReader;

/**
 * AclParserTest
 *
 * @author Jarosław Kardynia
 */
class AclParserTest extends \PHPUnit_Framework_TestCase{
    
    /** @test */
    public function extractOperator(){
        
        $aclParser = new AclParser(new AnnotationReader());
        $acl = new \Zend\Permissions\Acl\Acl();
        $actualAclParams = $aclParser->extractAcl(new AclResourceMock($acl));
        $firstAclParam = $actualAclParams[0];
        
        $this->assertEquals(Acl::OP_ADD, $firstAclParam->getOperator());
    }
    
    /** @test */
    public function getProvidedPermissionType(){
        
        $aclParser = new AclParser(new AnnotationReader());
        $acl = new \Zend\Permissions\Acl\Acl();
        $actualAclParams = $aclParser->extractAcl(new AclResourceMock($acl));
        $firstAclParam = $actualAclParams[0];
        $secondAclParam = $actualAclParams[1];
        
        
        $this->assertEquals(Acl::TYPE_ALLOW, $firstAclParam->getType());
        $this->assertEquals(Acl::TYPE_ALLOW, $secondAclParam->getType());
    }
    
    /** @test */
    public function getProvidedRoles(){
        
        $aclParser = new AclParser(new AnnotationReader());
        $acl = new \Zend\Permissions\Acl\Acl();
        $actualAclParams = $aclParser->extractAcl(new AclResourceMock($acl));
        $firstAclParam = $actualAclParams[0];
        $secondAclParam = $actualAclParams[1];
        
        $this->assertEquals(array("admin"), $firstAclParam->getRoles());
        $this->assertEquals(array("guest"), $secondAclParam->getRoles());
    }
    
    /** @test */
    public function extractResourceName(){
        
        $aclParser = new AclParser(new AnnotationReader());
        $acl = new \Zend\Permissions\Acl\Acl();
        $actualAclParams = $aclParser->extractAcl(new AclResourceMock($acl));
        $firstAclParam = $actualAclParams[0];
        $secondAclParam = $actualAclParams[1];
        
        $this->assertEquals('jkardynia\Annotations\Permissions\Acl\Parser\AclResourceMock::doSomeAdminStuff', $firstAclParam->getName());
        $this->assertEquals('jkardynia\Annotations\Permissions\Acl\Parser\AclResourceMock::doSomeGuestStuff', $secondAclParam->getName());
    }
}
