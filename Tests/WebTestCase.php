<?php


namespace ActiveLAMP\Bundle\TaxonomyBundle\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase as BaseTestCase

/**
 * @author Bez Hermoso <bez@activelamp.com>
 */
class WebTestCase extends BaseTestCase
{
    public function setUp()
    {
        parent::setUp();
    }

    public function tearDown()
    {
        parent::tearDown();
    }

    /**
     * @return string
     */
    protected static function getKernelClass()
    {
        require_once __DIR__ . '/Fixture/app/AppKernel.php';
        return 'ActiveLAMP\\Bundle\\TaxonomyBundle\\Tests\\Fixture\\AppKernel';
    }

    /**
     * @param array $options
     * @return \Symfony\Component\HttpKernel\KernelInterface
     */
    protected static function createKernel(array $options = array())
    {
        $className = static::getKernelClass();
        return new $className('test', isset($options['debug']) ? $options['debug'] : true);
    }

    /**
     * @return \Symfony\Component\DependencyInjection\ContainerInterface
     */
    protected function getContainer()
    {
        if (!static::$kernel) {
            static::$kernel = static::createKernel();
            static::$kernel->boot();
        }
        return static::$kernel->getContainer();
    }
} 