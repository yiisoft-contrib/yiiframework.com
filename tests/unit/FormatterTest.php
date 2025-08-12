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
        
        // Should have TargetNoopener enabled for security
        $this->assertArrayHasKey('TargetNoopener', $htmlConfig);
        $this->assertTrue($htmlConfig['TargetNoopener']);
        
        // Verify Attr configuration  
        $this->assertArrayHasKey('Attr', $config);
        $this->assertArrayHasKey('EnableID', $config['Attr']);
        $this->assertTrue($config['Attr']['EnableID']);
    }
}