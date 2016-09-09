<?php
/**
 * @file
 * Contains \Drupal\riot_tags\Plugin\RiotTag\RiotTag
 */

namespace Drupal\riot_tags\Plugin\RiotTag;

use \Drupal\Component\Plugin\PluginBase;
use \Drupal\riot_tags\RiotTagPluginManager;
/**
 * Class RiotTagPlugin
 * @package Drupal\riot_tags\Plugin\RiotTag
 */
class RiotTagPlugin extends PluginBase implements RiotTagInterface {

  /**
   * The HTML tag to be used. Do not use < or >;
   * @var
   */
  public $htmlTagName;

  /**
   * Attributes to attach to HTML tag.
   * @var
   */
  public $htmlTagAttributes;

  /**
   * The full path to the RiotJS compiled source.
   * @var
   */
  public $compiledTagPath;

  /**
   * References to tags that depend upon this one.
   * @var array
   */
  public $childTags;

  /**
   * Flag indicating whether this plugin instance can configure and display
   * its children without additional configuration. Defaults to false.
   * @var bool
   */
  public $suppressChildren = FALSE;


  /**
   * Constructs a Drupal\Component\Plugin\RiotTagPlugin object.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);

    $this->htmlTagName = $plugin_definition['htmlTagName'];
    $this->htmlTagAttributes = isset($plugin_definition['htmlTagAttributes'])
      ? $plugin_definition['htmlTagAttributes']
      : array();
    $this->childTags = isset($plugin_definition['childTags'])
      ? $plugin_definition['childTags']
      : array();
    $this->suppressChildren = isset($plugin_definition['suppressChildren'])
      ? $plugin_definition['suppressChildren']
      : array();
    $this->compiledTagPath = drupal_get_path('module', $plugin_definition['provider'])
      . '/' . $plugin_definition['compiledTagPath'];
  }
  /**
   * Return an HTML string appropriate for adding this tag to any site. For
   * headless/decoupled use cases.
   * @return string
   */
  public function toHTML($params = array()) {
    return drupal_render($this->toRenderArray());
  }

  /**
   * Creates a Drupal Render API array for use cases involving tighter
   * Drupal integration.
   */
  public function toRenderArray($params = array()) {
    system_theme();
    $options = $this->getTagOptions();
    $this->processOptions($options);
    $htmlid = drupal_html_id($this->htmlTagName);
    $manager = RiotTagPluginManager::create();
    $opts = $options['options'];
    $opts['children'] = $options['children'];
    $attached = array(
      'js' => array(),
    );
    $attached['js'][] = array(
      'data' => '(function($){$(document).ready(function() { riot.mount("#' .  $htmlid . '", ' . drupal_json_encode($opts) . '); });})(jQuery);',
      'type' => 'inline'
    );
    $manager->getInstanceJS($this, $attached);
    $atts = $this->getHTMLTagAttributes($params);
    $atts['id'] = $htmlid;
    $build = array(
      '#theme' => 'html_tag',
      '#tag' => $this->htmlTagName,
      '#attributes' => $atts,
      '#attached' => $attached,
    );
    return $build;
  }

  /**
   * Get information about this plugin instance.
   * @return mixed
   */
  public function getInfo() {
    return $this->getPluginDefinition();
  }

  /**
   * Recursively search the tag configuration optionset for this specific tags
   * options.
   * @return mixed
   */
  public function getTagOptions($params = array()) {
    if(empty($params)) {
      $params = $this->configuration['optionset_options']->options;
    }
    foreach($params as $key => $opt) {
      if($key === $this->getPluginId()) {
        return $opt;
      }
      else if(!empty($opt['children'])) {
        foreach($opt['children'] as $child_key => $child_opt) {
          if($copt = $this->getTagOptions(array($child_key => $child_opt))) {
            return $copt;
          }
        }
      }
    }
    return FALSE;
  }

  /**
   * Provides a list of text strings to be used in the tag UX for labels etc that
   * can be overridden in options and/or translated.
   * @return array
   */
  public function getTextOptions() {
    return array();
  }

  public function getHTMLTagAttributes($params = array()) {
    return $this->htmlTagAttributes;
  }

  public function getConfigForm($values) {
    $element = array();
    $txt = $this->getTextOptions();
    if(count($txt)) {
      $element['txt'] = array(
        '#type' => 'fieldset',
        '#title' => t('Interface Text'),
        '#collapsible' => TRUE,
        '#collapsed' => TRUE,
        '#description' => t('Interface text is meant to be customized in English and will be passed through Drupal translation functions in multi-lingual installations for display to end-users.')
      );
      foreach($txt as $key => $data) {
        $element['txt'][$key] = array(
          '#type' => 'textfield',
          '#title' => t($data['label']),
          '#description' => t($data['description']),
          '#default_value' => !isset($values['txt'][$key]) ? $data['default'] : $values['txt'][$key]
        );
      }
    }
    return $element;
  }

  public function processOptions(&$options) {
    if($options['plugin_enabled'] === 1) {
      $options['html_tag'] = $this->htmlTagName;
      if(!isset($options['options'])) {
        $options['options'] = array();
      }
      foreach($this->getTextOptions() as $o => $to) {
        $options['options']['txt'][$o] = t(isset($options['options']['txt'][$o]) ? $options['options']['txt'][$o] : $to['default']);
      }
      if(!empty($options['children'])) {
        $manager = RiotTagPluginManager::create();
        foreach($options['children'] as $id => $copts) {
          if($copts['plugin_enabled'] === 1) {
            /** @var RiotTagPlugin $child_instance */
            $child_instance = $manager->createInstance($id);
            $child_instance->processOptions($options['children'][$child_instance->getPluginId()]);
          }
          else {
            unset($options['children'][$id]);
          }
        }
      }
    }
  }

  public function getAttached(&$attached_array = array(), $configuration = array()) {
    $attached_array['js'][] = array('data' => $this->getCompiledTagPath());
    $attached_array['js'][] = drupal_get_path('module', 'riot_tags') . '/js/mixins/RiotTagsChildren.js';
    if(!isset($attached_array['libraries_load']) || !is_array($attached_array['libraries_load'])) {
      $attached_array['libraries_load'] = array();
    }
    $attached_array['libraries_load'][] = array('riotjs');
  }

  public function getConfiguration() {
    return $this->configuration;
  }

  public function getCompiledTagPath() {
    $tp = $this->compiledTagPath;
    $tagfile = basename($tp);
    $override_tp = drupal_get_path('theme', variable_get('theme_default', 'bartik')) . '/riottags/build/' . $tagfile;
    if(file_exists($override_tp)) {
      $tp = $override_tp;
    }
    return $tp;
  }
}
