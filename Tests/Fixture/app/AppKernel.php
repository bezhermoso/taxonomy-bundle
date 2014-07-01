<?php


namespace ActiveLAMP\Bundle\TaxonomyBundle\Tests\Fixture\Application;
use ActiveLAMP\Bundle\TaxonomyBundle\ALTaxonomyBundle;
use ActiveLAMP\Bundle\TaxonomyBundle\Tests\Fixture\ALTaxonomyTestBundle;
use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symfony\Component\HttpKernel\Kernel;


/**
 * Class AppKernel
 *
 * @author Bez Hermoso <bez@activelamp.com>
 */
class AppKernel extends Kernel
{

    /**
     * Returns an array of bundles to register.
     *
     * @return BundleInterface[] An array of bundle instances.
     *
     * @api
     */
    public function registerBundles()
    {
        return array(
            new FrameworkBundle(),
            new DoctrineBundle(),
            new ALTaxonomyBundle(),
            new ALTaxonomyTestBundle(),
        );
    }

    /**
     * Loads the container configuration.
     *
     * @param LoaderInterface $loader A LoaderInterface instance
     *
     * @api
     */
    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(__DIR__ . '/config/config.yml');
    }

    public function getCacheDir()
    {
        return sprintf('%s/%s/al-taxonomy-bundle/cache/test', sys_get_temp_dir(), Kernel::VERSION);
    }

    public function getLogDir()
    {
        return sprintf('%s/%s/al-taxonomy-bundle/logs', sys_get_temp_dir(), Kernel::VERSION);
    }
}