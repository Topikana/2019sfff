<?php

namespace AppBundle\Entity\VO;

use Doctrine\ORM\Mapping as ORM;

/**
 * VoAcknowledgmentStatements
 *
 * @ORM\Table(name="vo_acknowledgment_statements")
 * @ORM\Entity
 */
class VoAcknowledgmentStatements
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", length=10)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var int
     *
     * @ORM\Column(name="serial", type="integer", length=4)
     */
    private $serial;

    /**
     * @var string
     *
     * @ORM\Column(name="type_as", type="string", length=255, nullable=true)
     */
    private $type_as;

    /**
     * @var string
     *
     * @ORM\Column(name="grantid", type="string", length=255, nullable=true)
     */
    private $grantid;

    /**
     * @var string
     *
     * @ORM\Column(name="suggested", type="string")
     */
    private $suggested;

    /**
     * @var string
     *
     * @ORM\Column(name="relationShip", type="string")
     */
    private $relationShip;

    /**
     * @var string
     *
     * @ORM\Column(name="publicationUrl", type="string", length=255, nullable=true)
     */
    private $publicationUrl;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(targetEntity="Vo", mappedBy="AppBundle\Entity\VO_test\VoAcknowledgmentStatements")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="serial", referencedColumnName="serial")
     * })
     */
    private $Vo;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->Vo = new \Doctrine\Common\Collections\ArrayCollection();
    }


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set serial
     *
     * @param int $serial
     *
     * @return VoAcknowledgmentStatements
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
     * Set typeAs
     *
     * @param string $typeAs
     *
     * @return VoAcknowledgmentStatements
     */
    public function setTypeAs($typeAs)
    {
        $this->type_as = $typeAs;

        return $this;
    }

    /**
     * Get typeAs
     *
     * @return string
     */
    public function getTypeAs()
    {
        return $this->type_as;
    }

    /**
     * Set grantid
     *
     * @param string $grantid
     *
     * @return VoAcknowledgmentStatements
     */
    public function setGrantid($grantid)
    {
        $this->grantid = $grantid;

        return $this;
    }

    /**
     * Get grantid
     *
     * @return string
     */
    public function getGrantid()
    {
        return $this->grantid;
    }

    /**
     * Set suggested
     *
     * @param string $suggested
     *
     * @return VoAcknowledgmentStatements
     */
    public function setSuggested($suggested)
    {
        $this->suggested = $suggested;

        return $this;
    }

    /**
     * Get suggested
     *
     * @return string
     */
    public function getSuggested()
    {
        return $this->suggested;
    }

    /**
     * Set relationShip
     *
     * @param string $relationShip
     *
     * @return VoAcknowledgmentStatements
     */
    public function setRelationShip($relationShip)
    {
        $this->relationShip = $relationShip;

        return $this;
    }

    /**
     * Get relationShip
     *
     * @return string
     */
    public function getRelationShip()
    {
        return $this->relationShip;
    }

    /**
     * Set publicationUrl
     *
     * @param string $publicationUrl
     *
     * @return VoAcknowledgmentStatements
     */
    public function setPublicationUrl($publicationUrl)
    {
        $this->publicationUrl = $publicationUrl;

        return $this;
    }

    /**
     * Get publicationUrl
     *
     * @return string
     */
    public function getPublicationUrl()
    {
        return $this->publicationUrl;
    }

    /**
     * Add vo
     *
     * @param \AppBundle\Entity\VO\Vo $vo
     * @codeCoverageIgnore
     * @return VoAcknowledgmentStatements
     */
    public function addVo(\AppBundle\Entity\VO\Vo $vo)
    {
        $this->Vo[] = $vo;

        return $this;
    }

    /**
     * Remove vo
     *
     * @param \AppBundle\Entity\VO\Vo $vo
     * @codeCoverageIgnore
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeVo(\AppBundle\Entity\VO\Vo $vo)
    {
        return $this->Vo->removeElement($vo);
    }

    /**
     * Get vo
     * @codeCoverageIgnore
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getVo()
    {
        return $this->Vo;
    }
}
