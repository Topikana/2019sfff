<?php

namespace AppBundle\Services\OpsLavoisier;

use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Definition\Processor;


class OpsLavoisierLoader extends Extension {

    const FILE_NAME = 'OpsLavoisierConf.xml';

    static $mandatoryParameters = array(
        'lavoisier_host_core',
    );

    public function load(array $configs, ContainerBuilder $container) {

        $this->loadServiceConfiguration($configs, $container);

    }

    public function getPath(){
        $fileLocator = new FileLocator(__DIR__ . '/Config' );
        return $fileLocator->locate(OpsLavoisierLoader::FILE_NAME);
    }

    /**
     * comments
     */
    private function loadServiceConfiguration(array $configs, ContainerBuilder $container) {

        $loader = new XmlFileLoader($container, new FileLocator(__DIR__ . '/Config'));

        // check & add mandatory parameters to container
        foreach (self::$mandatoryParameters as $key) {
            if (!isset($configs[$key])) {
                throw new \Exception("Unable to set OpsLavoisier configuration,
                 you must provide following parameters : $key");
            }
            else
                $container->setParameter($key, $configs[$key]);
        }

        $loader->load(OpsLavoisierLoader::FILE_NAME);

    }

}

