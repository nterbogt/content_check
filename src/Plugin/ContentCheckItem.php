<?php

namespace Drupal\content_check\Plugin;

use Drupal\Core\Entity\ContentEntityInterface;

/**
 * Wrap an entity in cacheable variants for different tests.
 *
 * @package Drupal\content_check
 */
class ContentCheckItem {

  /**
   * Entity that all variants are based off.
   *
   * @var \Drupal\Core\Entity\ContentEntityInterface
   */
  protected $entity;

  /**
   * Storage for the rendered input plugins.
   *
   * @var array
   */
  protected $inputCache;

  /**
   * ContentCheckItem constructor.
   *
   * @param \Drupal\Core\Entity\ContentEntityInterface $entity
   *   The entity to base this item from.
   */
  public function __construct(ContentEntityInterface $entity) {
    $this->entity = $entity;
    $this->inputCache = [];
  }

  /**
   * The initial base entity.
   *
   * @return \Drupal\Core\Entity\ContentEntityInterface
   *   The base entity for this item.
   */
  public function getEntity() {
    return $this->entity;
  }

  /**
   * Get the data from an input plugin.
   *
   * @param string $plugin
   *   The ID of the input plugin to get.
   *
   * @return mixed
   *   The data returned by the plugin.
   */
  public function getInput($plugin) {
    if (isset($input_cache[$plugin])) {
      return $this->inputCache[$plugin];
    }

    /** @var \Drupal\content_check\Plugin\ContentCheckInputPluginManager $input_plugin_manager */
    try {
      $input_plugin_manager = \Drupal::service('plugin.manager.content_check.content_check_input');
      $instance = $input_plugin_manager->createInstance($plugin);
      $this->inputCache[$plugin] = $instance->getData($this);
    }
    catch (\Exception $e) {
      return NULL;
    }

    return $this->inputCache[$plugin];
  }

}
