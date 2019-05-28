<?php

namespace AppBundle\Services;

use Symfony\Component\DependencyInjection\Container;
/**
 * Created by JetBrains PhpStorm.
 * User: Olivier LEQUEUX
 * Date: 14/03/13
 * Time: 15:46
 * handle Notification strategies
 */
class VoChangeNotifier
{
    private $voName;
    private $voSerial;
    private $voEmailGetter;
    private $voPermalink;
    private $optionalDynamicTargets;
    private $container;
    private $isRejected;

    // posible values for optionalDynamicTargets are :   VoManagers | SiteManagers | NgiManagers
    // use them to add dynamic choice instead one done in dynamic_targets in module.yml configuration file ov vo module
    public function __construct(array $strategy, $voName, $voSerial, IVoEmailGetter $voEmailGetter, Container $container, array $optionalDynamicTargets = array(), $isRejected = false)
    {
        $this->strategy = $strategy;
        $this->voName = $voName;
        $this->voSerial = $voSerial;
        $this->voEmailGetter = $voEmailGetter;
        $this->voPermalink = null;
        $this->optionalDynamicTargets = $optionalDynamicTargets;
        $this->container = $container;
        $this->isRejected = $isRejected;
    }

    public function getVoPermalink()
    {
        return $this->voPermalink;
    }

    public function setVoPermalink($link)
    {
        $this->voPermalink = $link;
    }

    public function getVoName()
    {
        return $this->voName;
    }

    public function getTargets()
    {
        if (isset($this->strategy['static_targets'])) {
            $targets = $this->strategy['static_targets'];
        } else {
            $targets = array();
        }
        // build with configuration values
        if (isset($this->strategy['dynamic_targets'])) {
            foreach ($this->strategy['dynamic_targets'] as $method) {
                $targets = array_merge($targets,   $this->voEmailGetter->$method($this->container, $this->voSerial));
            }
        }
        // build with optional Dynamic Targets
        foreach ($this->optionalDynamicTargets as $method) {
            $targets = array_merge($targets,   $this->voEmailGetter->$method($this->container, $this->voSerial));
        }

        return $targets;

    }

    public function getSubject()
    {
        if ($this->isRejected) {
            return str_replace("%VONAME%", $this->voName, $this->strategy['subject']);

        }
        return str_replace("%VONAME%", $this->voName, $this->strategy['subject']);
    }
}
