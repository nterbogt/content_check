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

    list(, , $bundle) = entity_extract_ids($entity_type, $entity);

    $missing_alt_fields = array();
    if ($values = $this->getImageFieldValues($entity_type, $entity)) {
      foreach ($values as $field_name => $items) {
        foreach ($items as $delta => $item) {
          if (empty($item['alt']) || $item['alt'] === '""') {
            if (!isset($insecure_fields[$field_name])) {
              $instance = field_info_instance($entity_type, $field_name, $entity->type);
              $missing_alt_fields[$field_name] = array(
                'data' => t('@label field (@name)', array('@label' => $instance['label'], '@name' => $field_name)),
                'children' => array(),
              );
            }
            $missing_alt_fields[$field_name]['children'] = array_merge($missing_alt_fields[$field_name]['children'], array($item['filename']));
          }
        }
      }
    }

    if ($values = $this->getFilteredTextValues($entity_type, $entity)) {
      foreach ($values as $field_name => $items) {
        foreach ($items as $delta => $item) {
          $content = check_markup($item['value'], $item['format']);

          $missing_alt_images = array();
          $dom_document = filter_dom_load($content);
          foreach ($dom_document->getElementsByTagName('img') as $dom_node) {
            if (!$dom_node->hasAttribute('alt') || $dom_node->getAttribute('alt') === '') {
              $missing_alt_images[] = check_plain($dom_document->saveHtml($dom_node));
            }
          }

          if (!empty($missing_alt_images)) {
            if (!isset($insecure_fields[$field_name])) {
              $instance = field_info_instance($entity_type, $field_name, $entity->type);
              $missing_alt_fields[$field_name] = array(
                'data' => t('@label field (@name)', array('@label' => $instance['label'], '@name' => $field_name)),
                'children' => array(),
              );
            }
            $missing_alt_fields[$field_name]['children'] = array_merge($missing_alt_fields[$field_name]['children'], $missing_alt_images);
          }
        }
      }
    }

    if (!empty($missing_alt_fields)) {
      $results[__CLASS__] = array(
        'title' => t('Image alternate text'),
        'value' => t('Failed'),
        'severity' => REQUIREMENT_ERROR,
        'description' => t('Images wihout alternate text were found in the following fields: !items', array('!items' => theme('item_list', array('items' => $missing_alt_fields)))),
      );
    }

    return $results;
  }

}
