<?php
namespace jkardynia\Annotations\Permissions\Acl;

/**
 * AclTest
 *
 * @author Jarosław Kardynia
 */
class AclTest extends \PHPUnit_Framework_TestCase{
    
    /** @test */
    public function initializeAnnotationWithCorrectParams(){
        $annotation = new Acl(array("value" => "Allow",
            "roles" => "guest"));
        $this->assertNotNull($annotation);
    }
    
    /** 
     * @test
     * @expectedException \InvalidArgumentException
     */
    public function initializeAnnotationWithWrongParamsRisesException(){
        $annotation = new Acl(array("val" => "Allow"));
    }
    
    /** @test */
    public function annotationTypeIsCorrect(){
        $allowAnnotation = new Acl(array("value" => "Allow", "roles" => "guest"));
        $denyAnnotation = new Acl(array("value" => "Deny", "roles" => "guest"));
    }
    
    /** 
     * @test
     * @expectedException \InvalidArgumentException
     */
    public function wrongAnnotationTypeRisesException(){
        $annotation = new Acl(array("value" => "WrongType", "roles" => "guest"));
    }
    
    /** @test */
    public function singleRoleIsProvided(){
        $annotation = new Acl(array("value" => "Allow", "roles" => "guest"));
        
        $this->assertEquals(array("guest"), $annotation->getRoles());
    }
    
    /** @test */
    public function multipleRolesAreProvided(){
        $annotation1 = new Acl(array("value" => "Allow", "roles" => "guest,admin"));
        $annotation2 = new Acl(array("value" => "Allow", "roles" => " guest , admin   "));
        
        $this->assertEquals(array("guest", "admin"), $annotation1->getRoles());
        $this->assertEquals(array("guest", "admin"), $annotation2->getRoles());
    }
    
    /** @test */
    public function annotationReturnsProvidedType(){
        $annotation = new Acl(array("value" => "Deny", "roles" => "guest,admin"));
        
        $this->assertEquals("Deny", $annotation->getType());
    }
}

