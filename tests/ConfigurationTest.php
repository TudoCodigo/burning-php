<?php

declare(strict_types = 1);

namespace TudoCodigo\BurningPHP\Tests;

use TudoCodigo\BurningPHP\Configuration;

class ConfigurationTest
    extends TestCaseAbstract
{
    public function testConfiguration(): void
    {
        $configuration = Configuration::getInstance(true);

        $configuration->returnsBooleanFunctions = [ 'is_file' ];

        static::assertSame([ 'is_file' ], $configuration->returnsBooleanFunctions);

        $configuration->mergeAttributesWith([ 'returnsBooleanFunctions' => [ 'is_string' ] ]);

        static::assertSame([ 'is_string' ], $configuration->returnsBooleanFunctions);

        $configuration->mergeAttributesWith([ 'returnsBooleanFunctionsSkipParents' => [ '@parent', 'is_file', '@parent' ] ]);

        static::assertSame([ 'is_file' ], $configuration->__get('returnsBooleanFunctionsSkipParents'),
            '@parent values should be filtered because this key does not exists on current configuration attributes');

        $configuration->mergeAttributesWith([ 'returnsBooleanFunctions' => [ '@parent', 'is_file', '@parent' ] ]);

        static::assertSame([ 'is_string', 'is_file', 'is_string' ], $configuration->returnsBooleanFunctions);
    }
}
