<?php

class InsecureContentCheck implements ContentCheckInterface {
  use ContentCheckHelperTrait;

  public static function isApplicable($entity_type) {
    return TRUE;
    return (bool) variable_get('https');
  }

  public function check($entity_type, $entity) {
    $results = array();

    list(, , $bundle) = entity_extract_ids($entity_type, $entity);

    if ($values = $this->getFilteredTextValues($entity_type, $entity)) {
      // Switch some global variables around to make sure that check_markup()
      // will generate secure content.
      //$original_https = variable_get('https', FALSE);
      $original_base_url = $GLOBALS['base_url'];
      $GLOBALS['base_url'] = str_replace('http://', 'https://', $GLOBALS['base_url']);

      $insecure_fields = array();
      foreach ($values as $field_name => $items) {
        foreach ($items as $delta => $item) {
          $content = check_markup($item['value'], $item['format']);

          $insecure_items = array();
          $dom_document = filter_dom_load($content);
          $dom_xpath = new DomXpath($dom_document);
          foreach ($dom_xpath->query("//*[starts-with(@src, 'http://')]") as $dom_node) {
            $insecure_items[] = check_plain($dom_document->saveHtml($dom_node));
          }

          if (!empty($insecure_items)) {
            if (!isset($insecure_fields[$field_name])) {
            $instance = field_info_instance($entity_type, $field_name, $bundle);
              $insecure_fields[$field_name] = array(
                'data' => t('@label field (@name)', array('@label' => $instance['label'], '@name' => $field_name)),
                'children' => array(),
              );
            }
            $insecure_fields[$field_name]['children'] = array_merge($insecure_fields[$field_name]['children'], $insecure_items);
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
