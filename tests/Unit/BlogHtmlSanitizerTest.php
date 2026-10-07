<?php

namespace Tests\Unit;

use App\Services\BlogHtmlSanitizer;
use PHPUnit\Framework\TestCase;

class BlogHtmlSanitizerTest extends TestCase
{
    public function test_allows_formatting_but_removes_active_markup_and_dangerous_links(): void
    {
        $clean = (new BlogHtmlSanitizer)->clean('<h2>Solar</h2><p onclick="evil()">Hello <strong>world</strong> '
            .'<a href="javascript:alert(1)" style="color:red">bad</a> '
            .'<a href="https://example.test">good</a></p><script>alert(1)</script><svg onload="evil()"></svg>');

        $this->assertStringContainsString('<h2>Solar</h2>', $clean);
        $this->assertStringContainsString('<strong>world</strong>', $clean);
        $this->assertStringContainsString('href="https://example.test"', $clean);
        $this->assertStringNotContainsString('javascript:', $clean);
        $this->assertStringNotContainsString('onclick', $clean);
        $this->assertStringNotContainsString('alert(1)', $clean);
        $this->assertStringNotContainsString('<svg', $clean);
    }
}
