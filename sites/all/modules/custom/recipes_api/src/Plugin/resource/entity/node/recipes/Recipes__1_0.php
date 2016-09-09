<?php

namespace Drupal\recipes_api\Plugin\resource\entity\node\recipes;

use Drupal\restful\Plugin\resource\ResourceNode;

/**
 * Class Recipes__1_0
 * @package Drupal\recipes_api\Plugin\resource\entity\node\recipes
 *
 * @Resource(
 *   name = "recipes:1.0",
 *   resource = "recipes",
 *   label = "Recipes",
 *   description = "Export the recipes with all authentication providers.",
 *   authenticationTypes = TRUE,
 *   authenticationOptional = TRUE,
 *   dataProvider = {
 *     "entityType": "node",
 *     "bundles": {
 *       "recipe"
 *     },
 *   },
 *   majorVersion = 1,
 *   minorVersion = 0
 * )
 */

class Recipes__1_0 extends ResourceNode {
/*
 * {@inheritdoc}
 */
    protected function publicFields()
    {
        $public_fields = parent::publicFields();

        $public_fields['name'] = $public_fields['label'];
        unset($public_fields['label']);
        $public_fields['description'] = array(
            'property' => 'body',
            'sub_property' => 'value',
            'process_callbacks' => array('strip_tags'),
        );
        $public_fields['image'] = array (
            'property' => 'field_image',
        );

        return $public_fields;
    }

}