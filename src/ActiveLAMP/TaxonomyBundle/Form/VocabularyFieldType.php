<?php
/**
 * Created by PhpStorm.
 * User: bezalelhermoso
 * Date: 5/23/14
 * Time: 2:46 PM
 */

namespace ActiveLAMP\TaxonomyBundle\Form;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;


/**
 * Class VocabularyFieldType
 *
 * @package ActiveLAMP\TaxonomyBundle\Form
 * @author Bez Hermoso <bez@activelamp.com>
 */
class VocabularyFieldType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options); // TODO: Change the autogenerated stub
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'ActiveLAMP\TaxonomyBundle\Entity\VocabularyField'
        ));
    }

    public function getParent()
    {
        return parent::getParent(); // TODO: Change the autogenerated stub
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'vocabulary_field';
    }
}