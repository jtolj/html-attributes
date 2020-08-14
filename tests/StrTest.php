<?php

namespace Jtolj\HtmlAttributes\Test;

use PHPUnit\Framework\TestCase;
use Jtolj\HtmlAttributes\Helpers\Str;

class StrTest extends TestCase
{

    /** @test */
    public function endwith_returns_false_with_missing_suffix()
    {
        $string = 'MyTestValue';

        $this->assertFalse(Str::endsWith($string, 'MissingSuffix'));
    }

    /** @test */
    public function endwith_returns_true_with_existing_suffix()
    {
        $string = 'MyTestValue';

        $this->assertTrue(Str::endsWith($string, 'Value'));
    }

    /** @test */
    public function extract_suffix_returns_void_with_missing_suffix()
    {
        $string = 'MyTestValue';
        $suffix = Str::extractSuffix($string, ['MissingSuffix', 'AnotherMissingSuffix']);

        $this->assertNull($suffix);
    }

    /** @test */
    public function extract_suffix_returns_string_with_existing_suffix()
    {
        $string = 'MyTestValue';
        $suffix = Str::extractSuffix($string, ['Value', 'MissingSuffix']);

        $this->assertEquals($suffix, 'Value');
    }
}
