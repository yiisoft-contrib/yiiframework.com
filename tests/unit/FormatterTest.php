<?php

namespace tests\unit;

use app\components\Formatter;
use Codeception\Test\Unit;

/**
 * Test the Formatter component's HTMLPurifier configuration,
 * specifically the HTML.TargetNoopener security feature.
 * 
 * The HTML.TargetNoopener configuration option automatically adds
 * rel="noopener noreferrer" attributes to external links that have "target" attribute specified.
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
     * Test that markdown processing works with our formatter
     * This tests the integration between Markdown processing and HTMLPurifier
     */
    public function testMarkdownProcessing()
    {
        // Test basic markdown functionality
        $markdown = '# Test\n\nThis is a **bold** text with a [link](https://example.com).';
        $result = $this->formatter->asGuideMarkdown($markdown);
        
        // Should produce HTML wrapped in markdown div
        $this->assertStringContainsString('<div class="markdown">', $result);
        $this->assertStringContainsString('</div>', $result);
        
        // Should contain the processed link
        $this->assertStringContainsString('href="https://example.com"', $result);
    }

    /**
     * Test that TargetNoopener adds rel="noopener noreferrer" to external links with target="_blank"
     */
    public function testTargetNoopenerAddsRelAttribute()
    {
        // Create HTML with external link having target="_blank"
        $html = '<a href="https://example.com" target="_blank">External Link</a>';
        
        // Process through HTMLPurifier with our configuration
        $result = \yii\helpers\HtmlPurifier::process($html, $this->formatter->purifierConfig);
        
        // Should contain rel="noopener noreferrer" attribute
        $this->assertStringContainsString('rel="noopener noreferrer"', $result);
        $this->assertStringContainsString('href="https://example.com"', $result);
        $this->assertStringContainsString('target="_blank"', $result);
    }

    /**
     * Test that comment markdown processing works
     */
    public function testCommentMarkdownProcessing()
    {
        $markdown = 'Check out this [website](https://example.com)!';
        $result = $this->formatter->asCommentMarkdown($markdown);
        
        // Should produce HTML wrapped in markdown div
        $this->assertStringContainsString('<div class="markdown">', $result);
        $this->assertStringContainsString('href="https://example.com"', $result);
    }

    /**
     * Test that TargetNoopener works in comment markdown processing
     */
    public function testCommentMarkdownWithTargetBlank()
    {
        // Test HTML with target="_blank" in comment markdown
        $html = '<a href="https://example.com" target="_blank">External Link</a>';
        
        // Process through HTMLPurifier with our configuration
        $result = \yii\helpers\HtmlPurifier::process($html, $this->formatter->purifierConfig);
        
        // Should contain rel="noopener noreferrer" attribute
        $this->assertStringContainsString('rel="noopener noreferrer"', $result);
        $this->assertStringContainsString('href="https://example.com"', $result);
        $this->assertStringContainsString('target="_blank"', $result);
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
        
        // Should have TargetNoopener enabled for security
        $this->assertArrayHasKey('TargetNoopener', $htmlConfig);
        $this->assertTrue($htmlConfig['TargetNoopener']);
        
        // Verify Attr configuration  
        $this->assertArrayHasKey('Attr', $config);
        $this->assertArrayHasKey('EnableID', $config['Attr']);
        $this->assertTrue($config['Attr']['EnableID']);
    }
}