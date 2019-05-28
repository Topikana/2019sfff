<?php

namespace AppBundle\Services\TicketingSystem\Workflow;

use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Yaml\Yaml;
use AppBundle\Services\TicketingSystem\Workflow\StepConfiguration;
use AppBundle\Services\TicketingSystem\Workflow\Step;

/**
 * Description of Workflow/Loader
 * @author Olivier LEQUEUX
 */
class Loader extends Extension
{

    public $path;

    public function __construct($path)
    {
        $this->path = $path;
    }

    public function load(array $configs, ContainerBuilder $container)
    {

        // load service configuration
        $loader = new XmlFileLoader($container, new FileLocator($this->path));
        $loader->load('service.xml');
        $loadingConf = $container->getParameter('loading');


        // load raw steps
        $rawSteps = array();
        foreach ($loadingConf['locations'] as $stepName => $fileName) {

            $rawSteps[$stepName] = Yaml::parse(file_get_contents($this->path . $fileName));

            if (!is_array($rawSteps[$stepName])) {
                throw new \Exception('Unable to parse step configuration at ' . $this->path . $fileName);
            }
        }


        //manage ineritances
        $mergedSteps = array();
        $processor = new Processor;
        $configuration = new StepConfiguration;
        $processedConfiguration = array();


        foreach ($rawSteps as $stepName => $conf) {

            if (isset($loadingConf['inheritances'][$stepName])) {
                $parentStep = $loadingConf['inheritances'][$stepName];
                $mergedSteps = array('step' => self::MergeArrays($rawSteps[$parentStep]['step'], $conf['step']));
            } else {
                $mergedSteps = array('step' => $conf['step']);
            }
            try {
                $processedConfiguration[$stepName] = new Step($processor->processConfiguration($configuration, $mergedSteps));
            } catch (\Exception $e) {
                $msg = $e->getMessage();
                throw new \Exception("Unable to process '$stepName' step configuration : $msg ");
            }
        }

        if(!isset($loadingConf['steps_param_identifier'])) {
            throw new \Exception("Please set 'steps_param_identifier'' parameter in 'location' parameter and in your service");
        }

        $container->setParameter($loadingConf['steps_param_identifier'], $processedConfiguration);

    }

    /**
     * merge 2 arrays recursively to make inheritance
     * between configuration files
     * @param <array> $Arr1
     * @param <array> $Arr2
     * @return <array>
     */
    static public function MergeArrays($Arr1, $Arr2)
    {
        foreach ($Arr2 as $key => $Value) {
            if (array_key_exists($key, $Arr1) && is_array($Value))
                $Arr1[$key] = self::MergeArrays($Arr1[$key], $Arr2[$key]);
            else
                $Arr1[$key] = $Value;
        }

        return $Arr1;
    }


}

