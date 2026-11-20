<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use RuntimeException;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        // Berhenti sebelum migrasi bila bukan database uji
        if (! str_ends_with((string) getenv('DB_DATABASE'), '_test')) {
            throw new RuntimeException('Pengujian hanya boleh memakai database berakhiran _test.');
        }

        parent::setUp();

         // Pengujian tidak butuh hasil build frontend
        $this->withoutVite();
    }
}
