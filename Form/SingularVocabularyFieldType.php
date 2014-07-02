<?php
/**
 * Created by PhpStorm.
 * User: bezalelhermoso
 * Date: 5/30/14
 * Time: 10:58 AM
 */

namespace ActiveLAMP\Bundle\TaxonomyBundle\Form;

use ActiveLAMP\Bundle\TaxonomyBundle\Form\DataTransformer\SingularVocabularyFieldTransformer;
use ActiveLAMP\Bundle\TaxonomyBundle\Taxonomy\TaxonomyRegistry;
use ActiveLAMP\Bundle\TaxonomyBundle\Taxonomy\TaxonomyService;
use ActiveLAMP\Taxonomy\Taxonomy\TaxonomyServiceInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;


/**
 * Class SingularVocabularyFieldType
 *
 * @package ActiveLAMP\Bundle\TaxonomyBundle\Form
 * @author Bez Hermoso <bez@activelamp.com>
 */
class SingularVocabularyFieldType extends AbstractType
{
    /**
     * @var TaxonomyRegistry
     */
    protected $registry;

    /**
     * @param \ActiveLAMP\Bundle\TaxonomyBundle\Taxonomy\TaxonomyRegistry $registry
     *  m
     */
    public function __construct(TaxonomyRegistry $registry)
    {
        $this->registry = $registry;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {

        parent::setDefaultOptions($resolver);

        $resolver->setDefaults(array(
            'taxonomy_service' => $this->registry,
            'choice_list' => function (Options $options) {
                return new TermChoiceList($options['taxonomy_service'], $options['vocabulary']);
            }
        ));

        $registry = $this->registry;
        $resolver->setNormalizers(array(
            'taxonomy_service' =>
                function (Options $options, $value) use ($registry) {
                    if (is_string($value)) {
                        $value = $registry->getTaxonomyForManager($value);
                    }
                    return $value;
                }
        ));

        $resolver->setRequired(array(
            'vocabulary'
        ))
        ->setAllowedTypes(array(
            'taxonomy_service' => array('ActiveLAMP\\Taxonomy\\Taxonomy\\TaxonomyServiceInterface', 'string'),
        ))
        ;
    }

    public function getParent()
    {
        return 'choice';
    }

    public function getName()
    {
        return 'singular_vocabulary_field';
    }
}