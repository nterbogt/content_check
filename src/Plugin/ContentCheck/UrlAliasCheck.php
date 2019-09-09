<?php

namespace Drupal\content_check\Plugin\ContentCheck;

use Drupal\content_check\Plugin\ContentCheckBase;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Path\AliasManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Check whether an alias has been used for this content.
 *
 * @ContentCheck(
 *   id = "content_check_url_alias",
 *   label = @Translation("URL Alias"),
 * )
 */
class UrlAliasCheck extends ContentCheckBase {

  /**
   * The module handler.
   *
   * @var \Drupal\Core\Extension\ModuleHandlerInterface
   */
  protected $moduleHandler;

  /**
   * The alias manager.
   *
   * @var \Drupal\Core\Path\AliasManagerInterface
   */
  protected $aliasManager;

  /**
   * UrlAliasCheck constructor.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin ID for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $module_handler
   *   The module handler service.
   * @param \Drupal\Core\Path\AliasManagerInterface $alias_manager
   *   The alias manager service.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, ModuleHandlerInterface $module_handler, AliasManagerInterface $alias_manager) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);

    $this->moduleHandler = $module_handler;
    $this->aliasManager = $alias_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('module_handler'),
      $container->get('path.alias_manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function isApplicable($entity) {
    if (!$this->moduleHandler->moduleExists('path')) {
      return FALSE;
    }

    return parent::isApplicable($entity);
  }

  /**
   * {@inheritdoc}
   */
  public function check($item) {
    $entity = $item->getEntity();

    $base_path = $entity->toUrl()->setOption('path_processing', FALSE)->toString();
    $alias_path = $this->aliasManager->getAliasByPath($base_path, $entity->language()->getId());
    $severity = ($base_path === $alias_path) ? REQUIREMENT_WARNING : REQUIREMENT_INFO;

    $result = [
      'title' => $this->t('URL Alias'),
      'description' => $this->t('Use of a URL alias is recommended. This will improve your user experience and SEO.'),
      'value' => $alias_path,
      'severity' => $severity,
    ];

    return $result;
  }

}
