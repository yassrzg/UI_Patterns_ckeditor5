<?php

namespace Drupal\ui_patterns_ckeditor5\Controller;

use Drupal\Component\Serialization\Yaml;
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
//  public function getAllPatterns(): JsonResponse
//  {
//
//    $theme = \Drupal::theme()
//      ->getActiveTheme()
//      ->getName();
//    $libraries_themes = \Drupal::service('library.discovery')->getLibrariesByExtension($theme);
////    dd($libraries_themes);
//    $build = [
//      '#theme' => 'my_theme_hook',
//      '#attached' => [
//        'css' => [],
//        'js' => [],
//      ],
//    ];
//      // Attach JS libraries.
//    foreach ($libraries_themes as $library_name => $library_info) {
//      // Attacher les bibliothèques JS.
//      if (isset($library_info['js'])) {
//        foreach ($library_info['js'] as $js) {
//          $build['#attached']['js'][] = $js['data'];
//        }
//      }
//
//      // Attach css libraries.
//      if (isset($library_info['css'])) {
//        foreach ($library_info['css'] as $css) {
//          $build['#attached']['css'][] = $css['data'];
//        }
//      }
//    }
//
//    $build['#attached']['library'][] = 'ui_patterns_ckeditor5/patterns';
//    return new JsonResponse($libraries_themes);
//  }

  public function getAllPatterns(): JsonResponse
  {
    $patterns = [];
    foreach ($this->patternsManager->getGroupedDefinitions() as $groupName => $groupedDefinitions) {
      foreach ($groupedDefinitions as $definition) {
        $patterns[] = $definition->toArray();
      }
    }
    $patternPaths = [];

    foreach ($patterns as $pattern) {
      $basePath = $pattern['base path'];
      $template = $pattern['template'];

      $patternPaths[] = $basePath . '/' . $template;
    }

//    dd($patternPaths);
//    dd($patterns);
//    $grouped_plugin_definitions = $this->patternsManager->getSortedDefinitions();
    $grouped_plugin_definitions = $this->patternsManager->getPatternsOptions();
//    dd($grouped_plugin_definitions);
//    $accordionSettings = $this->patternsManager->getDefinition('accordion')->getAdditional()['settings'];
//    dd($accordionSettings);

//    dd($patterns, $grouped_plugin_definitions);
//    $yass = \Drupal::service('plugin.manager.ui_patterns')->getDefinitions();
//    $yass2 = [];
//
//    foreach ($yass as $patternDefinition) {
//      $yass2[] = $this->getPatternPaths($patternDefinition);
//    }
////
////    dd($yass2);
//
//    /** @var \Drupal\ui_patterns\Plugin\PatternBase $pattern */
//    $items = [
//      'patterns_destination' => [
//        'variables' => ['sources' => NULL, 'context' => NULL],
//      ],
//      'patterns_use_wrapper' => [
//        'variables' => ['use' => NULL],
//      ],
//    ];
//
//    foreach (UiPatterns::getManager()->getPatterns() as $pattern) {
//      $items += $pattern->getThemeImplementation();
//    }

//    $patternDefinition = new PatternDefinition();
//    $patternDefinition->getLibraries();

//    $libraries = [];
//
//    foreach ($yass as $patternDefinition) {
//      if ($patternDefinition instanceof \Drupal\ui_patterns\Definition\PatternDefinition) {
//        // Assurez-vous que $patternDefinition est bien une instance de PatternDefinition.
//        $libraries[] = $patternDefinition->getLibraries();
//      }
//    }

//    foreach (UiPatterns::getPatternDefinition($yass2['#id'])->getLibrariesNames() as $library) {
//      $element['#attached']['library'][] = $library;
//    }
//    dd($library);
//
//// Faites quelque chose avec le tableau $libraries.
//    dd($libraries);



//    $uiPatternsManager = \Drupal::service('plugin.manager.ui_patterns');
//
//    $patternDefinitions = $uiPatternsManager->getDefinitions();
////    dd($patternDefinitions);
//    foreach ($patternDefinitions as $patternDefinition) {
//      // Vérifiez si la définition est une instance de PatternInterface.
//      if ($patternDefinition instanceof \Drupal\ui_patterns\Plugin\PatternInterface) {
//        // Utilisez la méthode getLibraryDefinitions pour obtenir les informations sur les bibliothèques.
//        $libraryDefinitions = $patternDefinition->getLibraryDefinitions();
//
//        // Faites quelque chose avec les informations sur les bibliothèques, par exemple, imprimez-les.
//        dd($libraryDefinitions);
//      }
//    }
    // GETTING ACTIVE THEME
//    $theme = \Drupal::theme()
//      ->getActiveTheme();
//    dd($theme);

//    $theme = \Drupal::theme()
//      ->getActiveTheme()
//      ->getName();
//    $installed_atjs = \Drupal::service('library.discovery')->getLibrariesByExtension($theme);
//    dd($installed_atjs);
    return new JsonResponse($patternPaths);
  }

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


  public function getPatternContent($patternName) {
    $patternPath = "/var/www/html/d10_ckeditor5/web/themes/contrib/ui_suite_dsfr/templates/patterns/{$patternName}/pattern-{$patternName}.html.twig";
    $ymlPath = "/var/www/html/d10_ckeditor5/web/themes/contrib/ui_suite_dsfr/templates/patterns/{$patternName}/{$patternName}.ui_patterns.yml";

    if (!file_exists($patternPath)) {
      return new JsonResponse(['error' => "Pattern {$patternName} not found"], 404);
    }
    if (!file_exists($ymlPath)) {
      return new JsonResponse(['error' => "Pattern {$patternName} not found"], 404);
    }
    $yamlContent = file_get_contents($ymlPath);
    $config = Yaml::decode($yamlContent);
    $variables = $this->flattenConfigArray($config);
//    dd($variables);


    $grouped_plugin_definitions = $this->patternsManager->getSortedDefinitions();
//    dd($grouped_plugin_definitions);
    // Rendre le modèle Twig et le convertir en HTML
//    $html = \Drupal::service('twig')->render($patternPath);
//    dd($html);
//    $template = file_get_contents($patternPath);
//
//    // Define the variables for the Twig template
//    $variables = [
//      'title' => 'Your title',
//      'content' => 'Your content',
//      'expanded' => true,
//    ];
//
//    // Render the Twig template and convert it to HTML
//    $html = \Drupal::service('twig')->renderInline($template, $variables);
    $content = file_get_contents($patternPath);
//    dd($content);
    $cleanedContent = $this->cleanTwigContent($content);
//    dd($cleanedContent);


    return new JsonResponse(['content' => $content]);
  }
  private function cleanTwigContent($content) {
    // Supprimer les blocs {% ... %}
    $content = preg_replace('/{%.*?%}/', '', $content);

    // Supprimer les blocs {{ ... }}
    $content = preg_replace('/{{.*?}}/', '', $content);

    return $content;
  }


  private function flattenConfigArray(array $config, array $keys = []) {
    $flattened = [];

    foreach ($config as $key => $value) {
      $currentKeys = array_merge($keys, [$key]);

      if (is_array($value)) {
        $flattened = array_merge($flattened, $this->flattenConfigArray($value, $currentKeys));
      } else {
        // Concaténer les clés pour former une clé unique dans le tableau $variables
        $variableKey = implode('_', $currentKeys);

        // Remplacer les caractères invalides pour former une clé valide en PHP
        $variableKey = preg_replace('/[^A-Za-z0-9_]/', '', $variableKey);

        // Ajouter la paire clé-valeur au tableau $flattened
        $flattened[$variableKey] = $value;
      }
    }

    return $flattened;
  }

}


