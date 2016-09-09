<?php
/**
 * @file theme callbacks
 */

use \Drupal\riot_tags\RiotTagPluginManager;

/**
 * Render a group of tags
 * @param $variables
 * @return bool
 */
function theme_riot_tag($variables) {
  if(empty($variables['tag_plugin_instance'])) {
    return FALSE;
  }
  if(!libraries_load('riotjs')) {
    return FALSE;
  }
  /** @var \Drupal\riot_tags\Plugin\RiotTag\RiotTagPlugin $plug */
  $plug = $variables['tag_plugin_instance'];
  $build = $plug->toRenderArray($variables['optionset_options']);
  return drupal_render($build);
}

/**
 * Process variables for rendering.
 * @param $variables
 * @throws \Exception
 */
function riot_tags_preprocess_riot_tag(&$variables) {
  if(!empty($variables['optionset'])) {
    $optionset = riot_tags_optionset_load($variables['optionset']);
    if(!$optionset) {
      throw new Exception(t('An invalid optionset was specified!'));
    }
    $variables['optionset_options'] = $optionset;
    if(!empty($variables['opts'])) {
      $variables['optionset_options']->options[$variables['optionset_options']->tag_plugin]['options'] = array_replace_recursive($variables['optionset_options']->options[$variables['optionset_options']->tag_plugin]['options'], $variables['opts']);
    }
    $manager = RiotTagPluginManager::create();
    $variables['tag_plugin_instance'] = $manager->createInstance($optionset->tag_plugin, $variables);
  }
}
