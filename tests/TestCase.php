<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Http;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // ห้าม test ยิง API ของ GLO จริง (ระบบดึงผลสำรองจากหน้าเว็บอาจทำงานตามเวลาจริง)
        Http::preventStrayRequests();
    }
}
