<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
// Import the trait
use Illuminate\Foundation\Testing\RefreshDatabase; 

abstract class TestCase extends BaseTestCase
{
    // Use the trait in the base class
    use CreatesApplication, RefreshDatabase; 
}