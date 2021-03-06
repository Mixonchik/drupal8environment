<?php

namespace Drupal\Tests\views\Kernel;

use Drupal\Core\Render\BubbleableMetadata;
use Drupal\views\Views;

/**
 * Tests core view token replacement.
 *
 * @group views
 */
class TokenReplaceTest extends ViewsKernelTestBase {

  public static $modules = array('system');

  /**
   * Views used by this test.
   *
   * @var array
   */
  public static $testViews = array('test_tokens', 'test_invalid_tokens');

  protected function setUp($import_test_views = TRUE) {
    parent::setUp();
    $this->container->get('router.builder')->rebuild();
  }

  /**
   * Tests core token replacements generated from a view.
   */
  function testTokenReplacement() {
    $token_handler = \Drupal::token();
    $view = Views::getView('test_tokens');
    $view->setDisplay('page_1');
    $this->executeView($view);

    $expected = array(
      '[view:label]' => 'Test tokens',
      '[view:description]' => 'Test view to token replacement tests.',
      '[view:id]' => 'test_tokens',
      '[view:title]' => 'Test token page',
      '[view:url]' => $view->getUrl(NULL, 'page_1')->setAbsolute(TRUE)->toString(),
      '[view:total-rows]' => (string) $view->total_rows,
      '[view:base-table]' => 'views_test_data',
      '[view:base-field]' => 'id',
      '[view:items-per-page]' => '10',
      '[view:current-page]' => '1',
      '[view:page-count]' => '1',
    );

    $base_bubbleable_metadata = BubbleableMetadata::createFromObject($view->storage);
    $metadata_tests = [];
    $metadata_tests['[view:label]'] = $base_bubbleable_metadata;
    $metadata_tests['[view:description]'] = $base_bubbleable_metadata;
    $metadata_tests['[view:id]'] = $base_bubbleable_metadata;
    $metadata_tests['[view:title]'] = $base_bubbleable_metadata;
    $metadata_tests['[view:url]'] = $base_bubbleable_metadata;
    $metadata_tests['[view:total-rows]'] = $base_bubbleable_metadata;
    $metadata_tests['[view:base-table]'] = $base_bubbleable_metadata;
    $metadata_tests['[view:base-field]'] = $base_bubbleable_metadata;
    $metadata_tests['[view:items-per-page]'] = $base_bubbleable_metadata;
    $metadata_tests['[view:current-page]'] = $base_bubbleable_metadata;
    $metadata_tests['[view:page-count]'] = $base_bubbleable_metadata;

    foreach ($expected as $token => $expected_output) {
      $bubbleable_metadata = new BubbleableMetadata();
      $output = $token_handler->replace($token, array('view' => $view), [], $bubbleable_metadata);
      $this->assertIdentical($output, $expected_output, format_string('Token %token replaced correctly.', array('%token' => $token)));
      $this->assertEqual($bubbleable_metadata, $metadata_tests[$token]);
    }
  }

  /**
   * Tests core token replacements generated from a view without results.
   */
  function testTokenReplacementNoResults() {
    $token_handler = \Drupal::token();
    $view = Views::getView('test_tokens');
    $view->setDisplay('page_2');
    $this->executeView($view);

    $expected = array(
      '[view:page-count]' => '1',
    );

    foreach ($expected as $token => $expected_output) {
      $output = $token_handler->replace($token, array('view' => $view));
      $this->assertIdentical($output, $expected_output, format_string('Token %token replaced correctly.', array('%token' => $token)));
    }
  }

  /**
   * Tests path token replacements generated from a view without a path.
   */
  function testTokenReplacementNoPath() {
    $token_handler = \Drupal::token();
    $view = Views::getView('test_invalid_tokens');
    $view->setDisplay('block_1');
    $this->executeView($view);

    $expected = array(
      '[view:url]' => '',
    );

    foreach ($expected as $token => $expected_output) {
      $output = $token_handler->replace($token, array('view' => $view));
      $this->assertIdentical($output, $expected_output, format_string('Token %token replaced correctly.', array('%token' => $token)));
    }
  }

}
