<?php

namespace Drupal\content_check\Plugin;

/**
 * The interface for a ContentCheck plugin.
 *
 * @package Drupal\content_check
 */
interface ContentCheckInterface {

  /**
   * Check whether the test is applicable to this entity.
   *
   * @param \Drupal\Core\Entity\ContentEntityInterface $entity
   *   The entity to check.
   *
   * @return bool
   *   Whether the test is applicable.
   */
  public function isApplicable(ContentEntityInterface $entity);

  /**
   * Perform the check on the item.
   *
   * @param \Drupal\content_check\Plugin\ContentCheckItem $item
   *   The item to test.
   *
   * @return array
   *   Return the format as per hook_requirements().
   */
  public function check(ContentCheckItem $item);

}
