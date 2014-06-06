<?php
/**
 * Created by PhpStorm.
 * User: bezalelhermoso
 * Date: 6/4/14
 * Time: 5:36 PM
 */

namespace ActiveLAMP\TaxonomyBundle\Metadata;
use ActiveLAMP\TaxonomyBundle\Metadata\Reader\AnnotationReader;
use ActiveLAMP\TaxonomyBundle\Metadata\Reader\ReaderInterface;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\ClassMetadata;


/**
 * Class MetadataFactory
 *
 * @package ActiveLAMP\TaxonomyBundle\Metadata
 * @author Bez Hermoso <bez@activelamp.com>
 */
class MetadataFactory 
{

    /**
     * @var TaxonomyMetadata
     */
    protected $metadata;

    /**
     * @var Reader\ReaderInterface
     */
    protected $reader;


    public function __construct(ReaderInterface $reader = null)
    {
        if ($reader === null) {
            $reader = new AnnotationReader();
        }
        $this->reader = $reader;
    }


    public function getMetadata(EntityManager $em)
    {
        $doctrineMetadata = $em->getMetadataFactory()->getLoadedMetadata();

        if (count($doctrineMetadata) == 0) {
            $doctrineMetadata = $em->getMetadataFactory()->getAllMetadata();
        }

        $taxMetadata = new TaxonomyMetadata();

        foreach ($doctrineMetadata as $dm) {

            if (!$dm instanceof ClassMetadata) {
                continue;
            }

            $m = $this->createEntityMetadataForClass($dm->getReflectionClass());

            $this->reader->loadMetadataForClass($dm->getReflectionClass()->getName(), $m);

            $prevParent = $m;

            foreach ($this->getClassParents($dm->getReflectionClass()) as $parent) {
                $pm = $this->createEntityMetadataForClass($parent);
                $this->reader->loadMetadataForClass($parent->getName(), $pm);
                $prevParent->setParent($pm);
                $prevParent = $pm;
            }

            var_dump($m->getType());

            if ($m->getType() && count($m->getVocabularies()) > 0) {
                $taxMetadata->addEntityMetadata($m);
            }
        }

        return $taxMetadata;
    }

    /**
     * @param \ReflectionClass $class
     * @return \ReflectionClass[]
     */
    public function getClassParents(\ReflectionClass $class)
    {
        if ($class->getParentClass()) {
            $parents = $this->getClassParents($class->getParentClass());
            $p = array($class->getParentClass());
            return array_merge($p, $parents);
        } else {
            return array();
        }
    }

    public function createEntityMetadataForClass(\ReflectionClass $class)
    {
        $m = new Entity($class);
        return $m;
    }
}