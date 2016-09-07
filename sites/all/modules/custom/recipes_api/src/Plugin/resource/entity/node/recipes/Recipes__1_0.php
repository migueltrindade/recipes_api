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

}