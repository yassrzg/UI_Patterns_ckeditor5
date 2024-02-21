<?php

declare(strict_types = 1);

namespace Drupal\ui_skins\Plugin\Derivative;

use Drupal\Component\Plugin\Derivative\DeriverBase;
use Drupal\Core\Extension\ThemeHandlerInterface;
use Drupal\Core\Plugin\Discovery\ContainerDeriverInterface;
use Drupal\ui_skins\CssVariable\CssVariablePluginManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Derivative class for the local tasks and menu links for CSS variables.
 */
class CssVariables extends DeriverBase implements ContainerDeriverInterface {

  /**
   * The theme handler.
   *
   * @var \Drupal\Core\Extension\ThemeHandlerInterface
   */
  protected ThemeHandlerInterface $themeHandler;

  /**
   * The CSS variables plugin manager.
   *
   * @var \Drupal\ui_skins\CssVariable\CssVariablePluginManagerInterface
   */
  protected CssVariablePluginManagerInterface $cssVariablePluginManager;

  /**
   * Constructor.
   *
   * @param \Drupal\Core\Extension\ThemeHandlerInterface $themeHandler
   *   The theme handler.
   * @param \Drupal\ui_skins\CssVariable\CssVariablePluginManagerInterface $cssVariablePluginManager
   *   The CSS variables plugin manager.
   */
  public function __construct(
    ThemeHandlerInterface $themeHandler,
    CssVariablePluginManagerInterface $cssVariablePluginManager
  ) {
    $this->themeHandler = $themeHandler;
    $this->cssVariablePluginManager = $cssVariablePluginManager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, $base_plugin_id): static {
    // @phpstan-ignore-next-line
    return new static(
      $container->get('theme_handler'),
      $container->get('plugin.manager.ui_skins.css_variable')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getDerivativeDefinitions($base_plugin_definition): array {
    foreach ($this->themeHandler->listInfo() as $theme_name => $theme) {
      $definitions = $this->cssVariablePluginManager->getDefinitionsForTheme($theme_name);
      if (empty($definitions)) {
        continue;
      }

      $this->derivatives[$theme_name] = $base_plugin_definition;
      $this->derivatives[$theme_name]['title'] = $theme->info['name'];
      $this->derivatives[$theme_name]['route_parameters'] = [
        'theme' => $theme_name,
      ];
    }

    return $this->derivatives;
  }

}
