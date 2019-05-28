<?php

namespace AppBundle\Entity\VO;

use Doctrine\ORM\Mapping as ORM;

/**
 * VoContactHasProfile
 *
 * @ORM\Table(name="vo_contact_has_profile")
 * @ORM\Entity
 */
class VoContactHasProfile
{
    /**
     * @var int
     *
     * @ORM\Column(name="serial", type="integer", length=4)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $serial;

    /**
     * @var int
     *
     * @ORM\Column(name="user_profile_id", type="integer", length=4)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $user_profile_id;

    /**
     * @var int
     *
     * @ORM\Column(name="contact_id", type="integer", length=4)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $contact_id;

    /**
     * @var string
     *
     * @ORM\Column(name="comment", type="string", length=500)
     */
    private $comment;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(targetEntity="Vo", mappedBy="AppBundle\Entity\VO_test\VoContactHasProfile")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="serial", referencedColumnName="serial")
     * })
     */
    private $Vo;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(targetEntity="VoUserProfile", mappedBy="AppBundle\Entity\VO_test\VoContactHasProfile")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="user_profile_id", referencedColumnName="id")
     * })
     */
    private $VoUserProfile;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(targetEntity="VoContacts", mappedBy="AppBundle\Entity\VO_test\VoContactHasProfile")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="contact_id", referencedColumnName="id")
     * })
     */
    private $VoContacts;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->Vo = new \Doctrine\Common\Collections\ArrayCollection();
        $this->VoUserProfile = new \Doctrine\Common\Collections\ArrayCollection();
        $this->VoContacts = new \Doctrine\Common\Collections\ArrayCollection();
    }


    /**
     * Set serial
     *
     * @param int $serial
     *
     * @return VoContactHasProfile
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
     * Set userProfileId
     *
     * @param int $userProfileId
     *
     * @return VoContactHasProfile
     */
    public function setUserProfileId($userProfileId)
    {
        $this->user_profile_id = $userProfileId;

        return $this;
    }

    /**
     * Get userProfileId
     *
     * @return int
     */
    public function getUserProfileId()
    {
        return $this->user_profile_id;
    }

    /**
     * Set contactId
     *
     * @param int $contactId
     *
     * @return VoContactHasProfile
     */
    public function setContactId($contactId)
    {
        $this->contact_id = $contactId;

        return $this;
    }

    /**
     * Get contactId
     *
     * @return int
     */
    public function getContactId()
    {
        return $this->contact_id;
    }

    /**
     * Set comment
     *
     * @param string $comment
     *
     * @return VoContactHasProfile
     */
    public function setComment($comment)
    {
        $this->comment = $comment;

        return $this;
    }

    /**
     * Get comment
     *
     * @return string
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * Add vo
     *
     * @param \AppBundle\Entity\VO\Vo $vo
     * @codeCoverageIgnore
     * @return VoContactHasProfile
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

    /**
     * Add voUserProfile
     *
     * @param \AppBundle\Entity\VO\VoUserProfile $voUserProfile
     * @codeCoverageIgnore
     * @return VoContactHasProfile
     */
    public function addVoUserProfile(\AppBundle\Entity\VO\VoUserProfile $voUserProfile)
    {
        $this->VoUserProfile[] = $voUserProfile;

        return $this;
    }

    /**
     * Remove voUserProfile
     *
     * @param \AppBundle\Entity\VO\VoUserProfile $voUserProfile
     * @codeCoverageIgnore
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeVoUserProfile(\AppBundle\Entity\VO\VoUserProfile $voUserProfile)
    {
        return $this->VoUserProfile->removeElement($voUserProfile);
    }

    /**
     * Get voUserProfile
     * @codeCoverageIgnore
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getVoUserProfile()
    {
        return $this->VoUserProfile;
    }

    /**
     * Add voContact
     *
     * @param \AppBundle\Entity\VO\VoContacts $voContact
     * @codeCoverageIgnore
     * @return VoContactHasProfile
     */
    public function addVoContact(\AppBundle\Entity\VO\VoContacts $voContact)
    {
        $this->VoContacts[] = $voContact;

        return $this;
    }

    /**
     * Remove voContact
     *
     * @param \AppBundle\Entity\VO\VoContacts $voContact
     * @codeCoverageIgnore
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeVoContact(\AppBundle\Entity\VO\VoContacts $voContact)
    {
        return $this->VoContacts->removeElement($voContact);
    }

    /**
     * Get voContacts
     * @codeCoverageIgnore
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getVoContacts()
    {
        return $this->VoContacts;
    }
}
