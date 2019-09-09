<?php

namespace Drupal\content_check\Plugin;

use Drupal\Core\Plugin\PluginBase;

/**
 * The initial base implementation of the ContentCheck interface.
 *
 * @package Drupal\content_check
 */
abstract class ContentCheckInputBase extends PluginBase implements ContentCheckInputInterface {

  /**
   * {@inheritdoc}
   */
  abstract public function getData($item);

}
