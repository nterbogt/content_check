<?php

namespace Drupal\content_check\Element;

use Drupal\system\Element\StatusReportPage;

/**
 * Creates content check report page element.
 *
 * @RenderElement("content_check_report_page")
 */
class ContentCheckReportPage extends StatusReportPage {

  /**
   * {@inheritdoc}
   */
  public function getInfo() {
    $class = get_class($this);
    return [
      '#theme' => 'status_report_page',
      '#pre_render' => [
        [$class, 'preRenderCounters'],
        [$class, 'preRenderRequirements'],
      ],
    ];
  }

}
