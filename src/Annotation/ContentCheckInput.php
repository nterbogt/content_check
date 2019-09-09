<?php

namespace Drupal\content_check\Annotation;

use Drupal\Component\Annotation\Plugin;

/**
 * Defines a content check plugin annotation object.
 *
 * Plugin namespace: Plugin\content_check.
 *
 * For a working example,
 * see \Drupal\content_check\Plugin\ContentCheck\UrlAliasCheck.
 *
 * @see \Drupal\content_check\ContentCheckInputInterface
 * @see \Drupal\content_check\ContentCheckInputBase
 * @see \Drupal\content_check\ContentCheckInputPluginManager
 * @see hook_content_check_input_info_alter()
 * @see plugin_api
 *
 * @Annotation
 */
class ContentCheckInput extends Plugin {

  /**
   * The plugin ID.
   *
   * @var string
   */
  public $id;

}
