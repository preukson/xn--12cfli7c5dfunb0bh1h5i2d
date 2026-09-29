<?php

namespace Tests\Unit;

use App\Services\Lottery\TicketParser;
use PHPUnit\Framework\TestCase;

class TicketParserTest extends TestCase
{
    public function test_parses_mixed_separators_and_dedupes(): void
    {
        $result = (new TicketParser)->parse("730640\n417212, 004615 639214\n730640");

        $this->assertSame(['730640', '417212', '004615', '639214'], $result['numbers']);
        $this->assertSame([], $result['invalid']);
    }

    public function test_joins_numbers_typed_with_spaces_or_dashes(): void
    {
        $result = (new TicketParser)->parse("730 640\n417-212");

        $this->assertSame(['730640', '417212'], $result['numbers']);
    }

    public function test_converts_thai_digits(): void
    {
        $this->assertSame(['730640'], (new TicketParser)->parse('๗๓๐๖๔๐')['numbers']);
    }

    public function test_reports_invalid_chunks_instead_of_dropping_them(): void
    {
        $result = (new TicketParser)->parse("730640\n73064\nabc");

        $this->assertSame(['730640'], $result['numbers']);
        $this->assertSame(['73064', 'abc'], $result['invalid']);
    }
}
