<?php

namespace Drupal\content_check\Controller;

use Drupal\Component\Plugin\Exception\PluginException;
use Drupal\Component\Plugin\PluginManagerInterface;
use Drupal\content_check\Plugin\ContentCheckItem;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Routing\RouteMatchInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Base class for entity check controllers.
 */
class ContentCheckController extends ControllerBase {

  /**
   * The content check plugin manager.
   *
   * @var \Drupal\content_check\Plugin\ContentCheckPluginManager
   */
  protected $contentCheckPluginManager;

  /**
   * {@inheritdoc}
   */
  public function __construct(PluginManagerInterface $content_check_plugin_manager) {
    $this->contentCheckPluginManager = $content_check_plugin_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('plugin.manager.content_check.content_check')
    );
  }

  /**
   * Render the output of the checks applicable to this entity.
   *
   * @param \Drupal\Core\Routing\RouteMatchInterface $route_match
   *   The route match object to gain access to the entity.
   * @param string $entity_type_id
   *   The type of entity we're checking.
   *
   * @return array
   *   The render array containing the output for the page.
   */
  public function overview(RouteMatchInterface $route_match, $entity_type_id = NULL) {
    /** @var \Drupal\Core\Entity\ContentEntityInterface $entity */
    $entity = $route_match->getParameter($entity_type_id);

    $results = [];

    foreach ($this->contentCheckPluginManager->getDefinitions() as $id => $definition) {
      /** @var \Drupal\content_check\Plugin\ContentCheckInterface $instance */
      try {
        $instance = $this->contentCheckPluginManager->createInstance($id);
      }
      catch (PluginException $e) {
        continue;
      }

      if (!$instance->isApplicable($entity)) {
        continue;
      }

      // Run the test.
      $item = new ContentCheckItem($entity);
      $results[$id] = $instance->check($item);
    }

    return [
      '#type' => 'content_check_report_page',
      '#requirements' => $results,
    ];
  }

}
