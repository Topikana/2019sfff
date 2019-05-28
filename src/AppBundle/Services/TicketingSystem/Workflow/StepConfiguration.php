<?php

/**
 * structure constraints for a step configuration file
 * @author olivier lequeux
 */

namespace AppBundle\Services\TicketingSystem\Workflow;

use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;

class StepConfiguration implements ConfigurationInterface {

    /**
     * Treebuilder building required by interface
     */    
    public function getConfigTreeBuilder() {

        $treeBuilder = new TreeBuilder();
        $root = $treeBuilder->root('step');
        
        $root
            ->prototype('array')
                ->children()
                    ->scalarNode('id')
                        ->isRequired()
                    ->end()
                    ->scalarNode('value')->end()
                    ->booleanNode('required')
                        ->defaultFalse()
                    ->end()
                    ->booleanNode('visibility')
                        ->defaultTrue()
                    ->end()
                    ->booleanNode('quick')
                    ->defaultFalse()
                    ->end()
                    ->scalarNode('label')->end()
                    ->scalarNode('help')->end()
                    ->scalarNode('json_param')->end()
                    ->scalarNode('order')
                        ->defaultValue('999')
                    ->end()
                ->end()
              ->end();
               

        return $treeBuilder;
    }
    

}