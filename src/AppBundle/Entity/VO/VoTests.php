<?php

namespace AppBundle\Entity\VO;

use Doctrine\ORM\Mapping as ORM;

/**
 * VoTests
 *
 * @ORM\Table(name="vo_tests")
 * @ORM\Entity
 */
class VoTests
{
    /**
     * @var string
     *
     * @ORM\Column(name="test_name", type="string", length=255)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $test_name;

    /**
     * @var string
     *
     * @ORM\Column(name="roc_name", type="string", length=100)
     */
    private $roc_name;



    /**
     * Set testName
     *
     * @param string $testName
     *
     * @return VoTests
     */
    public function setTestName($testName)
    {
        $this->test_name = $testName;

        return $this;
    }

    /**
     * Get testName
     *
     * @return string
     */
    public function getTestName()
    {
        return $this->test_name;
    }

    /**
     * Set rocName
     *
     * @param string $rocName
     *
     * @return VoTests
     */
    public function setRocName($rocName)
    {
        $this->roc_name = $rocName;

        return $this;
    }

    /**
     * Get rocName
     *
     * @return string
     */
    public function getRocName()
    {
        return $this->roc_name;
    }
}
