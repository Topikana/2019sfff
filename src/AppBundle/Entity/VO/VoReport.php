<?php

namespace AppBundle\Entity\VO;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * VoReport
 *
 * @ORM\Table(name="vo_report")
 * @ORM\Entity
*/
class VoReport
{
    /**
     * @var int
     *
     * @ORM\Column(name="report_id", type="integer", length=4)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $report_id;

    /**
     * @var string
     *
     * @ORM\Column(name="report_body", type="string")
     */
    private $report_body;

    /**
     * @var int
     *
     * @ORM\Column(name="serial", type="integer", length=4)
     */
    private $serial;


    /**
     *
     * @ORM\Column(name="created_at", type="datetime")
     * @Gedmo\Timestampable(on="create")
     */
    private $created_at;

    /**
     *
     * @ORM\Column(name="updated_at", type="datetime")
     * @Gedmo\Timestampable
     */
    private $updated_at;


    /**
     * Get reportId
     *
     * @return int
     */
    public function getReportId()
    {
        return $this->report_id;
    }

    /**
     * Set reportBody
     *
     * @param string $reportBody
     *
     * @return VoReport
     */
    public function setReportBody($reportBody)
    {
        $this->report_body = $reportBody;

        return $this;
    }

    /**
     * Get reportBody
     *
     * @return string
     */
    public function getReportBody()
    {
        return $this->report_body;
    }

    /**
     * Set serial
     *
     * @param int $serial
     *
     * @return VoReport
     */
    public function setSerial($serial)
    {
        $this->serial = $serial;

        return $this;
    }

    /**
     * Get serial
     *
     * @return int
     */
    public function getSerial()
    {
        return $this->serial;
    }


    /**
     * @return mixed
     */
    public function getCreatedAt()
    {
        return $this->created_at;
    }

    /**
     * @param mixed $created_at
     */
    public function setCreatedAt($created_at)
    {
        $this->created_at = $created_at;
    }

    /**
     * @return mixed
     */
    public function getUpdatedAt()
    {
        return $this->updated_at;
    }

    /**
     * @param mixed $updated_at
     */
    public function setUpdatedAt($updated_at)
    {
        $this->updated_at = $updated_at;
    }

}
