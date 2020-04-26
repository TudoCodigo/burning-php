<?php

declare(strict_types = 1);

namespace TudoCodigo\BurningPHP\Tests\Processor;

use Symfony\Component\Finder\Finder;
use TudoCodigo\BurningPHP\Processor\Processor;
use TudoCodigo\BurningPHP\Support\Directory;
use TudoCodigo\BurningPHP\Tests\TestCaseAbstract;

class ProcessorTest
    extends TestCaseAbstract
{
    public function dataProviderProcessor(): array
    {
        $finderFiles = new Finder;
        $finderFiles->files()
            ->in(__DIR__ . '/tests')
            ->name('*.test');

        $testFiles = [];

        foreach ($finderFiles as $finderFile) {
            [ $message, $source, $processedSource ] = array_map('trim', explode('---', $finderFile->getContents()));

            $testFiles[$finderFile->getRelativePathname()] = [ $source, $processedSource, $message ];
        }

        return $testFiles;
    }

    /** @dataProvider dataProviderProcessor */
    public function testProcessor(string $phpSource, string $expectedProcessedPhpSource, string $message): void
    {
        static::prepareBurningConfigurationForTesting();

        $temporarySourcePath = Directory::temporaryFilename();

        file_put_contents($temporarySourcePath, $phpSource);

        $processorFile = Processor::getInstance(true)->process($temporarySourcePath);

        static::assertSame(
            strtr($expectedProcessedPhpSource, [
                '{{__DIR__}}'  => addcslashes(dirname($temporarySourcePath), '\\'),
                '{{__FILE__}}' => addcslashes($temporarySourcePath, '\\')
            ]),
            file_get_contents($processorFile->getExecutableSourcePath()),
            $message
        );

        unlink($temporarySourcePath);

        Processor::destructInstance();
    }
}
