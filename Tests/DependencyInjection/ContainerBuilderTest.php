<?php


namespace ActiveLAMP\Bundle\TaxonomyBundle\Tests\DependencyInjection;
use ActiveLAMP\Bundle\TaxonomyBundle\Tests\WebTestCase;


/**
 * Class ContainerBuilderTest
 *
 * @author Bez Hermoso <bez@activelamp.com>
 */
class ContainerBuilderTest extends WebTestCase
{
    public function testRegisteredTaxonomies()
    {
        $container = $this->getContainer();

        $this->assertTrue($container->has('al_taxonomy.taxonomy_service'));
        $this->assertTrue($container->has('al_taxonomy.taxonomy_service.default'));
        $this->assertTrue($container->has('al_taxonomy.taxonomy_service.secondary'));

        $registry = $container->get('al_taxonomy.taxonomy_service');

        $default = $container->get('al_taxonomy.taxonomy_service.default');
        $secondary = $container->get('al_taxonomy.taxonomy_service.secondary');

        $this->assertSame($default, $registry->getTaxonomyForManager('default'));
        $this->assertSame($secondary, $registry->getTaxonomyForManager('secondary'));

        $listeners = $default->getEntityManager()->getEventManager()->getListeners('loadClassMetadata');
        $this->assertContains($container->get('al_taxonomy.doctrine.resolve_target_entities.default'), $listeners);

        $listeners = $secondary->getEntityManager()->getEventManager()->getListeners('loadClassMetadata');
        $this->assertContains($container->get('al_taxonomy.doctrine.resolve_target_entities.secondary'), $listeners);

    }
} 