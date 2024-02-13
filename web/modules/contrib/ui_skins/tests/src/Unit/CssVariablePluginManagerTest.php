<?php

declare(strict_types = 1);

namespace Drupal\Tests\ui_skins\Unit;

use Drupal\Component\Plugin\Exception\PluginException;
use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Extension\ThemeHandlerInterface;
use Drupal\Core\StringTranslation\TranslationInterface;
use Drupal\Tests\UnitTestCase;
use Drupal\ui_skins\CssVariable\CssVariablePluginManager;
use Drupal\ui_skins\Definition\CssVariableDefinition;
use Drupal\ui_skins_test\DummyCssVariablePluginManager;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Test the CSS variable plugin manager.
 *
 * @group ui_skins
 *
 * @coversDefaultClass \Drupal\ui_skins\CssVariable\CssVariablePluginManager
 */
class CssVariablePluginManagerTest extends UnitTestCase {

  /**
   * The container.
   *
   * @var \Symfony\Component\DependencyInjection\TaggedContainerInterface
   */
  protected $container;

  /**
   * The string translation service.
   *
   * @var \Drupal\Core\StringTranslation\TranslationInterface
   */
  protected TranslationInterface $stringTranslation;

  /**
   * The CSS variables plugin manager.
   *
   * @var \Drupal\ui_skins_test\DummyCssVariablePluginManager
   */
  protected DummyCssVariablePluginManager $cssVariablePluginManager;

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();
    $this->container = new ContainerBuilder();
    $this->container->set('string_translation', $this->getStringTranslationStub());

    // Set up for this class.
    /** @var \Drupal\Core\Extension\ModuleHandlerInterface|\PHPUnit\Framework\MockObject\MockObject $moduleHandler */
    $moduleHandler = $this->createMock(ModuleHandlerInterface::class);
    $moduleHandler->expects($this->any())
      ->method('getModuleDirectories')
      ->willReturn([]);

    /** @var \Drupal\Core\Extension\ThemeHandlerInterface|\PHPUnit\Framework\MockObject\MockObject $themeHandler */
    $themeHandler = $this->createMock(ThemeHandlerInterface::class);
    $themeHandler->expects($this->any())
      ->method('getThemeDirectories')
      ->willReturn([]);

    $cache = $this->createMock(CacheBackendInterface::class);
    $this->stringTranslation = $this->getStringTranslationStub();

    $this->cssVariablePluginManager = new DummyCssVariablePluginManager($cache, $moduleHandler, $themeHandler, $this->stringTranslation);
  }

  /**
   * Tests the constructor.
   *
   * @covers ::__construct
   */
  public function testConstructor(): void {
    $this->assertInstanceOf(
      CssVariablePluginManager::class,
      $this->cssVariablePluginManager
    );
  }

  /**
   * Tests the processDefinition().
   *
   * @covers ::processDefinition
   */
  public function testProcessDefinitionWillReturnException(): void {
    $plugin_id = 'test';
    $definition = ['no_id' => $plugin_id];
    try {
      $this->cssVariablePluginManager->processDefinition($definition, $plugin_id);
    }
    catch (PluginException $exception) {
      $this->assertTrue(TRUE, 'The expected exception happened.');
    }
  }

  /**
   * Tests the processDefinition().
   *
   * @covers ::processDefinition
   */
  public function testProcessDefinition(): void {
    $plugin_id = 'test';
    $definition = ['id' => $plugin_id];

    $expected = new CssVariableDefinition($definition);
    $expected->setCategory($this->stringTranslation->translate('Other'));

    /** @var \Drupal\ui_skins\Definition\CssVariableDefinition $definition */
    $this->cssVariablePluginManager->processDefinition($definition, $plugin_id);
    $this->assertInstanceOf(CssVariableDefinition::class, $definition);
    $this->assertEquals($definition->toArray(), $expected->toArray());
  }

  /**
   * @covers ::getCategories
   */
  public function testGetCategories(): void {
    $this->cssVariablePluginManager->setCssVariables([
      'id_1' => [
        'id' => 'id_1',
        'category' => 'Cat 1',
      ],
      'id_2' => [
        'id' => 'id_2',
        'category' => 'Cat 2',
      ],
      'id_3' => [
        'id' => 'id_3',
      ],
    ]);
    $expected = [
      'Cat 1',
      'Cat 2',
      'Other',
    ];
    $categories = $this->cssVariablePluginManager->getCategories();
    $this->assertEquals($expected, $categories);
  }

  /**
   * @covers ::getSortedDefinitions
   */
  public function testGetSortedDefinitions(): void {
    $this->cssVariablePluginManager->setCssVariables([
      'id_z1z2' => [
        'category' => 'Z',
        'weight' => 1,
        'label' => '(Z)',
        'id' => 'id_z1z2',
      ],
      'id_z1z1' => [
        'category' => 'Z',
        'weight' => 1,
        'label' => 'Z',
        'id' => 'id_z1z1',
      ],
      'id_z1a2' => [
        'category' => 'Z',
        'weight' => 1,
        'label' => '(A)',
        'id' => 'id_z1a2',
      ],
      'id_z1a1' => [
        'category' => 'Z',
        'weight' => 1,
        'label' => 'A',
        'id' => 'id_z1a1',
      ],
      'id_z0z2' => [
        'category' => 'Z',
        'weight' => 0,
        'label' => '(Z)',
        'id' => 'id_z0z2',
      ],
      'id_z0z1' => [
        'category' => 'Z',
        'weight' => 0,
        'label' => 'Z',
        'id' => 'id_z0z1',
      ],
      'id_z0a2' => [
        'category' => 'Z',
        'weight' => 0,
        'label' => '(A)',
        'id' => 'id_z0a2',
      ],
      'id_z0a1' => [
        'category' => 'Z',
        'weight' => 0,
        'label' => 'A',
        'id' => 'id_z0a1',
      ],
      'id_a1z2' => [
        'category' => 'A',
        'weight' => 1,
        'label' => '(Z)',
        'id' => 'id_a1z2',
      ],
      'id_a1z1' => [
        'category' => 'A',
        'weight' => 1,
        'label' => 'Z',
        'id' => 'id_a1z1',
      ],
      'id_a1a2' => [
        'category' => 'A',
        'weight' => 1,
        'label' => '(A)',
        'id' => 'id_a1a2',
      ],
      'id_a1a1' => [
        'category' => 'A',
        'weight' => 1,
        'label' => 'A',
        'id' => 'id_a1a1',
      ],
      'id_a0z2' => [
        'category' => 'A',
        'weight' => 0,
        'label' => '(Z)',
        'id' => 'id_a0z2',
      ],
      'id_a0z1' => [
        'category' => 'A',
        'weight' => 0,
        'label' => 'Z',
        'id' => 'id_a0z1',
      ],
      'id_a0a2' => [
        'category' => 'A',
        'weight' => 0,
        'label' => '(A)',
        'id' => 'id_a0a2',
      ],
      'id_a0a1' => [
        'category' => 'A',
        'weight' => 0,
        'label' => 'A',
        'id' => 'id_a0a1',
      ],
    ]);

    $expected = [
      'id_a0a1',
      'id_a0a2',
      'id_a0z1',
      'id_a0z2',
      'id_a1a1',
      'id_a1a2',
      'id_a1z1',
      'id_a1z2',
      'id_z0a1',
      'id_z0a2',
      'id_z0z1',
      'id_z0z2',
      'id_z1a1',
      'id_z1a2',
      'id_z1z1',
      'id_z1z2',
    ];

    $sorted_definitions = $this->cssVariablePluginManager->getSortedDefinitions();
    $this->assertEquals($expected, \array_keys($sorted_definitions));
    $this->assertContainsOnlyInstancesOf(CssVariableDefinition::class, $sorted_definitions);
  }

  /**
   * @covers ::getGroupedDefinitions
   */
  public function testGetGroupedDefinitions(): void {
    $this->cssVariablePluginManager->setCssVariables([
      'cat_1_1_b' => [
        'id' => 'cat_1_1_b',
        'category' => 'Cat 1',
        'label' => 'B',
        'weight' => 1,
      ],
      'cat_1_1_a' => [
        'id' => 'cat_1_1_a',
        'category' => 'Cat 1',
        'label' => 'A',
        'weight' => 1,
      ],
      'cat_1_0_a' => [
        'id' => 'cat_1_0_a',
        'category' => 'Cat 1',
        'label' => 'A',
        'weight' => 0,
      ],
      'cat_2_0_a' => [
        'id' => 'cat_1_0_a',
        'category' => 'Cat 2',
        'label' => 'A',
        'weight' => 0,
      ],
      'no_category' => [
        'id' => 'no_category',
        'label' => 'B',
        'weight' => 0,
      ],
    ]);

    $category_expected = [
      'Cat 1' => [
        'cat_1_0_a',
        'cat_1_1_a',
        'cat_1_1_b',
      ],
      'Cat 2' => [
        'cat_2_0_a',
      ],
      'Other' => [
        'no_category',
      ],
    ];

    $definitions = $this->cssVariablePluginManager->getGroupedDefinitions();
    $this->assertEquals(\array_keys($category_expected), \array_keys($definitions));
    foreach ($category_expected as $category => $expected) {
      $this->assertArrayHasKey($category, $definitions);
      $this->assertEquals($expected, \array_keys($definitions[$category]));
      $this->assertContainsOnlyInstancesOf(CssVariableDefinition::class, $definitions[$category]);
    }
  }

}
