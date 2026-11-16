<?php

namespace Tests;

use Tests\Support\GrammarFixture;

/** Opt-in base class: the shared Tests\TestCase remains unchanged. */
abstract class GrammarTestCase extends IsolatedSqliteTestCase
{
    protected function setUp()
    {
        parent::setUp();
        GrammarFixture::load($this->app);
    }
}
