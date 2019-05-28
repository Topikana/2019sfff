<?php

namespace AppBundle\Tests\Entity\Vo;

use AppBundle\Tests\Entity\VO\Populate;

class VoTest extends \PHPUnit\Framework\TestCase
{
    private $datasource;
    /**
     * @var \AppBundle\Entity\VO\Vo
     */
    private $vo;

    /**
     * Init
     */
    public function setUp(){

        $this->datasource = Populate::datasource();
        $this->vo = Populate::createVo();
    }

    public function testInstance(){

        $this->assertEquals($this->datasource['vo.name'], $this->vo->getName());
        $this->assertEquals($this->datasource['vo.validation_date'], $this->vo->getValidationDate());
        $this->assertEquals($this->datasource['vo.creation_date'], $this->vo->getCreationDate());
        $this->assertEquals($this->datasource['vo.last_change'], $this->vo->getLastChange());
        $this->assertEquals($this->datasource['vo.header_id'], $this->vo->getHeaderId());
        $this->assertEquals($this->datasource['vo.ressources_id'], $this->vo->getRessourcesId());
        $this->assertEquals($this->datasource['vo.mailing_list_id'], $this->vo->getMailingListId());
        $this->assertEquals($this->datasource['vo.ggus_ticket_id'], $this->vo->getGgusTicketId());
        $this->assertEquals($this->datasource['vo.need_voms_help'], $this->vo->getNeedVomsHelp());
        $this->assertEquals($this->datasource['vo.need_ggus_support'], $this->vo->getNeedGgusSupport());
        $this->assertEquals($this->datasource['vo.voms_ticket_id'], $this->vo->getVomsTicketId());
        $this->assertEquals($this->datasource['vo.ggus_ticket_id_su_creation'], $this->vo->getGgusTicketIdSuCreation());
        $this->assertEquals($this->datasource['vo.monitored'], $this->vo->getMonitored());
        $this->assertEquals($this->datasource['vo.enable_team_ticket'], $this->vo->getEnableTeamTicket());

    }
}
