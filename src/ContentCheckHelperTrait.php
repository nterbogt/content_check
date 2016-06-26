<?php

trait ContentCheckHelperTrait {

  public function getFieldsByModule($module) {
    $fields = field_info_fields();
    return array_filter($fields, function($field) use ($module) {
      return $field['module'] == $module;
    });
  }

  public function getInstancesByType($entity_type, $bundle, array $types) {
    $instances = field_info_instances($entity_type, $bundle);
    return array_filter($instances, function($instance) use ($types) {

    });
  }

  public function getFilteredTextValues($entity_type, $entity) {
    list(, , $bundle) = entity_extract_ids($entity_type, $entity);
    $values = array();
    $text_fields = $this->getFieldsByModule('text');
    $text_instances = array_intersect_key(field_info_instances($entity_type, $bundle), $text_fields);
    foreach ($text_instances as $field_name => $instance) {
      if (!empty($instance['settings']['text_processing'])) {
        $values[$field_name] = field_get_items($entity_type, $entity, $field_name);
      }
    }
    return array_filter($values);
  }

}
