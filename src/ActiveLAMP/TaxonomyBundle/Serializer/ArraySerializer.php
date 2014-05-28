<?php
/**
 * Created by PhpStorm.
 * User: bezalelhermoso
 * Date: 5/27/14
 * Time: 10:36 AM
 */

namespace ActiveLAMP\TaxonomyBundle\Serializer;
use ActiveLAMP\TaxonomyBundle\Entity\Term;
use ActiveLAMP\TaxonomyBundle\Entity\VocabularyField;
use ActiveLAMP\TaxonomyBundle\Metadata\TaxonomyMetadata;
use ActiveLAMP\TaxonomyBundle\Model\TaxonomyService;


/**
 * Class ArraySerializer
 *
 * @package ActiveLAMP\TaxonomyBundle\Serializer
 * @author Bez Hermoso <bez@activelamp.com>
 */
class ArraySerializer implements SerializerInterface
{
    protected $metadata;

    /**
     * @var TaxonomyService
     */
    protected $taxonomyService;

    public function __construct(TaxonomyMetadata $metadata, TaxonomyService $taxonomyService = null)
    {
        $this->metadata = $metadata;

        if (null !== $taxonomyService) {
            $this->setTaxonomyService($taxonomyService);
        }
    }

    /**
     * @param $entity
     * @return array
     */
    public function serialize($entity)
    {
        $metadata = $this->metadata->getEntityMetadata($entity);

        $serialized = array();

        foreach ($metadata->getVocabularies() as $vocabMetadata) {
            $field = $vocabMetadata->extractValueInField($entity);
            $vocabData = $this->serializeField($field);
            $serialized[] = $vocabData;
        }

        return $serialized;
    }

    public function serializeTerms($terms)
    {
        $termData = array();
        foreach ($terms as $term) {

            if (!$term instanceof Term) {
                throw new \InvalidArgumentException(sprintf(
                    'Expected instance of Term. "%s" given.',
                    get_class($term)
                ));
            }

            $termData[] = array(
                'id' => $term->getId(),
                'name' => $term->getName(),
                'weight' => $term->getWeight(),
            );
        }

        return $termData;
    }


    public function serializeField(VocabularyField $field)
    {
        $vocabulary = $field->getVocabulary();
        $vocabData = array(
            'id' => $vocabulary->getId(),
            'name' => $vocabulary->getName(),
            'label' => $vocabulary->getLabelName(),
            'description' => $vocabulary->getDescription(),
            'terms' => $this->serializeTerms($field->getTerms()),
        );

        return $vocabData;
    }


    public function setTaxonomyService(TaxonomyService $taxonomyService)
    {
        $this->taxonomyService = $taxonomyService;
        return $this;
    }

    /**
     * @return TaxonomyService
     * @throws \RuntimeException
     */
    public function getTaxonomyService()
    {
        if (null === $this->taxonomyService) {
            throw new \RuntimeException('A TaxonomyService was never injected into this serializer.');
        }

        return $this->taxonomyService;
    }

    /**
     * @param $entity
     * @param $serializedData
     * @throws \OutOfBoundsException
     * @throws \InvalidArgumentException
     * @throws \DomainException
     */
    public function deserialize($entity, $serializedData)
    {
        $metadata = $this->metadata->getEntityMetadata($entity);

        $service = $this->getTaxonomyService();
        $service->loadVocabularyFields($entity);

        foreach ($serializedData as $vocabData) {

            if (!isset($vocabData['name'])) {
                throw new \OutOfBoundsException('Cannot find "name" attribute of root element child.');
            }

            if (!isset($vocabData['terms'])) {
                throw new \OutOfBoundsException('Cannot find "terms" attribute of root element child.');
            }

            if (!is_array($vocabData['terms'])) {
                throw new \InvalidArgumentException('"terms" attribute must be of type array.');
            }

            $vocabMetadata = $metadata->getVocabularyByName($vocabData['name']);

            if (!$vocabMetadata) {
                throw new \DomainException(sprintf(
                    'Vocabulary "%s" is not defined in entity type "%s"',
                    $vocabData['name'],
                    $metadata->getType()
                ));
            }

            $field = $vocabMetadata->extractValueInField($entity);

            $terms = $this->deserializeTerms($vocabData['terms']);
            $field->replace($terms);

        }
    }

    public function deserializeTerms(array $terms)
    {
        $termIds = array();
        foreach ($terms as $i => $termData) {
            if (!isset($termData['id'])) {
                throw new \OutOfBoundsException('Cannot find "id" attribute of term item on index ' . $i);
            }
            $termIds[] = $termData['id'];
        }

        if (count($termIds)) {
            $terms = $this->getTaxonomyService()->findTermsByIds($termIds);
        } else {
            $terms = array();
        }

        return $terms;
    }
}