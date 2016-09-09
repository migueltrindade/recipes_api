<?php
use \Drupal\riot_tags\RiotTagPluginManager;

/**
 * Define this Export UI plugin.
 */
$plugin = array(
  'schema' => 'riot_tags_optionset',
  'access' => 'administer riot tags',

  // Define the menu item.
  'menu' => array(
    'menu prefix' => 'admin/structure',
    'menu item' => 'riot-tags',
    'menu title' => 'RiotJS Tag Configuration',
    'menu description' => 'Create and export embeddable RiotJS tags.',
  ),

  // Define user interface texts.
  'title singular' => t('optionset'),
  'title plural' => t('optionsets'),
  'title singular proper' => t('RiotJS Tag Optionset'),
  'title plural proper' => t('RiotJS Tag Optionsets'),

  // Define the names of the functions that provide the add/edit forms.
  'form' => array(
    'settings' => 'riot_tags_optionset_ctools_export_ui_form',
    'submit' => 'riot_tags_optionset_ctools_export_ui_form_submit'
  ),
);

/**
 * Define the preset add/edit form.
 */
function riot_tags_optionset_ctools_export_ui_form(&$form, &$form_state) {

  /**
   * @var \Drupal\riot_tags\RiotTagPluginManager  $manager
   */
  $manager = RiotTagPluginManager::create();

  $form['#prefix'] = '<div id="messages"></div>';

  $preset = $form_state['item'];
  $form['title'] = array(
    '#type' => 'textfield',
    '#title' => t('Human Readable Name'),
    '#default_value' => isset($preset->title)
      ? $preset->title
      : '',
    '#required' => TRUE,
  );

  $values = isset($preset->options) ? $preset->options : array();
  $plugin_options = $manager->pluginsAsOptions();
  if(!isset($form_state['values']['tag_plugin'])) {
    $active_plugin = !empty($preset->tag_plugin)
      ? $preset->tag_plugin
      : FALSE;
  }
  else {
    $active_plugin = !empty($form_state['values']['tag_plugin'])
      ? $form_state['values']['tag_plugin']
      : FALSE;
  }

  $form['tag_plugin'] = array(
    '#type' => 'select',
    '#title' => t('Select a tag type.'),
    '#options' => $plugin_options,
    '#required' => TRUE,
    '#ajax' => array(
      'callback' => 'riot_tags_optionset_ajax'
    ),
    '#prefix' => '<div id="riot-tags-plugins">',
    '#suffix' => '</div>',
  );

  if(isset($preset->tag_plugin)) {
    $form['tag_plugin']['#default_value'] = $preset->tag_plugin;
  }

  $eopts = array('' => t('- Select -'));
  foreach(entity_get_info() as $et => $ei) {
    $eopts[$et] = t($ei['label']);
  }
  $form['options'] = array(
    '#title' => t('Tag Options'),
    '#type' => 'fieldset',
    '#tree' => TRUE,
    '#prefix' => '<div id="riot-tags-plugin-options">',
    '#suffix' => '</div>',
  );
  if(!empty($active_plugin)) {

    $form['options'][$active_plugin]['entity_attach'] = array(
      '#type' => 'select',
      '#title' => t('Attach as Extra Field'),
      '#options' => $eopts,
      '#description' => t('Attach this optionset as an extra field to the selected entity type.  The ID of the entity will be passed in opts.eid.'),
      '#default_value' => $values[$active_plugin]['entity_attach']
    );
    /** @var \Drupal\riot_tags\Plugin\RiotTag\RiotTagPlugin $instance */
    $instance = $manager->createInstance($active_plugin);
    $plugin_form = array();
    $manager->getInstanceConfigForm($instance, $values, $plugin_form);
    $form['options'] = array_replace_recursive($form['options'], $plugin_form);
  }
}

/**
 * AJAX Callback for replacing multiple form sections.
 * @param $form
 * @param $form_state
 * @return array
 */
function riot_tags_optionset_ajax($form, &$form_state) {
  $commands = array();
  $commands[] = ajax_command_replace(
    '#messages',
    '<div id="messages">' . theme('status_messages') . '</div>'
  );
  $commands[] = ajax_command_replace(
    '#riot-tags-plugin-options',
    render($form['options'])
  );
  return array('#type' => 'ajax', '#commands' => $commands);
}

/**
 * Default submit handler. Recursively calls form submit handlers.
 */
function riot_tags_optionset_ctools_export_ui_form_submit($form, &$form_state) {
  $active_plugin = $form_state['values']['tag_plugin'];
  $manager = RiotTagPluginManager::create();
  /** @var \Drupal\riot_tags\Plugin\RiotTag\RiotTagPlugin $instance */
  $instance = $manager->createInstance($active_plugin);
  $manager->callSubmitHandlers($instance, $form_state['values']['options']);
}
