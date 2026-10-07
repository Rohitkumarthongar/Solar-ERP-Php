<?php

namespace Tests\Unit;

use App\Http\Controllers\WebController;
use PHPUnit\Framework\TestCase;

class WebLeadNumberTest extends TestCase
{
    public function test_web_lead_numbers_are_distinct_and_outside_legacy_daily_suffix_pool(): void
    {
        $numbers = array_map(static fn (): string => WebController::newLeadNumber(), range(1, 2000));

        $this->assertCount(2000, array_unique($numbers));
        foreach ($numbers as $number) {
            $this->assertMatchesRegularExpression('/^LEAD-\d{8}-[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/', $number);
        }
    }
}
