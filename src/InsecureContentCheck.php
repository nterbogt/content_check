<?php

class InsecureContentCheck extends FilteredTextFieldCheckBase {

  public static function isApplicable($entity_type) {
    return TRUE;
    return (bool) variable_get('https');
  }

  public function check($entity_type, $entity) {
    $results = array();

    if ($values = $this->getValues($entity_type, $entity)) {
      // Switch some global variables around to make sure that check_markup()
      // will generate secure content.
      //$original_https = variable_get('https', FALSE);
      $original_base_url = $GLOBALS['base_url'];
      $GLOBALS['base_url'] = str_replace('http://', 'https://', $GLOBALS['base_url']);

      $secure_fields = array();
      $insecure_fields = array();
      foreach ($values as $field_name => $items) {
        foreach ($items as $delta => $item) {
          $content = check_markup($item['value'], $item['format']);
          if (preg_match_all('/src=\"(http:\/\/[^"]+)/', $content, $matches)) {
            if (!isset($insecure_fields[$field_name])) {
              $insecure_fields[$field_name] = array(
                'data' => t('Field @name', array('@name' => $field_name)),
                'children' => array(),
              );
            }
            $insecure_fields[$field_name]['children'] = array_merge($insecure_fields[$field_name]['children'], $matches[1]);
          }
        }
      }

      if (!empty($insecure_fields)) {
        $results[__CLASS__] = array(
          'title' => t('Insecure content'),
          'value' => t('Failed'),
          'severity' => REQUIREMENT_ERROR,
          'description' => t('Insecure content was found in the following fields: !items', array('!items' => theme('item_list', array('items' => $insecure_fields)))),
        );
      }

      // Reset the global variables.
      //$GLOBALS['conf']['https'] = $original_https;
      $GLOBALS['base_url'] = $original_base_url;
    }

    return $results;
  }

}
