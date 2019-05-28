<?php

namespace AppBundle\Entity\Downtime;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Subscription
 *
 * @ORM\Table(name="dt_subscription")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Downtime\SubscriptionRepository")
 */
class Subscription
{
    const I_WANT = 1;
    const I_DONT_WANT = 0;
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_active", type="boolean")
     */
    private $isActive;

    /**
     * @var string
     *
     * @ORM\Column(name="rule", type="string", length=255)
     */
    private $rule;

    /**
     * @var string
     *
     * @ORM\Column(name="region", type="string", length=255)
     */
    private $region;

    /**
     * @var string
     *
     * @ORM\Column(name="site", type="string", length=255, nullable=true)
     */
    private $site;

    /**
     * @var string
     *
     * @ORM\Column(name="node", type="string", length=255, nullable=true)
     */
    private $node;

    /**
     * @var string
     *
     * @ORM\Column(name="vo", type="string", length=255, nullable=true)
     */
    private $vo;

    /**
     * @var boolean
     *
     * @ORM\Column(name="adding", type="boolean")
     */
    private $adding;

    /**
     * @var boolean
     *
     * @ORM\Column(name="beginning", type="boolean")
     */
    private $beginning;


    /**
     * @var boolean
     *
     * @ORM\Column(name="ending", type="boolean")
     */
    private $ending;


    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Downtime\User", inversedBy="subscriptions")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=false)
     */
    private $user;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Downtime\Communication", mappedBy="subscription", cascade={"persist","remove"})
     */
    private $communications;

    /**
     * Subscription constructor.
     */
    public function __construct()
    {
        $this->communications = new ArrayCollection();
    }

    /**
     * Get id
     *
     * @codeCoverageIgnore
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set isActive
     *
     * @param bool $isActive
     *
     * @return Subscription
     */
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;

        return $this;
    }

    /**
     * Get isActive
     *
     * @return bool
     */
    public function getIsActive()
    {
        return $this->isActive;
    }

    /**
     * Set rule
     *
     * @param string $rule
     *
     * @return Subscription
     */
    public function setRule($rule)
    {
        $this->rule = $rule;

        return $this;
    }

    /**
     * Get rule
     *
     * @return string
     */
    public function getRule()
    {
        return $this->rule;
    }

    /**
     * Set region
     *
     * @param string $region
     *
     * @return Subscription
     */
    public function setRegion($region)
    {
        $this->region = $region;

        return $this;
    }

    /**
     * Get region
     *
     * @return string
     */
    public function getRegion()
    {
        return $this->region;
    }

    /**
     * Set site
     *
     * @param string $site
     *
     * @return Subscription
     */
    public function setSite($site)
    {
        $this->site = $site;

        return $this;
    }

    /**
     * Get site
     *
     * @return string
     */
    public function getSite()
    {
        return $this->site;
    }

    /**
     * Set node
     *
     * @param string $node
     *
     * @return Subscription
     */
    public function setNode($node)
    {
        $this->node = $node;

        return $this;
    }

    /**
     * Get node
     *
     * @return string
     */
    public function getNode()
    {
        return $this->node;
    }

    /**
     * Set vo
     *
     * @param string $vo
     *
     * @return Subscription
     */
    public function setVo($vo)
    {
        $this->vo = $vo;

        return $this;
    }

    /**
     * Get vo
     *
     * @return string
     */
    public function getVo()
    {
        return $this->vo;
    }

    /**
     * @return boolean
     */
    public function isAdding()
    {
        return $this->adding;
    }

    /**
     * @param boolean $adding
     */
    public function setAdding($adding)
    {
        $this->adding = $adding;
    }

    /**
     * @return boolean
     */
    public function isBeginning()
    {
        return $this->beginning;
    }

    /**
     * @param boolean $beginning
     */
    public function setBeginning($beginning)
    {
        $this->beginning = $beginning;
    }

    /**
     * @return boolean
     */
    public function isEnding()
    {
        return $this->ending;
    }

    /**
     * @param boolean $ending
     */
    public function setEnding($ending)
    {
        $this->ending = $ending;
    }

    /**
     * Get user
     *
     * @return User
     */
    public function getUser(){
        return $this->user;
    }

    /**
     * Set user
     *
     * @param User $user
     *
     * @return Subscription
     */
    public function setUser(User $user){
        $this->user = $user;
        return $this;
    }

    /**
     * Get communications
     *
     * @return ArrayCollection
     */
    public function getCommunications(){
        return $this->communications;
    }

    /**
     * Set communication
     *
     * @param ArrayCollection $communications
     *
     * @return Subscription
     */
    public function setCommunications(ArrayCollection $communications){
        $this->communications = $communications;
        return $this;
    }

    /**
     * Add communication
     *
     * @param Communication $communication
     *
     * @return Subscription
     */
    public function addCommunication(Communication $communication){
        $this->communications[] = $communication;
        $communication->setSubscription($this);
        return $this;
    }

    /**
     * Remove communication
     *
     * @param Communication $communication
     *
     * @return Subscription
     */
    public function removeCommunication(Communication $communication){
        $this->communications->removeElement($communication);
        return $this;
    }

    /**
     * Get adding
     *
     * @return boolean
     */
    public function getAdding()
    {
        return $this->adding;
    }

    /**
     * Get beginning
     *
     * @return boolean
     */
    public function getBeginning()
    {
        return $this->beginning;
    }

    /**
     * Get ending
     *
     * @return boolean
     */
    public function getEnding()
    {
        return $this->ending;
    }
}
