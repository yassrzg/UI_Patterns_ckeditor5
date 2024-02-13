<?php

namespace Drupal\ui_patterns_ckeditor5\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\ui_patterns\UiPatternsManager;
use Drupal\ui_patterns\UiPatternsManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Class PatternsLibraryController.
 *
 * @package Drupal\ui_patterns_library\Controller
 */
class UiPatternsCkeditorApi extends ControllerBase {

  /**
   * Patterns manager service.
   *
   * @var \Drupal\ui_patterns\UiPatternsManager
   */
  protected $patternsManager;

  /**
   * @var \Drupal\ui_patterns\UiPatternsManagerInterface
   */
  protected UiPatternsManagerInterface $patternsManagerInterface;

  /**
   * {@inheritdoc}
   */
  public function __construct(UiPatternsManager $ui_patterns_manager, UiPatternsManagerInterface $ui_patterns_manager_interface) {
    $this->patternsManager = $ui_patterns_manager;
    $this->patternsManagerInterface = $ui_patterns_manager_interface;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static($container->get('plugin.manager.ui_patterns')
      , $container->get('plugin.manager.ui_patterns'));
  }

  /**
   * Get all patterns as JSON.
   *
   * @return JsonResponse
   *   JSON response containing all patterns.
   */
  public function getAllPatterns(): JsonResponse
  {
    $patterns = [];
    foreach ($this->patternsManager->getGroupedDefinitions() as $groupName => $groupedDefinitions) {
      foreach ($groupedDefinitions as $definition) {
        $patterns[] = $definition->toArray();
      }
    }
//    dd($patterns);
//    $grouped_plugin_definitions = $this->patternsManager->getSortedDefinitions();
    $grouped_plugin_definitions = $this->patternsManager->getPatternsOptions();
//    dd($grouped_plugin_definitions);
//    $accordionSettings = $this->patternsManager->getDefinition('accordion')->getAdditional()['settings'];
//    dd($accordionSettings);

//    dd($patterns, $grouped_plugin_definitions);
    $yass = \Drupal::service('plugin.manager.ui_patterns')->getDefinitions();

    dd($yass);
    return new JsonResponse($patterns);
  }
}
