<?php

class UrlAliasCheck implements ContentCheckInterface {

  public static function isApplicable($entity_type) {
    return TRUE;
  }

  public function check($entity_type, $entity) {
    $results = array();
    $uri = entity_uri($entity_type, $entity);
    if (drupal_get_path_alias($uri['path']) === $uri['path']) {
      $results[__CLASS__] = array(
        'title' => t('URL alias'),
        'value' => t('Not set'),
        'severity' => REQUIREMENT_ERROR,
      );
    }
    return $results;
  }
}
