<?php
namespace jkardynia\Zend\Permissions\Acl;

/**
 * AclItemsCollectorTest
 *
 * @author Jarosław Kardynia
 */
class AclItemsCollectorTest extends \PHPUnit_Framework_TestCase{
    
    /** @test */
    public function adminHasAccessToHisAndGuestResources(){
        $collector = new AclItemsCollector();
        $collector->getAcl()->addRole('guest');
        $collector->getAcl()->addRole('admin', 'guest');
        $collector->addEntriesFromResourceClass('\jkardynia\Annotations\Permissions\Acl\Parser\AclResourceMock');
        
        $this->assertTrue($collector->getAcl()->isAllowed('admin', 'jkardynia\Annotations\Permissions\Acl\Parser\AclResourceMock::doSomeAdminStuff'));
        $this->assertTrue($collector->getAcl()->isAllowed('admin', 'jkardynia\Annotations\Permissions\Acl\Parser\AclResourceMock::doSomeGuestStuff'));
    }
    
    /** @test */
    public function guestHasAccessOnlyToHisResources(){
        $collector = new AclItemsCollector();
        $collector->getAcl()->addRole('guest');
        $collector->getAcl()->addRole('admin', 'guest');
        $collector->addEntriesFromResourceClass('\jkardynia\Annotations\Permissions\Acl\Parser\AclResourceMock');
        
        $this->assertFalse($collector->getAcl()->isAllowed('guest', 'jkardynia\Annotations\Permissions\Acl\Parser\AclResourceMock::doSomeAdminStuff'));
        $this->assertTrue($collector->getAcl()->isAllowed('guest', 'jkardynia\Annotations\Permissions\Acl\Parser\AclResourceMock::doSomeGuestStuff'));
    }
}
