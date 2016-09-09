<?php
/**
 * Provides drush command-line integration for rc_core features.
 *
 * @copyright 2012 Lucks Digital, Inc. All rights reserved.
 */

/**
 * Implements hook_drush_command().
 */
function riot_tags_drush_command() {
  $items['riot-tags-compile'] = array(
    'description' => dt('Compile RiotJS tag(s)'),
    'callback' => 'drush_riot_tags_compile',
    'aliases' => array('riotc'),
    'bootstrap' => DRUSH_BOOTSTRAP_DRUPAL_FULL,
    'options' => array(
      'watch' => array(
        'description' => 'Override the limit settings for the number of items to refresh. Set to 0 for unlimited.',
      ),
      'dirty' => array(
        'description' => 'Dirty items before running the refresh.',
      )
    )
  );
  return $items;
}

function drush_riot_tags_compile() {
  $tags = func_get_args();
  $manager = \Drupal\riot_tags\RiotTagPluginManager::create();
  $rspath = drupal_realpath(drupal_get_path('module','riot_solr'));
  $configpath = $rspath . '/riot.config.js';
  foreach($manager->getDefinitions() as $def) {
    if(count($tags) == 0 || in_array($def['htmlTagName'], $tags)) {
      drush_log(dt('Compiling @tag', array('@tag' => $def['htmlTagName'])), 'ok');
      $provpath = drupal_realpath(drupal_get_path('module',$def['provider']));
      $buildpath = dirname($provpath . '/' . $def['compiledTagPath']);
      $srcpath = str_replace('/build', '/src', $buildpath);
      $localscss = path_to_theme() . '/riottags/scss/' . '_' . $def['htmlTagName'] . '.scss';
      $overridescsspath = drupal_realpath($localscss);
      if($overridescsspath && file_exists($overridescsspath)) {
        $buildpath = drupal_realpath(path_to_theme() . '/riottags/build');
      }
      $cmd = "riot $srcpath $buildpath --config $configpath";
      $lines = array();
      $status = NULL;
      exec($cmd, $lines, $status);
      foreach($lines as $ln) {
        drush_log($ln, $status ? 'error' : 'ok');
      }
    }
  }
}