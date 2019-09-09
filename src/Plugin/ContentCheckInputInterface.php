<?php

namespace Drupal\content_check\Plugin;

/**
 * The interface for a ContentCheck plugin.
 *
 * @package Drupal\content_check
 */
interface ContentCheckInputInterface {

  /**
   * Check whether the test is applicable to this entity.
   *
   * @param \Drupal\content_check\Plugin\ContentCheckItem $item
   *   The entity to check.
   *
   * @return mixed
   *   The data object that will be returned when content check item is queried.
   */
  public function getData(ContentCheckItem $item);

}
