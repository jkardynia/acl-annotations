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
    public function extractsRolesFromResource(){
        $predefinedAcl = new Acl();
        
        $predefinedAcl->addRole("admin");
        $predefinedAcl->addRole("guest", "admin");
        
        $aclParser = new AclParser($predefinedAcl, new AnnotationReader());
        $acl = $aclParser->extractAcl(new AclResourceMock(), null);
        
        $this->assertTrue($acl->isAllowed("guest", "jkardynia_Annotations_Permissions_Acl_Parser_AclResourceMock::doSomeGuestStuf"));
        $this->assertTrue($acl->isAllowed("admin", "jkardynia_Annotations_Permissions_Acl_Parser_AclResourceMock::doSomeAdminStuf"));
    }
}

?>
