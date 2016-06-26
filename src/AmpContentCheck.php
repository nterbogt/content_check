<?php

class AmpContentCheck implements ContentCheckInterface {

  public static function isApplicable($entity_type) {
    return $entity_type == 'node';
  }

  public function check($entity_type, $entity) {
    $results = array();

    if (in_array($entity->type, amp_get_enabled_types())) {
      $html = $this->renderAmp($entity);
      $amp = _amp_create_amp_converter();
      $amp->loadHtml($html);
      $amp->convertToAmpHtml();
      if ($warnings = $amp->warningsHumanText()) {
        if (strpos($warnings, 'PASS') === FALSE) {
          $results[__CLASS__] = array(
            'title' => t('AMP content'),
            'value' => t('Warnings found during conversion'),
            'severity' => REQUIREMENT_WARNING,
            'description' => '<pre>' . filter_xss($warnings) . '</pre>',
          );
        }
      }
    }

    return $results;
  }
  
  private function renderAmp($node) {
    $original_theme = $GLOBALS['theme'];
    UtilHelper::switchTheme(variable_get('amp_theme', 'ampsubtheme_example'));

    $build = node_view($node, 'amp');
    $html = drupal_render($build);

    UtilHelper::switchTheme($original_theme);
    return $html;
  }

}
