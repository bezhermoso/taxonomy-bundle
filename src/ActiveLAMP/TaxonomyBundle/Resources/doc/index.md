#Usage

Steps:

1. Add `activelamp/taxonomy-bundle` as a dependency to your project.
2. Register `\ActiveLAMP\TaxonomyBundle\ALTaxonomyBundle` in your `app/AppKernel.php` file.

```php
   $bundles = array(
       ...
       new ActiveLAMP\TaxonomyBundle\ALTaxonomyBundle(),
   );
```

3. Update your database schema (i.e., run `php app/console doctrine:schema:update --force`)
4. Add the following lines to your `app/config/routing.yml` file to expose the back-end administration for taxonomies and terms:

```yml
al_taxonomy:
    resource: "@ALTaxonomyBundle/Controller/TaxonomyController.php"
    type:     annotation

al_term:
    resource: "@ALTaxonomyBundle/Controller/TermController.php"
    type:     annotation
```

5. Start using vocabularies with your entities using annotations.


##Annotations

Annotations reside in the `\ActiveLAMP\TaxonomyBundle\Annotations` namespace.

1. Specify that your entity contains vocabulary fields:

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

2. Mark which properties to use as vocabulary fields:

/**
 * @Taxn\Vocabulary("organization", singular=true)
 */
 protected $organization;
 
 
/**
 *
 * @Taxn\Vocabulary("language", singular=false)
 */
 protected $languages;
