<?php

namespace Drupal\content_check\Plugin\ContentCheck;

use DOMXPath;
use Drupal\content_check\Plugin\ContentCheckBase;
use GuzzleHttp\Client;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Check external links are to valid resources.
 *
 * @ContentCheck(
 *   id = "content_check_links",
 *   label = @Translation("Link checker"),
 * )
 */
class LinkedContentCheck extends ContentCheckBase {

  /**
   * The HTTP client.
   *
   * @var \Guzzle\Http\Client
   */
  protected $httpClient;

  /**
   * UrlAliasCheck constructor.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin ID for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Guzzle\Http\Client $http_client
   *   The http client to use to perform the link checks.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, Client $http_client) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);

    $this->httpClient = $http_client;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('http_client')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function check($item) {
    $dom_xpath = new DomXpath($item->getInput('rendered_entity_full_view_dom'));
    foreach ($dom_xpath->query("//*[@src]") as $dom_node) {
      $src = $dom_node->getAttribute('src');
      $urls[] = $src;
    }
    foreach ($dom_xpath->query("//*[@href]") as $dom_node) {
      $href = $dom_node->getAttribute('href');
      $urls[] = $href;
    }

    $urls = array_unique($urls);
    $urls = array_filter($urls, function ($url) {
      return strpos($url, '://') !== FALSE;
    });

    $broken_urls = [];
    foreach ($urls as $url) {
      try {
        $this->httpClient->get($url);
      }
      catch (\Exception $e) {
        $broken_urls[] = $url;
      }
    }

    $severity = count($broken_urls) > 0 ? REQUIREMENT_ERROR : REQUIREMENT_INFO;

    $result = [
      'title' => $this->t('Linked content'),
      'description' => [
        ['#markup' => $this->t('Checking that links to external sources are active.')],
        [
          '#theme' => 'item_list',
          '#list_type' => 'ul',
          '#items' => $broken_urls,
        ],
      ],
      // TODO: Format plural.
      'value' => $this->t('Checked :count links', [
        ':count' => count($urls),
      ]),
      'severity' => $severity,
    ];

    return $result;
  }

}
