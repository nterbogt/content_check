<?php

class LinkedContentCheck implements ContentCheckInterface {
  use ContentCheckHelperTrait;

  public static function isApplicable($entity_type) {
    return TRUE;
  }

  public function check($entity_type, $entity) {
    $results = array();
    $missing_items = array();

    list(, , $bundle) = entity_extract_ids($entity_type, $entity);

    if ($values = $this->getFilteredTextValues($entity_type, $entity)) {
      foreach ($values as $field_name => $items) {
        $missing_items[$field_name] = array();

        foreach ($items as $delta => $item) {
          $content = check_markup($item['value'], $item['format']);

          $dom_document = filter_dom_load($content);
          $dom_xpath = new DomXpath($dom_document);
          foreach ($dom_xpath->query("//*[@src]") as $dom_node) {
            $src = $dom_node->getAttribute('src');
            $response = drupal_http_request($src, array('method' => 'HEAD', 'timeout' => 10));
            if ($response->error) {
              $missing_items[$field_name][] = '<code>' . check_plain($dom_document->saveHtml($dom_node)) . '</code><br>' . check_plain($response->error) . ' (' . $response->code . ')';
            }
          }
          foreach ($dom_xpath->query("//*[@href]") as $dom_node) {
            $href = $dom_node->getAttribute('href');
            $response = drupal_http_request($href, array('method' => 'HEAD', 'timeout' => 10));
            if ($response->error) {
              $missing_items[$field_name][] = '<code>' . check_plain($dom_document->saveHtml($dom_node)) . '</code><br>' . check_plain($response->error) . ' (' . $response->code . ')';
            }
          }
        }
      }
    }

    if ($missing_items = array_filter($missing_items)) {
      foreach ($missing_items as $field_name => $items) {
        $instance = field_info_instance($entity_type, $field_name, $bundle);
        $missing_items[$field_name] = array(
          'data' => t('@label field (@name)', array('@label' => $instance['label'], '@name' => $field_name)),
          'children' => $items,
        );
      }
    }

    if (!empty($missing_items)) {
      $results[__CLASS__] = array(
        'title' => t('Linked content'),
        'value' => t('Failed'),
        'severity' => REQUIREMENT_ERROR,
        'description' => theme('item_list', array('items' => $missing_items)),
      );
    }

    return $results;
  }

}
