<?php

namespace Drupal\riot_tags\Plugin\RiotTag;

class RiotTagsClassToggle extends RiotTagPlugin {
  public function getConfigForm($values) {
    $element = parent::getConfigForm($values);
    $element['class_config'] = array(
      '#type' => 'textarea',
      '#title' => t('Class Toggle Config'),
      '#description' => t('Enter class toggle options 1 per line. class|active label|inactive label|default eg. <br />map-active|Hide Map|Show Map|true'),
      '#default_value' => isset($values['class_config'])
        ? $values['class_config']
        : '',
    );
    $element['multi'] = array(
      '#type' => 'checkbox',
      '#title' => t('Allow Multiple Active Classes'),
      '#default_value' => isset($values['multi'])
        ? $values['multi']
        : 0,
    );
    $element['empty'] = array(
      '#type' => 'checkbox',
      '#title' => t('Allow No Active Classes'),
      '#default_value' => isset($values['empty'])
        ? $values['empty']
        : 0,
    );
    return $element;
  }

  public function processOptions(&$options) {
    parent::processOptions($options);
    if(empty($options['options']['class_config'])) {
      return;
    }
    $opts = $this->explodeString($options['options']['class_config']);
    $options['options']['class_config'] = $opts;
  }

  private function explodeString($string) {
    $array = array();
    $data = trim($string);
    if ($data != '') {
      $data = trim(preg_replace('/\s\s+/', '^^', $string));
      $array = explode('^^', $data);
      // Trim after as well.
      foreach ($array as $a => $v) {
        $vals = explode('|', $v);
        $opts = array(
          'class' => $vals[0],
          'label_active' => t($vals[1]),
          'label_inactive' => t($vals[2]),
          'default' => $vals[3]
        );
        $array[$a] = $opts;
      }
    }

    return $array;
  }
}
