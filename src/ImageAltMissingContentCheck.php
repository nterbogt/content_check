<?php

class ImageAltMissingContentCheck implements ContentCheckInterface {
  use ContentCheckHelperTrait;

  public static function isApplicable($entity_type) {
    return TRUE;
  }

  private function getImageFieldValues($entity_type, $entity) {
    list(, , $bundle) = entity_extract_ids($entity_type, $entity);
    $values = array();
    $image_fields = $this->getFieldsByModule('image');
    $image_instances = array_intersect_key(field_info_instances($entity_type, $bundle), $image_fields);
    foreach ($image_instances as $field_name => $instance) {
      $values[$field_name] = field_get_items($entity_type, $entity, $field_name);
    }
    return array_filter($values);
  }

  public function check($entity_type, $entity) {
    $results = array();
    $missing_alt_items = array();

    list(, , $bundle) = entity_extract_ids($entity_type, $entity);

    if ($values = $this->getImageFieldValues($entity_type, $entity)) {
      foreach ($values as $field_name => $items) {
        $missing_alt_items[$field_name] = array();
        foreach ($items as $delta => $item) {
          if (empty($item['alt']) || $item['alt'] === '""') {
            $missing_alt_items[$field_name][] = check_plain($item['filename']);
          }
        }
      }
    }

    if ($values = $this->getFilteredTextValues($entity_type, $entity)) {
      foreach ($values as $field_name => $items) {
        $missing_alt_items[$field_name] = array();
        foreach ($items as $delta => $item) {
          $content = check_markup($item['value'], $item['format']);
          $dom_document = filter_dom_load($content);
          foreach ($dom_document->getElementsByTagName('img') as $dom_node) {
            if (!$dom_node->hasAttribute('alt') || $dom_node->getAttribute('alt') === '') {
              $missing_alt_items[$field_name][] = '<code>' . check_plain($dom_document->saveHtml($dom_node)) . '</code>';
            }
          }
        }
      }
    }

    if ($missing_alt_items = array_filter($missing_alt_items)) {
      foreach ($missing_alt_items as $field_name => $items) {
        $instance = field_info_instance($entity_type, $field_name, $bundle);
        $missing_alt_items[$field_name] = array(
          'data' => t('@label field (@name)', array('@label' => $instance['label'], '@name' => $field_name)),
          'children' => $items,
        );
      }
    }

    if (!empty($missing_alt_items)) {
      $results[__CLASS__] = array(
        'title' => t('Image alternate text'),
        'value' => t('Failed'),
        'severity' => REQUIREMENT_ERROR,
        'description' => theme('item_list', array('items' => $missing_alt_items)),
      );
    }

    return $results;
  }

}
