<?php

namespace ActiveLAMP\Bundle\TaxonomyBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('al_taxonomy');

        $rootNode
            ->children()
                ->scalarNode('default_taxonomy')->defaultValue('default')->cannotBeEmpty()->end()
                ->arrayNode('taxonomies')
                    ->requiresAtLeastOneElement()
                    ->useAttributeAsKey('default')
                    ->info('Taxonomy configuration for specific entity managers.')
                    ->prototype('array')
                        ->children()
                            ->scalarNode('connection')->defaultValue(null)->end()
                            ->scalarNode('vocabulary_class')->defaultValue(null)->end()
                            ->scalarNode('term_class')->defaultValue(null)->end()
                            ->scalarNode('entity_term_class')->defaultValue(null)->end()
                            ->scalarNode('taxonomy_service_class')->defaultValue(null)->end()
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
