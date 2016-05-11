<?php

abstract class FilteredTextFieldCheckBase implements ContentCheckInterface {

  public function getValues($entity_type, $entity) {
    list(, , $bundle) = entity_extract_ids($entity_type, $entity);
    $values = array();
    $instances = field_info_instances($entity_type, $bundle);
    foreach ($instances as $field_name => $instance) {
      $field = field_info_field($field_name);
      if ($field['module'] == 'text' && !empty($instance['settings']['text_processing'])) {
        $values[$field_name] = field_get_items($entity_type, $entity, $field['field_name']);
      }
    }
    return array_filter($values);
  }

}
