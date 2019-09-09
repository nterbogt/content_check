<?php

namespace Drupal\content_check\Plugin\ContentCheckInput;

use Drupal\content_check\Plugin\ContentCheckInputBase;
use Drupal\Component\Utility\Html;

/**
 * Render an entity using the full view and return the DOM.
 *
 * @ContentCheckInput(
 *   id = "rendered_entity_full_view_dom",
 * )
 */
class RenderedEntityFullViewDom extends ContentCheckInputBase {

  /**
   * {@inheritdoc}
   */
  public function getData($item) {
    $html = $item->getInput('rendered_entity_full_view');
    return Html::load($html);
  }

}
