<?php

namespace AppBundle\Tests\Entity\Vo;

use AppBundle\Tests\Entity\VO\Populate;

class VoHeaderTest extends \PHPUnit\Framework\TestCase
{
    private $datasource;
    /**
     * @var \AppBundle\Entity\VO\VoHeader
     */
    private $voHeader;

    /**
     * Init
     */
    public function setUp(){

        $this->datasource = Populate::datasource();
        $this->voHeader = Populate::createVoHeader();
    }

    public function testInstance(){
        $this->assertEquals(null, $this->voHeader->getId());
        $this->assertEquals($this->datasource['vh.name'], $this->voHeader->getName());
        $this->assertEquals($this->datasource['vh.alias'], $this->voHeader->getAlias());
        $this->assertEquals($this->datasource['vh.grid_id'], $this->voHeader->getGridId());
        $this->assertEquals($this->datasource['vh.serial'], $this->voHeader->getSerial());
        $this->assertEquals($this->datasource['vh.enrollment_url'], $this->voHeader->getEnrollmentUrl());
        $this->assertEquals($this->datasource['vh.homepage_url'], $this->voHeader->getHomepageUrl());
        $this->assertEquals($this->datasource['vh.support_procedure_url'], $this->voHeader->getSupportProcedureUrl());
        $this->assertEquals($this->datasource['vh.aup'], $this->voHeader->getAup());
        $this->assertEquals($this->datasource['vh.aup_type'], $this->voHeader->getAupType());
        $this->assertEquals($this->datasource['vh.description'], $this->voHeader->getDescription());
        $this->assertEquals($this->datasource['vh.arc_supported'], $this->voHeader->getArcSupported());
        $this->assertEquals($this->datasource['vh.glite_supported'], $this->voHeader->getGliteSupported());
        $this->assertEquals($this->datasource['vh.unicore_supported'], $this->voHeader->getUnicoreSupported());
        $this->assertEquals($this->datasource['vh.globus_supported'], $this->voHeader->getGlobusSupported());
        $this->assertEquals($this->datasource['vh.cloud_computing_supported'], $this->voHeader->getCloudComputingSupported());
        $this->assertEquals($this->datasource['vh.cloud_storage_supported'], $this->voHeader->getCloudStorageSupported());
        $this->assertEquals($this->datasource['vh.desktop_grid_supported'], $this->voHeader->getDesktopGridSupported());
        $this->assertEquals($this->datasource['vh.validation_date'], $this->voHeader->getValidationDate());
        $this->assertEquals($this->datasource['vh.discipline_id'], $this->voHeader->getDisciplineId());
        $this->assertEquals($this->datasource['vh.scope_id'], $this->voHeader->getScopeId());
        $this->assertEquals($this->datasource['vh.status_id'], $this->voHeader->getStatusId());
        $this->assertEquals($this->datasource['vh.insert_date'], $this->voHeader->getInsertDate());
        $this->assertEquals($this->datasource['vh.validated'], $this->voHeader->getValidated());
        $this->assertEquals($this->datasource['vh.reject_reason'], $this->voHeader->getRejectReason());
        $this->assertEquals($this->datasource['vh.notify_sites'], $this->voHeader->getNotifySites());

    }
}
