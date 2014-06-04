<?php
/**
 * Created by PhpStorm.
 * User: bezalelhermoso
 * Date: 6/4/14
 * Time: 10:30 AM
 */

namespace ActiveLAMP\TaxonomyBundle\Entity\Repository;
use ActiveLAMP\TaxonomyBundle\Model\VocabularyRepositoryInterface;
use Doctrine\ORM\EntityRepository;


/**
 * Class VocabularyRepository
 *
 * @package ActiveLAMP\TaxonomyBundle\Entity\Repository
 * @author Bez Hermoso <bez@activelamp.com>
 */
class VocabularyRepository extends EntityRepository implements VocabularyRepositoryInterface
{

    public function findById($id)
    {
        return $this->find($id);
    }

    public function findByName($name)
    {
        return $this->findOneBy(array('name' => $name));
    }
}