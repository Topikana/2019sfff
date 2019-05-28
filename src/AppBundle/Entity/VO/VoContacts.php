<?php

namespace AppBundle\Entity\VO;

use Doctrine\ORM\Mapping as ORM;

/**
 * VoContacts
 *
 * @ORM\Table(name="vo_contacts")
 * @ORM\Entity
 */
class VoContacts
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", length=4)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="first_name", type="string", length=255)
     */
    private $first_name;

    /**
     * @var string
     *
     * @ORM\Column(name="last_name", type="string", length=255)
     */
    private $last_name;

    /**
     * @var string
     *
     * @ORM\Column(name="dn", type="string", length=255)
     */
    private $dn;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255)
     */
    private $email;

    /**
     * @var int
     *
     * @ORM\Column(name="grid_body", type="integer", length=1)
     */
    private $grid_body;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="VoContactHasProfile", mappedBy="AppBundle\Entity\VO\VoContacts")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id", referencedColumnName="contact_id")
     * })
     */
    private $VoContactHasProfile;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->VoContactHasProfile = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set firstName
     *
     * @param string $firstName
     *
     * @return VoContacts
     */
    public function setFirstName($firstName)
    {
        $this->first_name = $firstName;

        return $this;
    }

    /**
     * Get firstName
     *
     * @return string
     */
    public function getFirstName()
    {
        return $this->first_name;
    }

    /**
     * Set lastName
     *
     * @param string $lastName
     *
     * @return VoContacts
     */
    public function setLastName($lastName)
    {
        $this->last_name = $lastName;

        return $this;
    }

    /**
     * Get lastName
     *
     * @return string
     */
    public function getLastName()
    {
        return $this->last_name;
    }

    /**
     * Set dn
     *
     * @param string $dn
     *
     * @return VoContacts
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
     * Set email
     *
     * @param string $email
     *
     * @return VoContacts
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
     * Set gridBody
     *
     * @param int $gridBody
     *
     * @return VoContacts
     */
    public function setGridBody($gridBody)
    {
        $this->grid_body = $gridBody;

        return $this;
    }

    /**
     * Get gridBody
     *
     * @return int
     */
    public function getGridBody()
    {
        return $this->grid_body;
    }

    /**
     * Add voContactHasProfile
     *
     * @param \AppBundle\Entity\VO\VoContactHasProfile $voContactHasProfile
     * @codeCoverageIgnore
     * @return VoContacts
     */
    public function addVoContactHasProfile(\AppBundle\Entity\VO\VoContactHasProfile $voContactHasProfile)
    {
        $this->VoContactHasProfile[] = $voContactHasProfile;

        return $this;
    }

    /**
     * Remove voContactHasProfile
     *
     * @param \AppBundle\Entity\VO\VoContactHasProfile $voContactHasProfile
     * @codeCoverageIgnore
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeVoContactHasProfile(\AppBundle\Entity\VO\VoContactHasProfile $voContactHasProfile)
    {
        return $this->VoContactHasProfile->removeElement($voContactHasProfile);
    }

    /**
     * Get voContactHasProfile
     * @codeCoverageIgnore
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getVoContactHasProfile()
    {
        return $this->VoContactHasProfile;
    }
}
