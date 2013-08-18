<?php
namespace jkardynia\Annotations\Permissions\Acl;

class AclTest extends \PHPUnit_Framework_TestCase{
    
    /** @test */
    public function initializeAnnotationWithCorrectParams(){
        $aclAnnotation = new Acl(array("value" => "Allow",
            "roles" => "guest"));
        $this->assertNotNull($aclAnnotation);
    }
    
    /** 
     * @test
     * @expectedException \InvalidArgumentException
     */
    public function initializeAnnotationWithWrongParamsRisesException(){
        $aclAnnotation = new Acl(array("val" => "Allow"));
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
        $allowAnnotation = new Acl(array("value" => "WrongType", "roles" => "guest"));
    }
}

