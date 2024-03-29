<?php

namespace Drupal\ui_patterns\Definition;

use Drupal\Component\Plugin\Definition\DerivablePluginDefinitionInterface;
use Drupal\Component\Plugin\Definition\PluginDefinition;
use Drupal\Core\Url;

/**
 * Pattern definition class.
 *
 * @package Drupal\ui_patterns\Definition
 */
class PatternDefinition extends PluginDefinition implements DerivablePluginDefinitionInterface, \ArrayAccess {

  use ArrayAccessDefinitionTrait;

  /**
   * Pattern prefix.
   */
  const PATTERN_PREFIX = 'pattern_';

  /**
   * Prefix for locally defined libraries.
   */
  const LIBRARY_PREFIX = 'ui_patterns';

  /**
   * Pattern definition.
   *
   * @var array
   */
  protected $definition = [
    'id' => NULL,
    'label' => NULL,
    'description' => NULL,
    'links' => [],
    'category' => '',
    'base path' => NULL,
    'file name' => NULL,
    'use' => NULL,
    'theme hook' => NULL,
    'custom theme hook' => FALSE,
    'template' => NULL,
    'libraries' => [],
    'fields' => [],
    'variants' => [],
    'tags' => [],
    'weight' => 0,
    'additional' => [],
    'deriver' => NULL,
    'provider' => NULL,
    'class' => NULL,
  ];

  /**
   * PatternDefinition constructor.
   */
  public function __construct(array $definition = []) {
    foreach ($definition as $name => $value) {
      if (array_key_exists($name, $this->definition)) {
        $this->definition[$name] = $value;
      }
      else {
        $this->definition['additional'][$name] = $value;
      }
    }

    $this->id = $this->definition['id'];
    $this->setFields($this->definition['fields']);
    $this->setVariants($this->definition['variants']);
    $this->setThemeHook(self::PATTERN_PREFIX . $this->id());

    if (!empty($definition['theme hook'])) {
      $this->setThemeHook($definition['theme hook']);
      $this->definition['custom theme hook'] = TRUE;
    }

    if (!$this->hasTemplate()) {
      $this->setTemplate(str_replace('_', '-', $this->getThemeHook()));
    }
  }

  /**
   * Getter.
   *
   * @return mixed
   *   Property value.
   */
  public function getLabel() {
    return $this->definition['label'];
  }

  /**
   * Setter.
   *
   * @param mixed $label
   *   Property value.
   *
   * @return $this
   */
  public function setLabel($label) {
    $this->definition['label'] = $label;
    return $this;
  }

  /**
   * Getter.
   *
   * @return string
   *   Property value.
   */
  public function getDescription() {
    return $this->definition['description'];
  }

  /**
   * Setter.
   *
   * @param string $description
   *   Property value.
   *
   * @return $this
   */
  public function setDescription($description) {
    $this->definition['description'] = $description;
    return $this;
  }

  /**
   * Getter.
   *
   * @return array
   *   The link.
   */
  public function getLinks(): array {
    $links = [];

    foreach ($this->definition['links'] as $link) {
      if (!\is_array($link)) {
        $link = [
          'url' => $link,
        ];
      }

      $link += [
        'title' => 'External documentation',
      ];

      $links[] = $link;
    }

    return $links;
  }

  /**
   * Setter.
   *
   * @param array $links
   *   Property value.
   *
   * @return $this
   */
  public function setLinks(array $links) {
    $this->definition['links'] = $links;
    return $this;
  }

  /**
   * Construct render links.
   *
   * @return array
   *   Render links.
   */
  public function getRenderLinks(): array {
    $renderLinks = [];
    foreach ($this->getLinks() as $link) {
      $renderLinks[] = $this->renderLink($link);
    }
    return $renderLinks;
  }

  /**
   * Getter.
   *
   * @return \Drupal\Core\StringTranslation\TranslatableMarkup|string
   *   Property value.
   */
  public function getCategory() {
    return $this->definition['category'];
  }

  /**
   * Setter.
   *
   * @param \Drupal\Core\StringTranslation\TranslatableMarkup|string $category
   *   Property value.
   *
   * @return $this
   */
  public function setCategory($category) {
    $this->definition['category'] = $category;
    return $this;
  }

  /**
   * Getter.
   *
   * @return mixed
   *   Property value.
   */
  public function getBasePath() {
    return $this->definition['base path'];
  }

  /**
   * Setter.
   *
   * @param mixed $basePath
   *   Property value.
   *
   * @return $this
   */
  public function setBasePath($basePath) {
    $this->definition['base path'] = $basePath;
    return $this;
  }

  /**
   * Getter.
   *
   * @return mixed
   *   Property value.
   */
  public function getFileName() {
    return $this->definition['file name'];
  }

  /**
   * Setter.
   *
   * @param mixed $fileName
   *   Property value.
   *
   * @return $this
   */
  public function setFileName($fileName) {
    $this->definition['file name'] = $fileName;
    return $this;
  }

  /**
   * Get Provider property.
   *
   * @return string
   *   Property value.
   */
  public function getProvider() {
    return $this->definition['provider'];
  }

  /**
   * Setter.
   *
   * @param mixed $provider
   *   Property value.
   *
   * @return $this
   */
  public function setProvider($provider) {
    $this->definition['provider'] = $provider;
    return $this;
  }

  /**
   * Get field.
   *
   * @param string $name
   *   Field name.
   *
   * @return PatternDefinitionField|null
   *   Definition field.
   */
  public function getField($name) {
    return $this->hasField($name) ? $this->definition['fields'][$name] : NULL;
  }

  /**
   * Getter.
   *
   * @return PatternDefinitionField[]
   *   Property value.
   */
  public function getFields() {
    return $this->definition['fields'];
  }

  /**
   * Get field as options.
   *
   * @return array
   *   Fields as select options.
   */
  public function getFieldsAsOptions() {
    $options = [];
    foreach ($this->getFields() as $field) {
      $options[$field->getName()] = $field->getLabel();
    }
    return $options;
  }

  /**
   * Set field.
   *
   * @param string $name
   *   Field name.
   * @param string $label
   *   Field label.
   *
   * @return $this
   */
  public function setField($name, $label) {
    $this->definition['fields'][$name] = $this->getFieldDefinition($name, $label);
    return $this;
  }

  /**
   * Setter.
   *
   * @param array $fields
   *   Property value.
   *
   * @return $this
   */
  public function setFields(array $fields) {
    foreach ($fields as $name => $value) {
      $field = $this->getFieldDefinition($name, $value);
      $this->definition['fields'][$field->getName()] = $field;
    }
    return $this;
  }

  /**
   * Check whereas field exists.
   *
   * @param string $name
   *   Field name.
   *
   * @return bool
   *   Whereas field exists
   */
  public function hasField($name) {
    return isset($this->definition['fields'][$name]);
  }

  /**
   * Get variant.
   *
   * @param string $name
   *   Field name.
   *
   * @return \Drupal\ui_patterns\Definition\PatternDefinitionVariant|null
   *   Definition field.
   */
  public function getVariant($name) {
    return $this->hasVariant($name) ? $this->definition['variants'][$name] : NULL;
  }

  /**
   * Getter.
   *
   * @return \Drupal\ui_patterns\Definition\PatternDefinitionVariant[]
   *   Property value.
   */
  public function getVariants() {
    return $this->definition['variants'];
  }

  /**
   * Get field as options.
   *
   * @return array
   *   Variants as select options.
   */
  public function getVariantsAsOptions() {
    $options = [];
    foreach ($this->getVariants() as $variant) {
      $options[$variant->getName()] = $variant->getLabel();
    }
    return $options;
  }

  /**
   * Set variant.
   *
   * @param string $name
   *   Variant name.
   * @param string $label
   *   Variant label.
   *
   * @return $this
   */
  public function setVariant($name, $label) {
    $this->definition['variants'][$name] = $this->getVariantDefinition($name, $label);
    return $this;
  }

  /**
   * Setter.
   *
   * @param array $variants
   *   Property value.
   *
   * @return $this
   */
  public function setVariants(array $variants) {
    foreach ($variants as $name => $value) {
      $variant = $this->getVariantDefinition($name, $value);
      $this->definition['variants'][$variant->getName()] = $variant;
    }
    return $this;
  }

  /**
   * Check whereas variant exists.
   *
   * @param string $name
   *   Variant name.
   *
   * @return bool
   *   Whereas variant exists
   */
  public function hasVariant($name) {
    return isset($this->definition['variants'][$name]);
  }

  /**
   * Check whereas pattern has variants.
   *
   * @return bool
   *   Whereas pattern has variants.
   */
  public function hasVariants() {
    return !empty($this->definition['variants']);
  }

  /**
   * Getter.
   *
   * @return string
   *   Property value.
   */
  public function getThemeHook() {
    return $this->definition['theme hook'];
  }

  /**
   * Setter.
   *
   * @param string $theme_hook
   *   Property value.
   *
   * @return $this
   */
  public function setThemeHook($theme_hook) {
    $this->definition['theme hook'] = $theme_hook;
    return $this;
  }

  /**
   * Getter.
   *
   * @return string
   *   Property value.
   */
  public function getUse() {
    return $this->definition['use'];
  }

  /**
   * Setter.
   *
   * @param string $use
   *   Property value.
   *
   * @return $this
   */
  public function setUse($use) {
    $this->definition['use'] = $use;
    return $this;
  }

  /**
   * Getter.
   *
   * @return bool
   *   Whereas definition uses the "use:" property.
   */
  public function hasUse() {
    $pattern_has_use = !empty($this->definition['use']);

    if (!$pattern_has_use) {
      $pattern_variants = $this->getVariants();
      foreach ($pattern_variants as $pattern_variant) {
        if ($pattern_variant->hasUse()) {
          $pattern_has_use = TRUE;
          break;
        }
      }
    }
    return $pattern_has_use;
  }

  /**
   * Getter.
   *
   * @return array
   *   Property value.
   */
  public function getTags() {
    return $this->definition['tags'];
  }

  /**
   * Setter.
   *
   * @param array $tags
   *   Property value.
   *
   * @return $this
   */
  public function setTags(array $tags) {
    $this->definition['tags'] = $tags;
    return $this;
  }

  /**
   * Getter.
   *
   * @return mixed
   *   Property value.
   */
  public function hasCustomThemeHook() {
    return $this->definition['custom theme hook'];
  }

  /**
   * Getter.
   *
   * @return mixed
   *   Property value.
   */
  public function getTemplate() {
    return $this->definition['template'];
  }

  /**
   * Setter.
   *
   * @param mixed $template
   *   Property value.
   *
   * @return $this
   */
  public function setTemplate($template) {
    $this->definition['template'] = $template;
    return $this;
  }

  /**
   * Getter.
   *
   * @return bool
   *   Whereas has template.
   */
  public function hasTemplate() {
    return !empty($this->definition['template']);
  }

  /**
   * Getter.
   *
   * @return mixed
   *   Property value.
   */
  public function getLibraries() {
    return $this->definition['libraries'];
  }

  /**
   * Getter.
   *
   * @return mixed
   *   Property value.
   */
  public function getLibrariesNames() {
    $libraries = [];
    foreach ($this->getLibraries() as $library) {
      if (is_array($library)) {
        $libraries[] = self::LIBRARY_PREFIX . '/' . $this->id() . '.' . key($library);
      }
      else {
        $libraries[] = $library;
      }
    }
    return $libraries;
  }

  /**
   * Setter.
   *
   * @param mixed $libraries
   *   Property value.
   *
   * @return $this
   */
  public function setLibraries($libraries) {
    $this->definition['libraries'] = $libraries;
    return $this;
  }

  /**
   * Get Class property.
   *
   * @return string
   *   Property value.
   */
  public function getClass() {
    return $this->definition['class'];
  }

  /**
   * Set Class property.
   *
   * @param string $class
   *   Property value.
   *
   * @return $this
   */
  public function setClass($class) {
    parent::setClass($class);
    $this->definition['class'] = $class;
    return $this;
  }

  /**
   * Getter.
   *
   * @return int
   *   Property value.
   */
  public function getWeight(): int {
    return $this->definition['weight'];
  }

  /**
   * Setter.
   *
   * @param int $weight
   *   Property value.
   *
   * @return $this
   */
  public function setWeight(int $weight) {
    $this->definition['weight'] = $weight;
    return $this;
  }

  /**
   * Get Additional property.
   *
   * @return array
   *   Property value.
   */
  public function getAdditional() {
    return $this->definition['additional'];
  }

  /**
   * Set Additional property.
   *
   * @param array $additional
   *   Property value.
   *
   * @return $this
   */
  public function setAdditional(array $additional) {
    $this->definition['additional'] = $additional;
    return $this;
  }

  /**
   * Get Deriver property.
   *
   * @return mixed
   *   Property value.
   */
  public function getDeriver() {
    return $this->definition['deriver'];
  }

  /**
   * Set Deriver property.
   *
   * @param mixed $deriver
   *   Property value.
   *
   * @return $this
   */
  public function setDeriver($deriver) {
    $this->definition['deriver'] = $deriver;
    return $this;
  }

  /**
   * Factory method: create a new field definition.
   *
   * @param string $name
   *   Field name.
   * @param string $value
   *   Field value.
   *
   * @return \Drupal\ui_patterns\Definition\PatternDefinitionField
   *   Definition instance.
   */
  public function getFieldDefinition($name, $value) {
    return new PatternDefinitionField($name, $value);
  }

  /**
   * Factory method: create a new variant definition.
   *
   * @param string $name
   *   Variant name.
   * @param string $value
   *   Variant value.
   *
   * @return \Drupal\ui_patterns\Definition\PatternDefinitionVariant
   *   Definition instance.
   */
  public function getVariantDefinition($name, $value) {
    return new PatternDefinitionVariant($name, $value);
  }

  /**
   * Return array definition.
   *
   * @return array
   *   Array definition.
   */
  public function toArray() {
    $definition = $this->definition;
    foreach ($this->getFields() as $field) {
      $definition['fields'][$field->getName()] = $field->toArray();
    }
    foreach ($this->getVariants() as $variant) {
      $definition['variants'][$variant->getName()] = $variant->toArray();
    }
    $definition['render_links'] = $this->getRenderLinks();

    return $definition;
  }

  /**
   * Render link.
   *
   * @param array $link
   *   A link from getLinks method.
   *
   * @return array
   *   The link render element.
   */
  protected function renderLink(array $link): array {
    $renderLink = [
      '#type' => 'link',
      '#title' => $link['title'],
    ];

    if (!empty($link['url'])) {
      $renderLink['#url'] = Url::fromUri($link['url']);
    }

    if (!empty($link['options'])) {
      $renderLink['#options'] = $link['options'];
    }

    if (!empty($link['attributes'])) {
      $renderLink['#attributes'] = $link['attributes'];
    }

    return $renderLink;
  }

}
