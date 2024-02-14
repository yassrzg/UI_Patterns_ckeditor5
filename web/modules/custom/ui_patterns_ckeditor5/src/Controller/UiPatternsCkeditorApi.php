<?php

namespace Drupal\ui_patterns_ckeditor5\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\ui_patterns\Definition\PatternDefinition;
use Drupal\ui_patterns\UiPatterns;
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

    $theme = \Drupal::theme()
      ->getActiveTheme()
      ->getName();
    $libraries_themes = \Drupal::service('library.discovery')->getLibrariesByExtension($theme);
//    dd($libraries_themes);
    $build = [
      '#theme' => 'my_theme_hook',
      '#attached' => [
        'css' => [],
        'js' => [],
      ],
    ];
      // Attach JS libraries.
    foreach ($libraries_themes as $library_name => $library_info) {
      // Attacher les bibliothèques JS.
      if (isset($library_info['js'])) {
        foreach ($library_info['js'] as $js) {
          $build['#attached']['js'][] = $js['data'];
        }
      }

      // Attach css libraries.
      if (isset($library_info['css'])) {
        foreach ($library_info['css'] as $css) {
          $build['#attached']['css'][] = $css['data'];
        }
      }
    }

    $build['#attached']['library'][] = 'ui_patterns_ckeditor5/patterns';
    return new JsonResponse($libraries_themes);
  }

//  public function getAllPatterns(): JsonResponse
//  {
//    $patterns = [];
//    foreach ($this->patternsManager->getGroupedDefinitions() as $groupName => $groupedDefinitions) {
//      foreach ($groupedDefinitions as $definition) {
//        $patterns[] = $definition->toArray();
//      }
//    }
////    dd($patterns);
////    $grouped_plugin_definitions = $this->patternsManager->getSortedDefinitions();
//    $grouped_plugin_definitions = $this->patternsManager->getPatternsOptions();
////    dd($grouped_plugin_definitions);
////    $accordionSettings = $this->patternsManager->getDefinition('accordion')->getAdditional()['settings'];
////    dd($accordionSettings);
//
////    dd($patterns, $grouped_plugin_definitions);
////    $yass = \Drupal::service('plugin.manager.ui_patterns')->getDefinitions();
////    $yass2 = [];
////
////    foreach ($yass as $patternDefinition) {
////      $yass2[] = $this->getPatternPaths($patternDefinition);
////    }
//////
//////    dd($yass2);
////
////    /** @var \Drupal\ui_patterns\Plugin\PatternBase $pattern */
////    $items = [
////      'patterns_destination' => [
////        'variables' => ['sources' => NULL, 'context' => NULL],
////      ],
////      'patterns_use_wrapper' => [
////        'variables' => ['use' => NULL],
////      ],
////    ];
////
////    foreach (UiPatterns::getManager()->getPatterns() as $pattern) {
////      $items += $pattern->getThemeImplementation();
////    }
//
////    $patternDefinition = new PatternDefinition();
////    $patternDefinition->getLibraries();
//
////    $libraries = [];
////
////    foreach ($yass as $patternDefinition) {
////      if ($patternDefinition instanceof \Drupal\ui_patterns\Definition\PatternDefinition) {
////        // Assurez-vous que $patternDefinition est bien une instance de PatternDefinition.
////        $libraries[] = $patternDefinition->getLibraries();
////      }
////    }
//
////    foreach (UiPatterns::getPatternDefinition($yass2['#id'])->getLibrariesNames() as $library) {
////      $element['#attached']['library'][] = $library;
////    }
////    dd($library);
////
////// Faites quelque chose avec le tableau $libraries.
////    dd($libraries);
//
//
//
////    $uiPatternsManager = \Drupal::service('plugin.manager.ui_patterns');
////
////    $patternDefinitions = $uiPatternsManager->getDefinitions();
//////    dd($patternDefinitions);
////    foreach ($patternDefinitions as $patternDefinition) {
////      // Vérifiez si la définition est une instance de PatternInterface.
////      if ($patternDefinition instanceof \Drupal\ui_patterns\Plugin\PatternInterface) {
////        // Utilisez la méthode getLibraryDefinitions pour obtenir les informations sur les bibliothèques.
////        $libraryDefinitions = $patternDefinition->getLibraryDefinitions();
////
////        // Faites quelque chose avec les informations sur les bibliothèques, par exemple, imprimez-les.
////        dd($libraryDefinitions);
////      }
////    }
//    // GETTING ACTIVE THEME
////    $theme = \Drupal::theme()
////      ->getActiveTheme();
////    dd($theme);
//
//    $theme = \Drupal::theme()
//      ->getActiveTheme()
//      ->getName();
//    $installed_atjs = \Drupal::service('library.discovery')->getLibrariesByExtension($theme);
//    dd($installed_atjs);
//    return new JsonResponse($patterns);
//  }

  public function getPatternPaths( $patternDefinition) {
    $basePath = $patternDefinition->getBasePath();
    $templatePath = $basePath . '/' . $patternDefinition->getTemplate();

    $libraries = $patternDefinition->getLibraries();
    $cssPaths = [];
    $jsPaths = [];

    foreach ($libraries as $library) {
      if (isset($library['css'])) {
        $cssPaths[] = $basePath . '/' . $library['css'];
      }

      if (isset($library['js'])) {
        $jsPaths[] = $basePath . '/' . $library['js'];
      }
    }

    return [
      'template' => $templatePath,
      'css' => $cssPaths,
      'js' => $jsPaths,
    ];
  }

}


