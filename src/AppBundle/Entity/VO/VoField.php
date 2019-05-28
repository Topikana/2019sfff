<?php

namespace AppBundle\Entity\VO;

use Doctrine\ORM\Mapping as ORM;

/**
 * VoField
 *
 * @ORM\Table(name="vo_field")
 * @ORM\Entity
 */
class VoField
{
    /**
     * @var string
     *
     * @ORM\Column(name="field_name", type="string", length=255)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $field_name;

    /**
     * @var string
     *
     * @ORM\Column(name="field_error_msg", type="string", length=2000)
     */
    private $field_error_msg;

    /**
     * @var string
     *
     * @ORM\Column(name="field_user_help", type="string", length=2000)
     */
    private $field_user_help;

    /**
     * @var string
     *
     * @ORM\Column(name="field_admin_help", type="string", length=2000)
     */
    private $field_admin_help;

    /**
     * @var string
     *
     * @ORM\Column(name="category", type="string", length=2000)
     */
    private $category;

    /**
     * @var string
     *
     * @ORM\Column(name="field_link", type="string", length=255)
     */
    private $field_link;

    /**
     * @var string
     *
     * @ORM\Column(name="field_example", type="string", length=255)
     */
    private $field_example;

    /**
     * @var int
     *
     * @ORM\Column(name="field_regexp_id", type="integer", length=4)
     */
    private $field_regexp_id;

    /**
     * @var int
     *
     * @ORM\Column(name="required", type="integer", length=1)
     */
    private $required;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(targetEntity="VoRegexp", mappedBy="AppBundle\Entity\VO\VoField")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="field_regexp_id", referencedColumnName="id")
     * })
     */
    private $VoRegexp;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->VoRegexp = new \Doctrine\Common\Collections\ArrayCollection();
    }


    /**
     * Set fieldName
     *
     * @param string $fieldName
     *
     * @return VoField
     */
    public function setFieldName($fieldName)
    {
        $this->field_name = $fieldName;

        return $this;
    }

    /**
     * Get fieldName
     *
     * @return string
     */
    public function getFieldName()
    {
        return $this->field_name;
    }

    /**
     * Set fieldErrorMsg
     *
     * @param string $fieldErrorMsg
     *
     * @return VoField
     */
    public function setFieldErrorMsg($fieldErrorMsg)
    {
        $this->field_error_msg = $fieldErrorMsg;

        return $this;
    }

    /**
     * Get fieldErrorMsg
     *
     * @return string
     */
    public function getFieldErrorMsg()
    {
        return $this->field_error_msg;
    }

    /**
     * Set fieldUserHelp
     *
     * @param string $fieldUserHelp
     *
     * @return VoField
     */
    public function setFieldUserHelp($fieldUserHelp)
    {
        $this->field_user_help = $fieldUserHelp;

        return $this;
    }

    /**
     * Get fieldUserHelp
     *
     * @return string
     */
    public function getFieldUserHelp()
    {
        return $this->field_user_help;
    }

    /**
     * Set fieldAdminHelp
     *
     * @param string $fieldAdminHelp
     *
     * @return VoField
     */
    public function setFieldAdminHelp($fieldAdminHelp)
    {
        $this->field_admin_help = $fieldAdminHelp;

        return $this;
    }

    /**
     * Get fieldAdminHelp
     *
     * @return string
     */
    public function getFieldAdminHelp()
    {
        return $this->field_admin_help;
    }

    /**
     * Set category
     *
     * @param string $category
     *
     * @return VoField
     */
    public function setCategory($category)
    {
        $this->category = $category;

        return $this;
    }

    /**
     * Get category
     *
     * @return string
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * Set fieldLink
     *
     * @param string $fieldLink
     *
     * @return VoField
     */
    public function setFieldLink($fieldLink)
    {
        $this->field_link = $fieldLink;

        return $this;
    }

    /**
     * Get fieldLink
     *
     * @return string
     */
    public function getFieldLink()
    {
        return $this->field_link;
    }

    /**
     * Set fieldExample
     *
     * @param string $fieldExample
     *
     * @return VoField
     */
    public function setFieldExample($fieldExample)
    {
        $this->field_example = $fieldExample;

        return $this;
    }

    /**
     * Get fieldExample
     *
     * @return string
     */
    public function getFieldExample()
    {
        return $this->field_example;
    }

    /**
     * Set fieldRegexpId
     *
     * @param int $fieldRegexpId
     *
     * @return VoField
     */
    public function setFieldRegexpId($fieldRegexpId)
    {
        $this->field_regexp_id = $fieldRegexpId;

        return $this;
    }

    /**
     * Get fieldRegexpId
     *
     * @return int
     */
    public function getFieldRegexpId()
    {
        return $this->field_regexp_id;
    }

    /**
     * Set required
     *
     * @param int $required
     *
     * @return VoField
     */
    public function setRequired($required)
    {
        $this->required = $required;

        return $this;
    }

    /**
     * Get required
     *
     * @return int
     */
    public function getRequired()
    {
        return $this->required;
    }

    /**
     * Add voRegexp
     *
     * @param \AppBundle\Entity\VO\VoRegexp $voRegexp
     * @codeCoverageIgnore
     * @return VoField
     */
    public function addVoRegexp(\AppBundle\Entity\VO\VoRegexp $voRegexp)
    {
        $this->VoRegexp[] = $voRegexp;

        return $this;
    }

    /**
     * Remove voRegexp
     *
     * @param \AppBundle\Entity\VO\VoRegexp $voRegexp
     * @codeCoverageIgnore
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeVoRegexp(\AppBundle\Entity\VO\VoRegexp $voRegexp)
    {
        return $this->VoRegexp->removeElement($voRegexp);
    }

    /**
     * Get voRegexp
     * @codeCoverageIgnore
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getVoRegexp()
    {
        return $this->VoRegexp;
    }
}
