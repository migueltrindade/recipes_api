<?php
/**
 * @file
 * Contains \Drupal\riot_tags\Plugin\RiotTag\RiotTagInterface
 */

namespace Drupal\riot_tags\Plugin\RiotTag;

interface RiotTagInterface {

  /**
   * Returns plain old HTML for use anywhere on the web.
   * @param array $params
   * @return mixed
   */
  public function toHTML($params = array());

  /**
   * Build a renderable array that can easily be altered/used in drupal
   * @param array $params
   * @return mixed
   */
  public function toRenderArray($params = array());

  /**
   * @return mixed
   */
  public function getTagOptions();
}
