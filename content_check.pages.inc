<?php

function content_check_results_page($entity_type, $entity) {
  require_once DRUPAL_ROOT . '/includes/install.inc';
  drupal_add_css(drupal_get_path('module', 'system') . '/system.admin.css');
  $results = content_check_process_checks($entity_type, $entity);
  if (empty($results)) {
    return MENU_NOT_FOUND;
  }
  return theme('status_report', array('requirements' => $results));
}
