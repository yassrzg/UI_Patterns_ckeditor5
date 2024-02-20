<?php

namespace Drupal\ui_patterns_ckeditor5\Template;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

/**
 * Twig extension providing UI Patterns-specific functionalities.
 *
 * @package Drupal\ui_patterns\Template
 */
class TwigExtension extends AbstractExtension {

  use AttributesFilterTrait;

  /**
   * {@inheritdoc}
   */
  public function getName() {
    return 'ui_patterns_ckeditor5';
  }

  /**
   * {@inheritdoc}
   */
  public function getFunctions() {
    return [
      new TwigFunction('pattern', [
        $this,
        'renderPattern',
      ]),
      new TwigFunction('pattern_preview', [
        $this,
        'renderPatternPreview',
      ]),
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getFilters() {
    return [
      new TwigFilter('add_class', [$this, 'addClass']),
      new TwigFilter('set_attribute', [$this, 'setAttribute']),
    ];
  }


  public function addClass($element, $class)
  {
    if (is_array($element)) {
      $element['#attributes']['class'][] = $class;
    }

    return $element;
  }

  /**
   * Render given pattern.
   *
   * @param string $id
   *   Pattern ID.
   * @param array $fields
   *   Pattern fields.
   * @param string $variant
   *   Variant name.
   *
   * @return array
   *   Pattern render array.
   *
   * @see \Drupal\ui_patterns\Element\Pattern
   */
  public function renderPattern($id, array $fields = [], $variant = "") {
    return [
      '#type' => 'pattern',
      '#id' => $id,
      '#fields' => $fields,
      '#variant' => $variant,
    ];
  }

  /**
   * Render given pattern.
   *
   * @param string $id
   *   Pattern ID.
   * @param string $variant
   *   Variant name.
   *
   * @return array
   *   Pattern render array.
   *
   * @see \Drupal\ui_patterns\Element\Pattern
   */
  public function renderPatternPreview($id, $variant = "") {
    return [
      '#type' => 'pattern_preview',
      '#id' => $id,
      '#variant' => $variant,
    ];
  }

}
