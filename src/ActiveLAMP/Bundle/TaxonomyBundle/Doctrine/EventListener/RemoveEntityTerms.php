<?php
/**
 * Created by PhpStorm.
 * User: bezalelhermoso
 * Date: 5/28/14
 * Time: 2:30 PM
 */

namespace ActiveLAMP\Bundle\TaxonomyBundle\Doctrine\EventListener;

use ActiveLAMP\Bundle\TaxonomyBundle\Metadata\TaxonomyMetadata;
use ActiveLAMP\Bundle\TaxonomyBundle\Model\TaxonomyService;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;


/**
 * Class RemoveEntityTerms
 *
 * @package ActiveLAMP\Bundle\TaxonomyBundle\Doctrine\EventListener
 * @author Bez Hermoso <bez@activelamp.com>
 */
class RemoveEntityTerms implements EventSubscriber
{

    /**
     * @var \ActiveLAMP\Bundle\TaxonomyBundle\Metadata\TaxonomyMetadata
     */
    protected $metadata;

    /**
     * @param TaxonomyMetadata $metadata
     */
    public function __construct(TaxonomyMetadata $metadata)
    {
        $this->metadata = $metadata;
    }

    /**
     * Returns an array of events this subscriber wants to listen to.
     *
     * @return array
     */
    public function getSubscribedEvents()
    {
        return array(
            Events::preRemove,
        );
    }

    public function preRemove(LifecycleEventArgs $eventArgs)
    {
        $entity = $eventArgs->getEntity();

        if (!$this->metadata->hasEntityMetadata($entity)) {
            return;
        }

        $metadata = $this->metadata->getEntityMetadata($entity);

        $type = $metadata->getType();
        $id = $metadata->extractIdentifier($entity);

        $dql =
            $eventArgs
                ->getEntityManager()
                ->createQueryBuilder()
                ->delete('ALTaxonomyBundle:EntityTerm', 'et')
                ->andWhere('et.entityType = :type')
                ->andWhere('et.entityIdentifier = :id')
                ->setParameters(array(
                    'type' => $type,
                    'id' => $id,
                ));

        $dql->getQuery()->execute();
    }
}