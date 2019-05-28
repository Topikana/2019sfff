<?php

namespace AppBundle\Services\TicketingSystem\Helpdesk;

use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Yaml\Yaml;


/**
 * Description of Helpdesk Loader
 * @author Olivier LEQUEUX
 */
class Loader extends Extension
{

    static $mandatoryParameters = array(
        'ggus.team.login',
        'ggus.team.password',
        'ggus.ops.login',
        'ggus.ops.password',
        'rt.ops.login',
        'rt.ops.password',
        'sf1.env',
        'lavoisier_host_core',
        'cache.dir');

    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__ . '/Config'));

        // check & add mandatory parameters to container
        foreach (self::$mandatoryParameters as $key) {

            if (!isset($configs[$key])) {
                throw new \Exception("Unable to set Ticketing System configuration,
                 you must provide following parameters : $key");
            } else
                $container->setParameter($key, $configs[$key]);
        }


        $loader->load(sprintf('ggus_env.%s.xml', $container->getParameter('sf1.env')));
        $loader->load('ggus_services.xml');
        $loader->load(sprintf('rt_env.%s.xml', $container->getParameter('sf1.env')));
        $loader->load('rt_services.xml');
    }

}

