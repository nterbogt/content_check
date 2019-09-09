<?php

namespace Drupal\content_check\Plugin\ContentCheckInput;

use Drupal\content_check\Plugin\ContentCheckInputBase;

/**
 * Render an entity using the full view and return HTML.
 *
 * @ContentCheckInput(
 *   id = "rendered_entity_full_view",
 * )
 */
class RenderedEntityFullView extends ContentCheckInputBase {

  /**
   * {@inheritdoc}
   */
  public function getData($item) {
    $view_builder = \Drupal::entityTypeManager()->getViewBuilder($item->getEntity()->getEntityTypeId());
    $build = $view_builder->view($item->getEntity(), 'full', $item->getEntity()->language()->getId());
    return render($build);
  }

}
