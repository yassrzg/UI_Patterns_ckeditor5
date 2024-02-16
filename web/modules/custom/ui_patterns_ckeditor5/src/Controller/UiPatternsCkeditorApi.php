<?php

namespace Drupal\ui_patterns_ckeditor5\Controller;

use Drupal\Component\Serialization\Yaml;
use Drupal\Core\Controller\ControllerBase;
use Drupal\ui_patterns\Definition\PatternDefinition;
use Drupal\ui_patterns\Element\PatternContext;
use Drupal\ui_patterns\Element\PatternPreview;
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
//     GETTING ACTIVE THEME
//    $theme = \Drupal::theme()
//      ->getActiveTheme();
//    dd($theme);

//    $theme = \Drupal::theme()
//      ->getActiveTheme()
//      ->getName();
//    $installed_atjs = \Drupal::service('library.discovery')->getLibrariesByExtension($theme);
//    dd($installed_atjs);

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
//    dd($items);
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

    $uiPatternsManager = \Drupal::service('plugin.manager.ui_patterns');
    $patternDefinitions = $uiPatternsManager->getDefinitions();
//    dd($patternDefinitions);
//    foreach ($patternDefinitions as $patternId => $patternDefinition) {
//      // Assuming that $element is a suitable array for processing.
//      $element = ['#id' => $patternId];
//      $processedElement = PatternPreview::processFields($element);
////      dd($processedElement);
//    }
//
//
//    foreach ($patternDefinitions as $patternId => $patternDefinition) {
//      $element = ['#id' => $patternId];
//      $definition = UiPatterns::getPatternDefinition($element['#id']);
//      $element['#context'] = new PatternContext('preview');
//      dd($element, 'element');
//
//      $fields = [];
//      foreach ($definition->getFields() as $field) {
//        $preview = $field->getPreview();
//        // Some fields are used as Twig array keys and don't need escaping.
////      if ($field->getEscape()) {
////        // The examples are not user submitted and are safe markup.
////        $preview = self::getPreviewMarkup($preview);
////      }
////      $fields[$field->getName()] = $preview;
//
//      }
//      dd($preview, 'preview');
//    }



    if (isset($patternDefinitions[$patternName])) {
      $patternDefinition = $patternDefinitions[$patternName];
      $basePath = $patternDefinition->getBasePath();
      $patternPath = $basePath . '/' . $patternDefinition->getTemplate() . '.html.twig';

    } else {
      // Gérer le cas où le motif spécifié n'a pas été trouvé
      dd("Le motif '$patternName' n'a pas été trouvé.");
    }

    if (!file_exists($patternPath)) {
      return new JsonResponse(['error' => "Pattern {$patternName} not found"], 404);
    }



    $content = file_get_contents($patternPath);
//    dd($content);


    if (isset($patternDefinitions[$patternName])) {
      $patternDefinition = $patternDefinitions[$patternName];
      $fields = $patternDefinition->getFields();
      $fieldKeys = array_keys($fields);
      foreach ($fieldKeys as $key) {
        if ($key === 'image') {
          $module_path= \Drupal::service('extension.path.resolver')->getPath('module', 'ui_patterns_ckeditor5');
          $image_path = $module_path . '/assets/image/images.webp';
          $image_url = \Drupal::service('file_url_generator')->generateAbsoluteString($image_path);
          $fieldValues[$key] = $image_url;
        } else {
          $fieldValues[$key] = 'default value';
        }
      }

    } else {
      return new JsonResponse(['error' => "Pattern {$patternName} not found"], 404);
    }
//    dd($fieldValues, 'fieldValues');
//    dd($fieldValues);
    $loader = new \Twig\Loader\ArrayLoader([$patternDefinition->getTemplate() => $content]);
    $twig = new \Twig\Environment($loader);

// Get the service from the Drupal container.
    $extension = \Drupal::service('ui_patterns_ckeditor5.twig.extension');
    $extension2 = \Drupal::service('ui_patterns.twig.extension');
    $extension3 = \Drupal::service('ui_patterns_settings.twig');
    $extension4 = \Drupal::service('twig.loader.theme_registry');
    $extension5 = \Drupal::service('twig.extension');
    $extension6 = \Drupal::service('twig');
    $extension7 = \Drupal::service('twig.loader.string');
    $extension8 = \Drupal::service('twig.loader.theme_registry');


// Add the extension to the Twig environment.
    $twig->addExtension($extension);
    $twig->addExtension($extension2);
//    $twig->addExtension($extension3);
////    $twig->addExtension($extension4);
//    $twig->addExtension($extension5);
////    $twig->addExtension($extension6);
////    $twig->addExtension($extension7);
////    $twig->addExtension($extension8);

    $template = $twig->load($patternDefinition->getTemplate());
//    dd($template);
    $html = $template->render($fieldValues);
    dd($html);
//    dd($html);
//    dd($fieldKeys, $fieldValues);

//    $twig = new \Twig\Environment(new \Twig\Loader\ArrayLoader([$patternDefinition->getTemplate() => $content]));
////    dd($twig);
//    $template = $twig->load($patternDefinition->getTemplate());
//    dd($template);
//    $html = $template->render($fieldValues);
////    dd($html);




    return new JsonResponse(['content' => $html]);
  }

//  public function getPatternVariables($patternName) {
//    $patternVariables = [];
//
//    // Obtenez tous les modèles de motifs
//    foreach (UiPatterns::getManager()->getPatterns() as $pattern) {
//      dd($pattern);
//      if ($pattern['pluginId'] === $patternName) {
//        // Obtenez les variables du motif
//        $patternVariables = $pattern->getVariables();
//        break;
//      }
//    }
//
//
//    // Remplacez les valeurs nulles par 'default_value'
//    array_walk_recursive($patternVariables, function (&$value) {
//      $value = ($value === null) ? 'default_value' : $value;
//    });
//
//    return $patternVariables;
//  }

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


