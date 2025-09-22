<?php

use Illuminate\Foundation\Testing\DatabaseTransactions;

class LogoReplacementTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * Test that report templates use Involved Talent logo instead of AOE Science logo
     */
    public function testReportTemplatesUseInvolvedTalentLogo()
    {
        // Test the main report cover template
        $cover_template = file_get_contents(base_path('resources/views/dashboard/reports/partials/_cover.blade.php'));
        
        // Check that template contains the new logo
        $this->assertContains('logo-small.png', $cover_template);
        
        // Check that template doesn't contain old AOE logo
        $this->assertNotContains('report-logo-1.png', $cover_template);
        $this->assertNotContains('aoe-science_logo.png', $cover_template);
    }

    /**
     * Test that report templates have been updated to use new logo
     */
    public function testAllReportTemplatesUpdated()
    {
        // Get list of report template files
        $report_files = glob(base_path('resources/views/reports/*.blade.php'));
        
        $this->assertGreaterThan(0, count($report_files), 'No report template files found');
        
        foreach ($report_files as $file) {
            $content = file_get_contents($file);
            
            // Check that file doesn't contain old AOE logo references
            $this->assertNotContains('report-logo-1.png', $content, 
                "File " . basename($file) . " still contains old AOE logo reference");
        }
    }

    /**
     * Test that logo file exists
     */
    public function testInvolvedTalentLogoExists()
    {
        $logo_path = public_path('assets/images/logo-small.png');
        
        $this->assertFileExists($logo_path, 'Involved Talent small logo file does not exist');
        
        // Check that it's a valid image file
        $image_info = getimagesize($logo_path);
        $this->assertNotFalse($image_info, 'Logo file is not a valid image');
        $this->assertEquals('image/png', $image_info['mime'], 'Logo file is not a PNG image');
    }

    /**
     * Test that logo is properly referenced in templates
     */
    public function testLogoProperlyReferenced()
    {
        // Test a few specific report templates
        $test_files = [
            'resources/views/reports/pwms.blade.php',
            'resources/views/reports/test.blade.php',
            'resources/views/reports/modelp.blade.php'
        ];
        
        foreach ($test_files as $file) {
            if (file_exists(base_path($file))) {
                $content = file_get_contents(base_path($file));
                
                // Check that file contains proper asset reference
                $this->assertContains('{{ asset("assets/images/logo-small.png") }}', $content,
                    "File $file does not contain proper asset reference for logo");
            }
        }
    }
}
