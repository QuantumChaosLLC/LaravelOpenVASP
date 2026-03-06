<?php

declare(strict_types=1);

namespace LaravelOpenVasp\Tests\Unit;

use LaravelOpenVasp\Support\Lei;
use LaravelOpenVasp\Tests\TestCase;

class LeiValidationTest extends TestCase
{
    public function test_it_accepts_valid_lei_with_correct_checksum(): void
    {
        $this->assertTrue(Lei::isValid('24IN00POZKARSTIN8350'));
        $this->assertTrue(Lei::isValid('FJUM00TVYIIPDDVIOV34'));
    }

    public function test_it_rejects_lei_with_invalid_checksum(): void
    {
        $this->assertFalse(Lei::isValid('24IN00POZKARSTIN8351'));
    }

    public function test_it_rejects_lei_with_invalid_format(): void
    {
        $this->assertFalse(Lei::isValid('invalid-lei'));
    }
}
