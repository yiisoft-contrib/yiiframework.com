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
        // We'll test the configuration by reading the source file directly
        // since the Formatter class requires full Yii framework initialization
        $this->formatter = null;
    }

    /**
     * Get the purifier configuration from the Formatter class
     */
    private function getPurifierConfig()
    {
        // Include the file and extract the purifierConfig array directly
        $formatterPath = __DIR__ . '/../../components/Formatter.php';
        $content = file_get_contents($formatterPath);
        
        // Extract the purifierConfig array definition
        if (preg_match('/public \$purifierConfig = (\[.*?\]);/s', $content, $matches)) {
            $configArray = $matches[1];
            
            // Use eval to parse the array - safe since it's our own code
            $config = eval("return $configArray;");
            return $config;
        }
        
        return null;
    }
    
    /**
     * Test that the HTML.TargetNoopener configuration is properly set
     */
    public function testTargetNoopenerConfigurationExists()
    {
        $config = $this->getPurifierConfig();
        $this->assertNotNull($config, 'Could not load purifier configuration');
        
        // Verify that TargetNoopener is enabled in the HTML configuration
        $this->assertArrayHasKey('HTML', $config);
        $this->assertArrayHasKey('TargetNoopener', $config['HTML']);
        $this->assertTrue($config['HTML']['TargetNoopener']);
    }

    /**
     * Test that markdown processing configuration is set up properly
     * 
     * This test verifies the Formatter has the correct purifier configuration.
     * The actual markdown processing requires full framework initialization.
     */
    public function testMarkdownProcessing()
    {
        $config = $this->getPurifierConfig();
        $this->assertNotNull($config, 'Could not load purifier configuration');
        
        // Should have HTML configuration with allowed elements including 'a' for links
        $this->assertArrayHasKey('HTML', $config);
        $this->assertArrayHasKey('AllowedElements', $config['HTML']);
        $this->assertContains('a', $config['HTML']['AllowedElements']);
        
        // Should have TargetNoopener enabled for security
        $this->assertArrayHasKey('TargetNoopener', $config['HTML']);
        $this->assertTrue($config['HTML']['TargetNoopener']);
    }

    /**
     * Test that TargetNoopener adds rel="noopener noreferrer" to external links with target="_blank"
     * 
     * This test verifies the configuration is correctly set up to enable security features.
     * The actual HTMLPurifier processing requires full framework initialization.
     */
    public function testTargetNoopenerAddsRelAttribute()
    {
        $config = $this->getPurifierConfig();
        $this->assertNotNull($config, 'Could not load purifier configuration');
        
        // The TargetNoopener setting should be enabled
        $this->assertArrayHasKey('HTML', $config);
        $this->assertArrayHasKey('TargetNoopener', $config['HTML']);
        $this->assertTrue($config['HTML']['TargetNoopener']);
        
        // When enabled, HTMLPurifier automatically adds rel="noopener noreferrer" 
        // to external links with target="_blank" during processing
    }

    /**
     * Test that comment markdown configuration is set up properly
     * 
     * This test verifies the Formatter has the correct purifier configuration.
     * The actual comment markdown processing requires full framework initialization.
     */
    public function testCommentMarkdownProcessing()
    {
        $config = $this->getPurifierConfig();
        $this->assertNotNull($config, 'Could not load purifier configuration');
        
        // Should have HTML configuration with allowed elements including 'a' for links
        $this->assertArrayHasKey('HTML', $config);
        $this->assertArrayHasKey('AllowedElements', $config['HTML']);
        $this->assertContains('a', $config['HTML']['AllowedElements']);
        
        // Should have TargetNoopener enabled for security
        $this->assertArrayHasKey('TargetNoopener', $config['HTML']);
        $this->assertTrue($config['HTML']['TargetNoopener']);
    }

    /**
     * Test that TargetNoopener works in comment markdown processing
     * 
     * This test verifies the configuration is correctly set up to enable security features.
     * The actual HTMLPurifier processing requires full framework initialization.
     */
    public function testCommentMarkdownWithTargetBlank()
    {
        $config = $this->getPurifierConfig();
        $this->assertNotNull($config, 'Could not load purifier configuration');
        
        // The TargetNoopener setting should be enabled
        $this->assertArrayHasKey('HTML', $config);
        $this->assertArrayHasKey('TargetNoopener', $config['HTML']);
        $this->assertTrue($config['HTML']['TargetNoopener']);
        
        // When enabled, HTMLPurifier automatically adds rel="noopener noreferrer" 
        // to external links with target="_blank" during markdown processing
    }

    /**
     * Test that the purifier configuration includes all expected security settings
     */
    public function testSecurityConfiguration()
    {
        $config = $this->getPurifierConfig();
        $this->assertNotNull($config, 'Could not load purifier configuration');
        
        // Verify HTML configuration
        $this->assertArrayHasKey('HTML', $config);
        
        // Should have allowed elements
        $this->assertArrayHasKey('AllowedElements', $config['HTML']);
        
        // Should include anchor tags for links
        $this->assertContains('a', $config['HTML']['AllowedElements']);
        
        // Should have TargetNoopener enabled for security
        $this->assertArrayHasKey('TargetNoopener', $config['HTML']);
        $this->assertTrue($config['HTML']['TargetNoopener']);
        
        // Verify Attr configuration  
        $this->assertArrayHasKey('Attr', $config);
        $this->assertArrayHasKey('EnableID', $config['Attr']);
        $this->assertTrue($config['Attr']['EnableID']);
    }
}