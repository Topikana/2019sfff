<?php

namespace AppBundle\Entity\Broadcast;

use Doctrine\ORM\Mapping as ORM;

/**
 * Broadcast
 *
 * @ORM\Table(name="broadcast_mailing_lists")
 * @ORM\Entity
 */
class BroadcastMailingLists
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", length=4)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=150)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="value", type="string", length=250)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $value;

    /**
     * @var string
     *
     * @ORM\Column(name="user_id", type="string", length=255)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $user_id;

    /**
     * @var string
     *
     * @ORM\Column(name="scope", type="string", length=100)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $scope;



    /**
     * Set id
     *
     * @param int $id
     * @codeCoverageIgnore
     * @return BroadcastMailingLists
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get id
     * @codeCoverageIgnore
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return BroadcastMailingLists
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set value
     *
     * @param string $value
     *
     * @return BroadcastMailingLists
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
     * Set userId
     *
     * @param string $userId
     *
     * @return BroadcastMailingLists
     */
    public function setUserId($userId)
    {
        $this->user_id = $userId;

        return $this;
    }

    /**
     * Get userId
     *
     * @return string
     */
    public function getUserId()
    {
        return $this->user_id;
    }

    /**
     * Set scope
     *
     * @param string $scope
     *
     * @return BroadcastMailingLists
     */
    public function setScope($scope)
    {
        $this->scope = $scope;

        return $this;
    }

    /**
     * Get scope
     *
     * @return string
     */
    public function getScope()
    {
        return $this->scope;
    }
}
