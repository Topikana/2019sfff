<?php

namespace AppBundle\Entity\Downtime;

use Doctrine\ORM\Mapping as ORM;

/**
 * EmailStatus
 *
 * @ORM\Table(name="email_status")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Downtime\EmailStatusRepository")
 */
class EmailStatus
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="primary_key", type="string", length=255)
     */
    private $primaryKey;

    /**
     * @var int
     *
     * @ORM\Column(name="subscription_id", type="integer")
     */
    private $subscriptionId;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255)
     */
    private $email;

    /**
     * @var bool
     *
     * @ORM\Column(name="adding_sent", type="boolean")
     */
    private $addingSent;

    /**
     * @var bool
     *
     * @ORM\Column(name="beginning_sent", type="boolean")
     */
    private $beginningSent;

    /**
     * @var bool
     *
     * @ORM\Column(name="ending_sent", type="boolean")
     */
    private $endingSent;

    /**
     * @ORM\Column(type="datetime" , nullable=true)
     */
    protected $addingSentDate;

    /**
     * @ORM\Column(type="datetime" , nullable=true)
     */
    protected $beginningSentDate;

    /**
     * @ORM\Column(type="datetime" , nullable=true)
     */
    protected $endingSentDate;


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
     * Set primaryKey
     *
     * @param string $primaryKey
     *
     * @return EmailStatus
     */
    public function setPrimaryKey($primaryKey)
    {
        $this->primaryKey = $primaryKey;

        return $this;
    }

    /**
     * Get primaryKey
     *
     * @return string
     */
    public function getPrimaryKey()
    {
        return $this->primaryKey;
    }

    /**
     * @return int
     */
    public function getSubscriptionId()
    {
        return $this->subscriptionId;
    }

    /**
     * @param int $subscriptionId
     */
    public function setSubscriptionId($subscriptionId)
    {
        $this->subscriptionId = $subscriptionId;
    }

    /**
     * Set email
     *
     * @param string $email
     *
     * @return EmailStatus
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set addingSent
     *
     * @param bool $addingSent
     *
     * @return EmailStatus
     */
    public function setAddingSent($addingSent)
    {
        $this->addingSent = $addingSent;

        return $this;
    }

    /**
     * Get addingSent
     *
     * @return bool
     */
    public function getAddingSent()
    {
        return $this->addingSent;
    }

    /**
     * Set beginningSent
     *
     * @param bool $beginningSent
     *
     * @return EmailStatus
     */
    public function setBeginningSent($beginningSent)
    {
        $this->beginningSent = $beginningSent;

        return $this;
    }

    /**
     * Get beginningSent
     *
     * @return bool
     */
    public function getBeginningSent()
    {
        return $this->beginningSent;
    }

    /**
     * Set endingSent
     *
     * @param bool $endingSent
     *
     * @return EmailStatus
     */
    public function setEndingSent($endingSent)
    {
        $this->endingSent = $endingSent;

        return $this;
    }

    /**
     * Get endingSent
     *
     * @return bool
     */
    public function getEndingSent()
    {
        return $this->endingSent;
    }

    /**
     * @return mixed
     */
    public function getAddingSentDate()
    {
        return $this->addingSentDate;
    }

    /**
     * @param mixed $addingSentDate
     */
    public function setAddingSentDate($addingSentDate)
    {
        $this->addingSentDate = $addingSentDate;
    }

    /**
     * @return mixed
     */
    public function getBeginningSentDate()
    {
        return $this->beginningSentDate;
    }

    /**
     * @param mixed $beginningSentDate
     */
    public function setBeginningSentDate($beginningSentDate)
    {
        $this->beginningSentDate = $beginningSentDate;
    }

    /**
     * @return mixed
     */
    public function getEndingSentDate()
    {
        return $this->endingSentDate;
    }

    /**
     * @param mixed $endingSentDate
     */
    public function setEndingSentDate($endingSentDate)
    {
        $this->endingSentDate = $endingSentDate;
    }


}
