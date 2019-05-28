<?php

namespace AppBundle\Entity\Downtime;

use Doctrine\ORM\Mapping as ORM;

/**
 * Communication
 *
 * @ORM\Table(name="dt_communication")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Downtime\CommunicationRepository")
 */
class Communication
{
    const TYPE_EMAIL_HTML = 0;
    const TYPE_EMAIL_TEXT = 1;
    const TYPE_RSS = 2;
    const TYPE_ICAL = 3;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var int
     *
     * @ORM\Column(name="type", type="integer")
     */
    private $type;

    /**
     * @var string
     *
     * @ORM\Column(name="value", type="string", length=255, nullable=true)
     */
    private $value;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Downtime\Subscription", inversedBy="communications")
     * @ORM\JoinColumn(name="subscription_id", referencedColumnName="id", nullable=false)
     */
    private $subscription;

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
     * Set type
     *
     * @param int $type
     *
     * @return Communication
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return int
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set value
     *
     * @param string $value
     *
     * @return Communication
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Get value
     *
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Get subscription
     *
     * @return Subscription
     */
    public function getSubscription(){
        return $this->subscription;
    }

    /**
     * Set subscription
     *
     * @param Subscription $subscription
     *
     * @return Communication
     */
    public function setSubscription(Subscription $subscription){
        $this->subscription = $subscription;
        return $this;
    }
}
