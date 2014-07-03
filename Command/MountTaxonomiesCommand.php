<?php
/**
 * Created by PhpStorm.
 * User: bezalelhermoso
 * Date: 6/30/14
 * Time: 11:16 AM
 */

namespace ActiveLAMP\Bundle\TaxonomyBundle\Command;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;


/**
 * Class MountTaxonomiesCommand
 *
 * @package ActiveLAMP\Bundle\TaxonomyBundle\Command
 * @author Bez Hermoso <bez@activelamp.com>
 */
class MountTaxonomiesCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('taxonomy:vocabulary:mount')
            ->setAliases(array('tax:mnt'))
            ->addOption('em', null, InputOption::VALUE_OPTIONAL, 'Entity manager to mount to.', null)
            ->setDescription('Mount taxonomies found in YAML config files in all bundles.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $service = $this->getContainer()->get('al_taxonomy.taxonomy_service');
        $loader = $this->getContainer()->get('al_taxonomy.taxonomy_loader');

        if ($input->getOption('em') === null) {
            $service = $service->getTaxonomyForManager($input->getOption('em'));
        }

        $vocabs = array();

        foreach ($loader->getVocabularies() as $vocab) {

            if (null !== ($vocabulary = $service->findVocabularyByName($vocab->getName()))) {
                $vocabulary->setLabel($vocab->getLabel());
                $vocabulary->setDescription($vocab->getDescription());
            } else {
                $vocabulary = $vocab;
            }
            $vocabs[$vocab->getName()] = $vocabulary;
            $service->saveVocabulary($vocabulary);
        }

        foreach ($loader->getTerms() as $t) {
            if (null !== ($term = $service->findTermByName($t->getName()))) {
                $term->setLabel($t->getLabel());
                $term->setWeight($t->getWeight());
            } else {
                $term = $t;
            }
            $term->setVocabulary($vocabs[$term->getVocabulary()->getName()]);
            $service->saveTerm($term);
        }
    }
} 