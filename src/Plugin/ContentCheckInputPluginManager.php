<?php

namespace Drupal\content_check\Plugin;

use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Plugin\DefaultPluginManager;
use Drupal\content_check\Annotation\ContentCheckInput;

/**
 * Manages content check input plugins.
 */
class ContentCheckInputPluginManager extends DefaultPluginManager {

  /**
   * Constructs a new ContentCheckInputPluginManager.
   *
   * @param \Traversable $namespaces
   *   An object that implements \Traversable which contains the root paths
   *   keyed by the corresponding namespace to look for plugin implementations.
   * @param \Drupal\Core\Cache\CacheBackendInterface $cache_backend
   *   Cache backend instance to use.
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $module_handler
   *   The module handler.
   */
  public function __construct(\Traversable $namespaces, CacheBackendInterface $cache_backend, ModuleHandlerInterface $module_handler) {
    parent::__construct('Plugin/ContentCheckInput', $namespaces, $module_handler, ContentCheckInputInterface::class, ContentCheckInput::class);

    $this->alterInfo('content_check_input_info');
    $this->setCacheBackend($cache_backend, 'content_check_input_plugins');
  }

}
