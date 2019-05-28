<?php

namespace AppBundle\Services\JSTree;

use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Definition\Processor;


class JSTreeLoader extends Extension {

    const FILE_NAME = 'jsTreeConf.xml';

    static $mandatoryParameters = array(
        'sf1.env',
        'lavoisier_host_broadcast',
        );

    public function load(array $configs, ContainerBuilder $container) {

        $this->loadServiceConfiguration($configs, $container);

    }

    public function getPath(){
        $fileLocator = new FileLocator(__DIR__ . '/Config' );
        return $fileLocator->locate(JSTreeLoader::FILE_NAME);
    }

    /**
     * comments
     */
    private function loadServiceConfiguration(array $configs, ContainerBuilder $container) {

        $loader = new XmlFileLoader($container, new FileLocator(__DIR__ . '/Config'));

        // check & add mandatory parameters to container 
        foreach (self::$mandatoryParameters as $key) {
            if (!isset($configs[$key])) {
                throw new \Exception("Unable to set JSTree configuration,
                 you must provide following parameters : $key");
            }
            else
                $container->setParameter($key, $configs[$key]);
        }

        // @todo : filename
        $loader->load(JSTreeLoader::FILE_NAME);

    }

}

