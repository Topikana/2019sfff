<?php

namespace AppBundle\Tests\Entity\Vo;

use AppBundle\Tests\Entity\VO\Populate;

class VoFieldTest extends \PHPUnit\Framework\TestCase
{
    private $datasource;
    /**
     * @var \AppBundle\Entity\VO\VoField
     */
    private $field;

    /**
     * Init
     */
    public function setUp(){

        $this->datasource = Populate::datasource();
        $this->field = Populate::createVoField();
    }

    public function testInstance(){
        $this->assertEquals($this->datasource['f.field_name'], $this->field->getFieldName());
        $this->assertEquals($this->datasource['f.field_error_msg'], $this->field->getFieldErrorMsg());
        $this->assertEquals($this->datasource['f.field_user_help'], $this->field->getFieldUserHelp());
        $this->assertEquals($this->datasource['f.field_admin_help'], $this->field->getFieldAdminHelp());
        $this->assertEquals($this->datasource['f.category'], $this->field->getCategory());
        $this->assertEquals($this->datasource['f.field_link'], $this->field->getFieldLink());
        $this->assertEquals($this->datasource['f.field_example'], $this->field->getFieldExample());
        $this->assertEquals($this->datasource['f.field_regexp_id'], $this->field->getFieldRegexpId());
        $this->assertEquals($this->datasource['f.required'], $this->field->getRequired());

    }
}