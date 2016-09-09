<?php
/**
 * Created by PhpStorm.
 * User: ryneemory
 * Date: 9/7/16
 * Time: 6:54 PM
 */

namespace Drupal\recipes_riot\Plugin\RiotTag;

use Drupal\riot_tags\Plugin\RiotTag\RiotTagPlugin;


class RecipesSearch extends RiotTagPlugin {
    public function processOptions(&$options){
        parent::processOptions($options); 
        $options['options'] = array(
          'ryne' => 'awesome',  
        );
    }

}