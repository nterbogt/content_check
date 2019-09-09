<?php

namespace Drupal\content_check\Plugin;

use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Plugin\PluginBase;
use Symfony\Component\DependencyInjection\ContainerInterface;

// Include to get access to REQUIREMENT_XX for check results.
require_once DRUPAL_ROOT . '/core/includes/install.inc';

/**
 * The initial base implementation of the ContentCheck interface.
 *
 * @package Drupal\content_check
 */
abstract class ContentCheckBase extends PluginBase implements ContentCheckInterface, ContainerFactoryPluginInterface {

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition
    );
  }

  /**
   * {@inheritdoc}
   */
  public function isApplicable($entity) {
    return TRUE;
  }

  /**
   * {@inheritdoc}
   */
  abstract public function check($item);

}
