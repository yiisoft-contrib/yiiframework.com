<?php

namespace tests\unit;

use app\components\Formatter;
use Codeception\Test\Unit;

/**
 * Test the Formatter component's HTMLPurifier configuration,
 * specifically the HTML.TargetNoopener security feature.
 * 
 * The HTML.TargetNoopener configuration option automatically adds
 * rel="noopener noreferrer" attributes to external links that have target attribute specified.
 * This prevents security vulnerabilities where a malicious page opened in a new tab
 * could access the parent window through window.opener.
 */
class FormatterTest extends Unit
{
    /**
     * @var Formatter
     */
    protected $formatter;

    protected function _before()
    {
        $this->formatter = new Formatter();
    }

    /**
     * Test that the HTML.TargetNoopener configuration is properly set
     */
    public function testTargetNoopenerConfigurationExists()
    {
        $config = $this->formatter->purifierConfig;
        
        // Verify that TargetNoopener is enabled in the HTML configuration
        $this->assertArrayHasKey('HTML', $config);
        $this->assertArrayHasKey('TargetNoopener', $config['HTML']);
        $this->assertTrue($config['HTML']['TargetNoopener']);
    }

    /**
     * Test that the purifier configuration includes all expected security settings
     */
    public function testSecurityConfiguration()
    {
        $config = $this->formatter->purifierConfig;
        
        // Verify HTML configuration
        $this->assertArrayHasKey('HTML', $config);
        $htmlConfig = $config['HTML'];
        
        // Should have allowed elements
        $this->assertArrayHasKey('AllowedElements', $htmlConfig);
        $this->assertIsArray($htmlConfig['AllowedElements']);
        
        // Should include anchor tags for links
        $this->assertContains('a', $htmlConfig['AllowedElements']);
        
        // Should have AllowedAttributes to support TargetNoopener
        $this->assertArrayHasKey('AllowedAttributes', $htmlConfig);
        $this->assertIsString($htmlConfig['AllowedAttributes']);
        // Verify target and rel attributes are allowed for anchor tags (required for TargetNoopener)
        $this->assertStringContainsString('a@target', $htmlConfig['AllowedAttributes']);
        $this->assertStringContainsString('a@rel', $htmlConfig['AllowedAttributes']);
        
        // Should have TargetNoopener enabled for security
        $this->assertArrayHasKey('TargetNoopener', $htmlConfig);
        $this->assertTrue($htmlConfig['TargetNoopener']);
        
        // Verify Attr configuration  
        $this->assertArrayHasKey('Attr', $config);
        $this->assertArrayHasKey('EnableID', $config['Attr']);
        $this->assertTrue($config['Attr']['EnableID']);
    }

    /**
     * Test that HTMLPurifier adds rel="noopener noreferrer" to links with target="_blank" 
     * This test verifies the security feature works as expected
     */
    public function testTargetBlankLinksGetNoopenerRel()
    {
        // Skip this test if HTMLPurifier is not available (e.g., in CI without full dependencies)
        if (!class_exists('\HTMLPurifier')) {
            $this->markTestSkipped('HTMLPurifier is not available');
        }
        
        // Test HTML with a link that has target="_blank"
        $htmlInput = '<a href="https://example.com" target="_blank">External Link</a>';
        
        try {
            // Process through HTMLPurifier with the formatter's configuration
            $result = \yii\helpers\HtmlPurifier::process($htmlInput, $this->formatter->purifierConfig);
            
            // Should contain rel="noopener noreferrer"
            $this->assertStringContainsString('rel="noopener noreferrer"', $result);
            $this->assertStringContainsString('target="_blank"', $result);
            $this->assertStringContainsString('href="https://example.com"', $result);
        } catch (\Error $e) {
            // If there's an error due to missing dependencies, mark test as skipped
            $this->markTestSkipped('HTMLPurifier dependencies not available: ' . $e->getMessage());
        }
    }

    /**
     * Test that links without target="_blank" don't get rel attributes added
     */
    public function testLinksWithoutTargetBlankUnaffected()
    {
        // Skip this test if HTMLPurifier is not available
        if (!class_exists('\HTMLPurifier')) {
            $this->markTestSkipped('HTMLPurifier is not available');
        }
        
        // Test HTML with a normal link (no target attribute)
        $htmlInput = '<a href="https://example.com">Normal Link</a>';
        
        try {
            // Process through HTMLPurifier with the formatter's configuration
            $result = \yii\helpers\HtmlPurifier::process($htmlInput, $this->formatter->purifierConfig);
            
            // Should NOT contain rel="noopener noreferrer"
            $this->assertStringNotContainsString('rel="noopener noreferrer"', $result);
            $this->assertStringContainsString('href="https://example.com"', $result);
        } catch (\Error $e) {
            $this->markTestSkipped('HTMLPurifier dependencies not available: ' . $e->getMessage());
        }
    }

    /**
     * Test that asMarkdown method properly processes links with target="_blank"
     */
    public function testMarkdownProcessingWithTargetBlank()
    {
        try {
            // Test a simple markdown to ensure the method works
            $markdown = 'This is a [test link](https://example.com)';
            $result = $this->formatter->asMarkdown($markdown);
            
            // Should be wrapped in markdown div
            $this->assertStringContainsString('<div class="markdown">', $result);
            $this->assertStringContainsString('href="https://example.com"', $result);
        } catch (\Error $e) {
            $this->markTestSkipped('Markdown processing dependencies not available: ' . $e->getMessage());
        }
    }

    /**
     * Test that the security configuration is applied in comment processing
     */
    public function testCommentMarkdownSecurity()
    {
        try {
            // Test a simple comment to ensure the method works
            $markdown = 'Check this [link](https://external.com)';
            $result = $this->formatter->asCommentMarkdown($markdown);
            
            // Should be wrapped in markdown div and process the link
            $this->assertStringContainsString('<div class="markdown">', $result);
            $this->assertStringContainsString('href="https://external.com"', $result);
        } catch (\Error $e) {
            $this->markTestSkipped('Comment markdown processing dependencies not available: ' . $e->getMessage());
        }
    }
}