<?php


namespace ActiveLAMP\Bundle\TaxonomyBundle\Taxonomy;
use ActiveLAMP\Taxonomy\Entity\EntityTermInterface;
use ActiveLAMP\Taxonomy\Entity\TermInterface;
use ActiveLAMP\Taxonomy\Entity\VocabularyInterface;
use ActiveLAMP\Taxonomy\Metadata\TaxonomyMetadata;
use ActiveLAMP\Taxonomy\Taxonomy\TaxonomyServiceInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerInterface;


/**
 * Class TaxonomyRegistry
 *
 * @author Bez Hermoso <bez@activelamp.com>
 */
class TaxonomyRegistry implements TaxonomyServiceInterface
{
    /**
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
     */
    protected $container;

    /**
     * @var string
     */
    protected $defaultManager;

    /**
     * @param ContainerInterface $container
     * @param $defaultManager
     */
    public function __construct(ContainerInterface $container, $defaultManager)
    {
        $this->container = $container;
        $this->defaultManager = $defaultManager;
    }

    /**
     * @param $manager
     * @return TaxonomyServiceInterface
     * @throws \RuntimeException
     */
    public function getTaxonomyForManager($manager)
    {
        $id = sprintf('al_taxonomy.taxonomy_service.%s', $manager);
        if (false === $this->container->has($id)) {
            throw new \RuntimeException(sprintf('No taxonomy configured for the "%s" object manager', $manager));
        }
        return $this->container->get($id);
    }

    /**
     * @return TaxonomyServiceInterface
     */
    protected function getDefaultTaxonomy()
    {
        return $this->getTaxonomyForManager($this->defaultManager);
    }

    /**
     * @param $name
     * @return TermInterface
     */
    public function findTermByName($name)
    {
        return $this->getDefaultTaxonomy()->findTermByName($name);
    }

    /**
     * @return TaxonomyMetadata
     */
    public function getMetadata()
    {
        return $this->getDefaultTaxonomy()->getMetadata();
    }

    /**
     * @param VocabularyInterface $vocabulary
     * @param $flush
     * @return
     */
    public function saveVocabulary(VocabularyInterface $vocabulary, $flush = true)
    {
        return $this->getDefaultTaxonomy()->saveVocabulary($vocabulary, $flush);
    }

    /**
     * @return VocabularyInterface[]|array
     */
    public function findAllVocabularies()
    {
        return $this->getDefaultTaxonomy()->findAllVocabularies();
    }

    /**
     * @param $entity
     * @param bool $flush
     */
    public function saveTaxonomies($entity, $flush = true)
    {
        return $this->getDefaultTaxonomy()->saveTaxonomies($entity, $flush);
    }

    /**
     * @param EntityTermInterface $entityTerm
     * @param bool $flush
     * @throws \LogicException
     */
    public function saveEntityTerm(EntityTermInterface $entityTerm, $flush = true)
    {
        return $this->getDefaultTaxonomy()->saveEntityTerm($entityTerm, $flush);
    }

    /**
     * @param $entity
     * @throws \RuntimeException
     */
    public function loadVocabularyFields($entity)
    {
        return $this->getDefaultTaxonomy()->loadVocabularyFields($entity);
    }

    /**
     * @param TermInterface $term
     */
    public function deleteTerm(TermInterface $term)
    {
        return $this->getDefaultTaxonomy()->deleteTerm($term);
    }

    /**
     * @param $name
     * @return VocabularyInterface[]
     */
    public function findVocabularyByName($name)
    {
        return $this->getDefaultTaxonomy()->findVocabularyByName($name);
    }

    /**
     * @return ObjectManager
     */
    public function getEntityManager()
    {
        return $this->getDefaultTaxonomy()->getEntityManager();
    }

    /**
     * @param VocabularyInterface $vocabulary
     */
    public function deleteVocabulary(VocabularyInterface $vocabulary)
    {
        return $this->getDefaultTaxonomy()->deleteVocabulary($vocabulary);
    }

    /**
     * @param $id
     * @return TermInterface
     */
    public function findTermById($id)
    {
        return $this->getDefaultTaxonomy()->findTermById($id);
    }

    /**
     * @param TermInterface $term
     * @param bool $flush
     */
    public function saveTerm(TermInterface $term, $flush = true)
    {
        return $this->getDefaultTaxonomy()->saveTerm($term, $flush);
    }

    /**
     * @return TermInterface[]|array
     */
    public function findAllTerms()
    {
        return $this->getDefaultTaxonomy()->findAllTerms();
    }

    /**
     * @param $vocabulary
     * @return ArrayCollection
     */
    public function findTermsInVocabulary($vocabulary)
    {
        return $this->getDefaultTaxonomy()->findTermsInVocabulary($vocabulary);
    }

    /**
     * @return string
     */
    public function getTermClass()
    {
        return $this->getDefaultTaxonomy()->getTermClass();
    }

    /**
     * @return string
     */
    public function getVocabularyClass()
    {
        return $this->getDefaultTaxonomy()->getVocabularyClass();
    }

    /**
     * @return string
     */
    public function getEntityTermClass()
    {
        return $this->getDefaultTaxonomy()->getEntityTermClass();
    }

    /**
     * @return VocabularyInterface
     */
    public function createVocabulary()
    {
        return $this->getDefaultTaxonomy()->createVocabulary();
    }

    /**
     * @param VocabularyInterface $vocabulary
     *
     * @return TermInterface
     */
    public function createTerm(VocabularyInterface $vocabulary = null)
    {
        return $this->getDefaultTaxonomy()->createTerm($vocabulary);
    }

    /**
     * @return EntityTermInterface
     */
    public function createEntityTerm()
    {
        return $this->createEntityTerm();
    }
}