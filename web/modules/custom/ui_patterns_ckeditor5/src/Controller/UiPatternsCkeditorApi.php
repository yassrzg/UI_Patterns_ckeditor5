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


    if (isset($patternDefinitions[$patternName])) {
      $patternDefinition = $patternDefinitions[$patternName];
      $basePath = $patternDefinition->getBasePath();
      $patternPath = $basePath . '/' . $patternDefinition->getTemplate() . '.html.twig';

    } else {
      // Gérer le cas où le motif spécifié n'a pas été trouvé
      dd("Le motif '$patternName' n'a pas été trouvé.");
    }

    if (!file_exists($patternPath)) {
      return new JsonResponse(['error' => "Pattern {$patternName} not found yass"], 404);
    }



    $content = file_get_contents($patternPath);
//    dd($content);


    if (isset($patternDefinitions[$patternName])) {
      $patternDefinition = $patternDefinitions[$patternName];
      $fields = $patternDefinition->getFields();
//      dd($fields, 'fields');

      $fieldValues = [];
      foreach ($fields as $field) {
        $type = $field->getType();
        $preview = $field->getPreview();
//        dd($fields, 'type');

        switch ($type) {
          case 'text':
            $fieldValues[$field->getName()] = $preview ?? 'default text';
//            dd('hello', $fieldValues[$field->getName()]);
            break;
          case 'boolean':
            $fieldValues[$field->getName()] = $preview ?? false;
            break;
          case'render':

              foreach ($preview as $fieldName => $fieldValue) {
//                dd($fieldName);
                // Si la clé est "theme" et la valeur est "image", affichez la balise img
                if ($fieldName === 'theme' && $fieldValue === 'image') {
                  // Construisez votre balise img avec les attributs nécessaires
                  $uri = $preview['uri']; // Supposons que 'uri' est toujours défini
                  $alt = $preview['alt'] ?? ''; // Valeur par défaut pour alt si non défini
                  $preview = "<img src=\"$uri\" alt=\"$alt\">";
                  $fieldValues['image'] = $preview;
//                  $fieldValues['image'] = [$preview];

                }
                elseif ($fieldName === 'type' && $fieldValue === 'html_tag') {
                  $tag = $preview['tag'] ?? '';
                  $value = $preview['value'] ?? '';
                  $attributes = $preview['attributes'] ?? [];

                  // Construisez votre balise <a> avec les attributs nécessaires
                  $fieldValues['title'] = "<$tag";

                  foreach ($attributes as $attrName => $attrValue) {
                    $fieldValues['title'] .= " $attrName=\"$attrValue\"";
                  }

                  $fieldValues['title'] .= ">$value</$tag>";

                }

              }

            break;
          // Ajoutez d'autres types de champ au besoin
          default:
            $fieldValues[$field->getName()] = $preview ?? 'default value';
        }
      }
    } else {
      // Gérer le cas où le motif spécifié n'a pas été trouvé
      echo "Le motif '$patternName' n'a pas été trouvé.";
    }

//    dd($fieldValues, 'fieldValues');

    $modifiedHtml = $this->replaceAddClassWithClass($content);

    $modifiedHtml2 = $this->replaceAddClass($modifiedHtml);
    dd($modifiedHtml2);


//    dd($modifiedHtml);
    $twig = new \Twig\Environment(new \Twig\Loader\ArrayLoader([$patternDefinition->getTemplate() => $modifiedHtml2]));
// Get the service from the Drupal container.
    $extension = \Drupal::service('ui_patterns_ckeditor5.twig.extension');
    $extension2 = \Drupal::service('ui_patterns.twig.extension');
    $extension3 = \Drupal::service('ui_patterns_settings.twig');
    $extension5 = \Drupal::service('twig.extension');


    $extensions = [$extension, $extension2, $extension3, $extension5];
    $twig->setExtensions($extensions);


    $template = $twig->load($patternDefinition->getTemplate());
//    dd($template);
    $html = $template->render($fieldValues);
//    dd($html);





    return new JsonResponse(['content' => $html]);
  }



  private function cleanTwigContent($content) {
    // Supprimer les blocs {% ... %}
    $content = preg_replace('/{%.*?%}/', '', $content);

    // Supprimer les blocs {{ ... }}
    $content = preg_replace('/{{.*?}}/', '', $content);

    return $content;
  }

  function replaceAddClassWithClass($htmlString) {
    // Recherche de toutes les occurrences de {{ attributes.addClass('...') }}
    $pattern = '/{{\s*attributes\.addClass\((["\'])(.*?)\1\)\s*}}/';
    $replacement = ' class=$1$2$1';
//    dd('hello', $htmlString);

    return preg_replace($pattern, $replacement, $htmlString);
  }
//  @todo Replace the following function with the one above
  function replaceAddClass($htmlString) {
    // Recherche de toutes les occurrences de {{ variable|add_class('...') }}
    $pattern = '/{{\s*([^\|]+)\|add_class\((["\'])(.*?)\2\)\s*}}/';
    $replacement = '<$1 class=$2$3$2>';

    // Remplace les occurrences dans la chaîne HTML
    $result = preg_replace($pattern, $replacement, $htmlString);
    $result = str_replace('{{', '<', $result);

    // Affiche le résultat pour le débogage
//    dd('hello', $result);

    return $result;
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


  public function LinksForCssAndJS() {
    $theme = \Drupal::theme()
      ->getActiveTheme()
      ->getName();
    $libraries_themes = \Drupal::service('library.discovery')->getLibrariesByExtension($theme);

    $jsFiles = [];
    $cssFiles = [];

    foreach ($libraries_themes as $library) {
      if (isset($library['js'])) {
        foreach ($library['js'] as $jsFile) {
          $jsFiles[] = $jsFile['data'];
        }
      }

      if (isset($library['css'])) {
        foreach ($library['css'] as $cssFile) {
          $cssFiles[] = $cssFile['data'];
        }
      }
    }
    return new JsonResponse(['jsFiles' => $jsFiles, 'cssFiles' => $cssFiles]);

  }

}


