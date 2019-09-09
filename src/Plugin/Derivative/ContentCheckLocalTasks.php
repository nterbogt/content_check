<?php

namespace Drupal\content_check\Plugin\Derivative;

use Drupal\Component\Plugin\Derivative\DeriverBase;
use Drupal\Component\Plugin\Derivative\DeriverInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;

/**
 * Provides dynamic local tasks for content check.
 */
class ContentCheckLocalTasks extends DeriverBase implements DeriverInterface {
  use StringTranslationTrait;

  /**
   * {@inheritdoc}
   */
  public function getDerivativeDefinitions($base_plugin_definition) {
    // Create tabs for all possible entity types.
    $entity_types = \Drupal::entityTypeManager()->getDefinitions();
    foreach ($entity_types as $entity_type_id => $entity_type) {
      // Find the route name for the translation overview.
      $check_route_name = "entity.$entity_type_id.content_check_overview";

      $base_route_name = "entity.$entity_type_id.canonical";
      $this->derivatives[$check_route_name] = [
        'entity_type' => $entity_type_id,
        'title' => $this->t('Check'),
        'route_name' => $check_route_name,
        'base_route' => $base_route_name,
      ] + $base_plugin_definition;
    }
    return parent::getDerivativeDefinitions($base_plugin_definition);
  }

}
