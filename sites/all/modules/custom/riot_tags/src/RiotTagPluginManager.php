<?php

/**
 * @file
 * Contains \Drupal\riot_tags\RiotTagPluginManager.
 */

namespace Drupal\riot_tags;

use \Drupal\Core\Plugin\DefaultPluginManager;
use \Drupal\Core\Plugin\Factory\ContainerFactory;
use \Drupal\Core\Plugin\Discovery\YamlDiscovery;
use \Drupal\plug\Util\Module;

/**
 * Name plugin manager.
 */
class RiotTagPluginManager extends DefaultPluginManager {

  /**
   * {@inheritdoc}
   */
  protected $defaults = array(
    // Human readable label for the RiotTag.
    'label' => '',
    'htmlTagName' => '',
    'compiledTagPath' => FALSE,
    'class' => 'Drupal\riot_tags\Plugin\RiotTag\RiotTagPlugin',
    'id' => '',
  );

  /**
   * Static cache for formatting plugin instances into key/value pairs
   * suitable for use with the Form API.
   * @var array
   */
  public $fapiOptions = array();

  /**
   * Constructs RiotTagPluginManager.
   *
   * @param \Traversable $namespaces
   *   An object that implements \Traversable which contains the root paths
   *   keyed by the corresponding namespace to look for plugin implementations.
   * @param \DrupalCacheInterface $cache_backend
   *   Cache backend instance to use.
   */
  public function __construct(\Traversable $namespaces, \DrupalCacheInterface $cache_backend) {
    parent::__construct(FALSE, $namespaces);
    $this->discovery = new YamlDiscovery('RiotTags', Module::getDirectories());
    $this->factory = new ContainerFactory($this);
    $this->alterInfo('riot_tag_plugin');
    $this->setCacheBackend($cache_backend, 'RiotTag_plugins');
  }

  /**
   * RiotTagPluginManager factory method.
   *
   * @param string $bin
   *   The cache bin for the plugin manager.
   *
   * @return RiotTagPluginManager
   *   The created manager.
   */
  public static function create($bin = 'cache') {
    return new static(Module::getNamespaces(), _cache_get_object($bin));
  }

  /**
   * Builds an array of FAPI compatible options for select lists and
   * checkboxes.
   * @return array
   */
  public function pluginsAsOptions() {
    if(!empty($this->fapiOptions)) {
      return $this->fapiOptions;
    }
    foreach($this->getDefinitions() as $id => $plugin) {
      $this->fapiOptions[$id] = $plugin['label'];
    }
    return $this->fapiOptions;
  }

  /**
   * @param \Drupal\riot_tags\Plugin\RiotTag\RiotTagPlugin $plugin_instance
   * @param $form_values
   * @param array $form_api_array
   * @return array
   */
  public function getInstanceConfigForm($plugin_instance, $form_values, &$form_api_array = array()) {
    $plugin_id = $plugin_instance->getPluginId();
    $values = $form_values[$plugin_id]['options'];
    $id = drupal_html_id($plugin_id);
    $form_api_array[$plugin_id] = array(
      '#type' => 'fieldset',
//      '#title' => t('Options for @plug', array('@plug' => $plugin_id)),
      '#tree' => TRUE,
      '#attributes' => array(
        'id' => $id
      ),
    );
    $states = array(
      'visible' => array(
        '#'.$id.' :input[name*="' . $plugin_id .'\\]\\[plugin_enabled"]' => array(
          'checked' => TRUE,
        ),
      ),
    );
    $form_api_array[$plugin_id]['options'] = array(
      '#type' => 'container',
      '#states' => $states,
    );

    $form_api_array[$plugin_id]['options'] += $plugin_instance->getConfigForm($values);
    
    $form_api_array[$plugin_id]['plugin_enabled'] = array(
      '#type' => 'checkbox',
      '#title' => t('Enable @plug', array('@plug' => $plugin_id)),
      '#weight' => -99999,
      '#default_value' => isset($form_values[$plugin_id]['plugin_enabled'])
        ? $form_values[$plugin_id]['plugin_enabled']
        : FALSE,
    );
    $form_api_array[$plugin_id]['#weight'] = !empty($form_values[$plugin_id]['plugin_enabled']) ? $form_values[$plugin_id]['plugin_weight'] : 999;
    $form_api_array[$plugin_id]['plugin_weight'] = array(
      '#type' => 'select',
      '#title' => t('Plugin Weight'),
      '#weight' => -99998,
      '#default_value' => isset($form_values[$plugin_id]['plugin_weight'])
        ? $form_values[$plugin_id]['plugin_weight']
        : 0,
      '#states' => $states,
      '#options' => array_combine(range(-20, 20), range(-20, 20)),
    );
    $form_api_array[$plugin_id]['plugin_id'] = array(
      '#type' => 'hidden',
      '#value' => $plugin_id,
    );
    if(!empty($plugin_instance->childTags) && !$plugin_instance->suppressChildren) {
      $form_api_array[$plugin_id]['children'] = array(
        '#states' => $states,
        '#type' => 'container',
      );
      foreach($plugin_instance->childTags as $tag) {
        /** @var \Drupal\riot_tags\Plugin\RiotTag\RiotTagPlugin $child_instance */
        $child_instance = $this->getFactory()->createInstance($tag);
        $this->getInstanceConfigForm(
          $child_instance,
          $form_values[$plugin_id]['children'],
          $form_api_array[$plugin_id]['children']
        );
      }
    }
  }

  /**
   * @param \Drupal\riot_tags\Plugin\RiotTag\RiotTagPlugin $plugin_instance
   * @param array $attached_js
   * @return array
   */
  public function getInstanceJS($plugin_instance, &$attached_js = array()) {
    $attached_js[] = array('data' => $plugin_instance->getCompiledTagPath());
    $plugin_instance->getAttached($attached_js);
    if(!empty($plugin_instance->childTags)) {
      foreach($plugin_instance->childTags as $tag) {
        /** @var \Drupal\riot_tags\Plugin\RiotTag\RiotTagPlugin $child_instance */
        $child_instance = $this->getFactory()->createInstance($tag, $plugin_instance->getConfiguration());
        $this->getInstanceJS($child_instance, $attached_js);
      }
    }
    return $attached_js;
  }

  /**
   * @param \Drupal\riot_tags\Plugin\RiotTag\RiotTagPlugin $instance
   * @param array $values
   */
  public function callSubmitHandlers($instance, &$values) {
    if(method_exists($instance, 'configFormSubmit')) {
      $instance->configFormSubmit($values[$instance->getPluginId()]['options']);
    }

    if(!empty($instance->childTags)) {
      foreach($instance->childTags as $tag) {
        /** @var \Drupal\riot_tags\Plugin\RiotTag\RiotTagPlugin $child_instance */
        $child_instance = $this->getFactory()->createInstance($tag);
        $this->callSubmitHandlers(
          $child_instance,
          $values
        );
      }
    }
  }
}
