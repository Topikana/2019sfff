<?php

namespace AppBundle\Entity\Downtime;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * User
 *
 * @ORM\Table(name="dt_user")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Downtime\UserRepository")
 */
class User
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
     * @var array
     *
     * @ORM\Column(name="roles", type="array",  nullable=true)
     */
    private $roles;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=true)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255, nullable=true)
     */
    private $email;



    /**
     * @var string
     *
     * @ORM\Column(name="dn", type="string", length=255)
     */
    private $dn;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Downtime\Subscription", mappedBy="user", cascade={"persist", "remove"})
     */
    private $subscriptions;

    /**
     * User constructor.
     * @param $username
     * @param array $roles
     */
    public function __construct($username, array $roles)
    {
        $this->dn = $username;
        $this->roles = $roles;

        $this->subscriptions = new ArrayCollection();
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
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }


    /**
     * Set email
     *
     * @param string $email
     *
     * @return User
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
     * Set dn
     *
     * @param string $dn
     *
     * @return User
     */
    public function setDn($dn)
    {
        $this->dn = $dn;

        return $this;
    }

    /**
     * Get dn
     *
     * @return string
     */
    public function getDn()
    {
        return $this->dn;
    }

    /**
     * Get roles
     *
     * @codeCoverageIgnore
     * @return array
     */
    public function getRoles()
    {
        return $this->roles;
    }


    /**
     * Get subscription
     *
     * @return ArrayCollection
     */
    public function getSubscriptions(){
        return $this->subscriptions;
    }

    /**
     * Set subscriptions
     *
     * @param ArrayCollection $subscriptions
     *
     * @return User
     */
    public function setSubscriptions(ArrayCollection $subscriptions){
        $this->subscriptions = $subscriptions;
        foreach ($subscriptions as $subscription) {
            $subscription->setUser($this);
        }
        return $this;
    }

    /**
     * Add subscription
     *
     * @param Subscription $subscription
     *
     * @return User
     */
    public function addSubscription(Subscription $subscription){
        $this->subscriptions[] = $subscription;
        $subscription->setUser($this);
        return $this;
    }

    /**
     * Remove subscription
     *
     * @param Subscription $subscription
     *
     * @return User
     */
    public function removeSubscription(Subscription $subscription){
        $this->subscriptions->removeElement($subscription);
        return $this;
    }

    /**
     * Set roles
     *
     * @param array $roles
     *
     * @return User
     */
    public function setRoles($roles)
    {
        $this->roles = $roles;

        return $this;
    }
}
