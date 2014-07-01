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
                ->arrayNode('taxonomies')
                    ->useAttributeAsKey('name')
                    ->prototype('array')
                        ->addDefaultChildrenIfNoneSet()
                        ->children()
                            ->scalarNode('vocabulary_class')->default('%al_taxonomy.entity.vocabulary.class%')->end()
                            ->scalarNode('term_class')->default('%al_taxonomy.entity.term.class%')->end()
                            ->scalarNode('entity_term_class')->default('%al_taxonomy.entity.entity_term.class%')->end()
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
