<?php

namespace ActiveLAMP\Bundle\TaxonomyBundle\DependencyInjection;

use ActiveLAMP\Bundle\TaxonomyBundle\DependencyInjection\Configuration;
use Doctrine\ORM\Tools\ResolveTargetEntityListener;
use Symfony\Component\DependencyInjection\Alias;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class ALTaxonomyExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');
        $loader->load('forms.yml');

        if (count($config['taxonomies']) == 0) {
            $config['taxonomies']['default'] = array();
        }

        $this->prepareTaxonomyLoader($container);
        $this->prepareTaxonomies($config, $container);
    }

    /**
     * @param ContainerBuilder $container
     */
    protected function prepareTaxonomyLoader(ContainerBuilder $container)
    {
        $bundles = $container->getParameter('kernel.bundles');

        $files = array();
        foreach ($bundles as $name => $ns) {
            $ref = new \ReflectionClass($ns);
            $file = dirname($ref->getFileName()) . '/Resources/config/taxonomy.yml';
            if (file_exists($file)) {
                $files[] = $file;
            }
        }

        $definition = $container->getDefinition('al_taxonomy.taxonomy_loader');
        $definition->replaceArgument(0, $files);
    }

    public function prepareTaxonomies(array $config, ContainerBuilder $container)
    {
        foreach ($config['taxonomies'] as $em => $taxonomy) {
            $this->prepareTaxonomy($taxonomy, $em, $container);
        }

        if (!isset($config['taxonomies'][$config['default_taxonomy']])) {
            throw new \InvalidArgumentException(sprintf('Cannot find "%s" taxonomy, thus cannot set it as default', $config['default_taxonomy']));
        }

        $container->getDefinition('al_taxonomy.taxonomy_service')->replaceArgument(1, $config['default_taxonomy']);
        $container->setParameter('al_taxonomy.default_taxonomy', $config['default_taxonomy']);
    }

    public function prepareTaxonomy(array $taxonomyConfig, $emName, ContainerBuilder $container)
    {
        $defaults = array(
            'vocabulary_class' => $container->getParameter('al_taxonomy.entity.vocabulary.class'),
            'term_class' => $container->getParameter('al_taxonomy.entity.term.class'),
            'entity_term_class' => $container->getParameter('al_taxonomy.entity.entity_term.class'),
            'taxonomy_service_class' => $container->getParameter('al_taxonomy.taxonomy_service.class'),
            'connection' => $emName,
        );

        $taxonomyConfig = array_replace_recursive($defaults, array_filter($taxonomyConfig));

        $service = new Definition($taxonomyConfig['taxonomy_service_class']);
        $service->setArguments(array(
            new Reference(sprintf('doctrine.orm.%s_entity_manager', $emName)),
            $defaults['vocabulary_class'],
            $defaults['term_class'],
            $defaults['entity_term_class'],
        ));

        $this->attachResolveTargetListener($taxonomyConfig, $emName, $container);

        $container->setDefinition(sprintf('al_taxonomy.taxonomy_service.%s', $emName), $service);

    }

    /**
     * @param array $config
     * @param $emName
     * @param ContainerBuilder $container
     */
    protected function attachResolveTargetListener(array $config, $emName, ContainerBuilder $container)
    {
        $definition = new Definition('Doctrine\ORM\Tools\ResolveTargetEntityListener');

        $definition->addMethodCall('addResolveTargetEntity', array(
            'ActiveLAMP\\Taxonomy\\Entity\\VocabularyInterface',
            $config['vocabulary_class'],
            array(),
        ));
        $definition->addMethodCall('addResolveTargetEntity', array(
            'ActiveLAMP\\Taxonomy\\Entity\\TermInterface',
            $config['term_class'],
            array(),
        ));
        $definition->addMethodCall('addResolveTargetEntity', array(
            'ActiveLAMP\\Taxonomy\\Entity\\EntityTermInterface',
            $config['entity_term_class'],
            array(),
        ));
        $definition->addTag('doctrine.event_listener', array(
            'event' => 'loadClassMetadata',
            'connection' => $config['connection'],
        ));

        $container->setDefinition('al_taxonomy.doctrine.resolve_target_entities.' . $emName, $definition);
    }
}
