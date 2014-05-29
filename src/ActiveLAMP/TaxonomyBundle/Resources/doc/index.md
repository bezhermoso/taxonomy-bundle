#Usage

Steps:

* Add `activelamp/taxonomy-bundle` as a dependency to your project.
* Register the bundle:

```php
   $bundles = array(
       ...
       new ActiveLAMP\TaxonomyBundle\ALTaxonomyBundle(),
   );
```

* Update your database schema (i.e., run `php app/console doctrine:schema:update --force`)
* Add the following lines to your `app/config/routing.yml` file to expose the back-end administration for taxonomies and terms:

```yml
al_taxonomy:
    resource: "@ALTaxonomyBundle/Controller/TaxonomyController.php"
    type:     annotation

al_term:
    resource: "@ALTaxonomyBundle/Controller/TermController.php"
    type:     annotation
```

* Start using vocabularies with your entities using annotations.


#Annotations

Annotations reside in the `\ActiveLAMP\TaxonomyBundle\Annotations` namespace.

Declare that your entity is a termed entity by tagging it with the `@Entity` annotation:

```php
<?php

use ActiveLAMP\TaxonomyBundle\Annotations as Taxn;

/**
 *
 * @Taxn\Entity
 */
class User
{

}
```

...then mark the properties which are going to be using vocabulary terms with the `@Vocabulary` annotation:

```php

<?php


   /**
    *
    * @Taxn\Vocabulary(name="languages")
    */
   protected $languages;
   
   /**
    *
    * @Taxn\Vocabulary(name="organizations", singular=true)
    */
   protected $organization;
 
```

And you might want to stub non-singular vocabulary fields in the constructor method:

```php
<?php

use Doctrine\Common\Collections\ArrayCollection;


     public function __construct()
     {
         $this->languages = new ArrayCollection();
     }
     
```

This way the fields will behave as if taxonomy terms are already loaded even if the entity object is detached:

```php

$user = new User(); //Detached entity.
$user->getLanguages()->removeElement($swahili);
$user->getLanguages()->add($french);

```
If you didn't stub the `languages` property with an ArrayCollection instance, the last two calls will throw errors.


#The taxonomy service

The taxonomy service can be retrieved from the service container at `al_taxonomy.taxonomy_service`.

#Common operations

###Retrieving vocabularies

```php
<?php

//Via the service:
$languages = $service->findVocabularyByName("languages")

//Via the vocabulary field of a managed entity:

$user = $em->find('Your\Namespace\User', 1);
$languages = $user->getLanguages()->getVocabulary();

//From detached entities:

$user = new User();
$service->loadVocabularyFields($user);
$languages = $user->getLanguages()->getVocabulary();
```

###Retrieving terms

```php
<?php

//From a vocabulary object:

$languages = $service->findVocabularyByName("languages");

$french = $language->getTermByName('french');
$filipino = $language->getTermByName('filipino');

/* Will throw a \DomainException for non-existing terms. */
$klingon = $language->getTermByName('klingon'); 
```

###Persisting taxonomies

```php
$user = $em->find('Your\Namespace\User', 1);
$languages = $user->getLanguages()->getVocabulary();

$user->getLanguages()->add($languages->getTermByName('french'));
$user->getLanguages()->removeElement($languages->getTermByName('english'));

$service->saveTaxonomies($user);

```
