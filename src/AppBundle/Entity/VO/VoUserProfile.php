<?php

namespace AppBundle\Entity\VO;

use Doctrine\ORM\Mapping as ORM;

/**
 * VoUserProfile
 *
 * @ORM\Table(name="vo_user_profile")
 * @ORM\Entity
 */
class VoUserProfile
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
     * @ORM\Column(name="profile", type="string", length=100)
     */
    private $profile;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=255)
     */
    private $description;

    /**
     * @var string
     *
     * @ORM\Column(name="help_msg", type="string", length=1000)
     */
    private $help_msg;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="VoContactHasProfile", mappedBy="AppBundle\Entity\VO_test\VoUserProfile")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id", referencedColumnName="user_profile_id")
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
     * Set profile
     *
     * @param string $profile
     *
     * @return VoUserProfile
     */
    public function setProfile($profile)
    {
        $this->profile = $profile;

        return $this;
    }

    /**
     * Get profile
     *
     * @return string
     */
    public function getProfile()
    {
        return $this->profile;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return VoUserProfile
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set helpMsg
     *
     * @param string $helpMsg
     *
     * @return VoUserProfile
     */
    public function setHelpMsg($helpMsg)
    {
        $this->help_msg = $helpMsg;

        return $this;
    }

    /**
     * Get helpMsg
     *
     * @return string
     */
    public function getHelpMsg()
    {
        return $this->help_msg;
    }

    /**
     * Add voContactHasProfile
     *
     * @param \AppBundle\Entity\VO\VoContactHasProfile $voContactHasProfile
     * @codeCoverageIgnore
     * @return VoUserProfile
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
