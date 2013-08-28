<?php
namespace jkardynia\Annotations\Permissions\Acl;

/**
 * ResourceTest
 *
 * @author Jarosław Kardynia
 */
class ResourceTest  extends \PHPUnit_Framework_TestCase{
    /** @test */
    public function checkName(){
        $resource = new Resource('resourceName');
        
        $this->assertEquals('resourceName', $resource->getName());
    }
}
