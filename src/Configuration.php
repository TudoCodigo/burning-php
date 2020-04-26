<?php

declare(strict_types = 1);

namespace TudoCodigo\BurningPHP;

use Symfony\Component\Finder\Finder;
use TudoCodigo\BurningPHP\Support\Deterministic;
use TudoCodigo\BurningPHP\Support\Hash;
use TudoCodigo\BurningPHP\Support\Traits\HasAttributesTrait;
use TudoCodigo\BurningPHP\Support\Traits\SingletonPatternTrait;

/**
 * @property string|null $version
 * @property string|null $sourceHash
 *
 * @property bool        $disableXdebug
 *
 * @property string[]    $returnsBooleanFunctions
 *
 * @property bool        $sessionLastDirectoryEnabled
 * @property string      $sessionDirectoryFormat
 */
class Configuration
    implements \JsonSerializable
{
    use HasAttributesTrait,
        SingletonPatternTrait;

    private const
        BURNING_CONFIGURATION_FILE = __DIR__ . '/../burning.json',
        BURNING_COMPOSER_FILE = __DIR__ . '/../composer.json';

    private array $targetComposer = [];

    public string $currentWorkingDir;

    public function __construct()
    {
        $configurationFile = realpath(self::BURNING_CONFIGURATION_FILE);

        $this->mergeWith($configurationFile);

        $this->currentWorkingDir = getcwd();

        $userConfigurationFile = realpath(getcwd() . '/burning.json') ?: null;

        if ($configurationFile !== $userConfigurationFile) {
            $this->mergeWith($userConfigurationFile);
        }

        $targetComposerFile = $this->currentWorkingDir . '/composer.json';

        if (is_readable($targetComposerFile)) {
            $this->targetComposer = json_decode(file_get_contents($targetComposerFile), true, 512, JSON_THROW_ON_ERROR) ?: [];
        }

        $selfComposer = json_decode(file_get_contents(self::BURNING_COMPOSER_FILE), true, 512, JSON_THROW_ON_ERROR);

        $this->version    = $selfComposer['version'];
        $this->sourceHash = $this->getBurningSourceHash();
    }

    private function mergeWith(?string $configurationFile): void
    {
        if ($configurationFile !== null && is_readable($configurationFile)) {
            $this->mergeAttributesWith(json_decode(file_get_contents($configurationFile), true, 512, JSON_THROW_ON_ERROR));
        }
    }

    public function getBurningSourceHash(): string
    {
        return Deterministic::withClosure(static function () {
            $finder = new Finder;
            $finder->files()->in(__DIR__);

            $filesModificationTime = [];

            foreach ($finder as $file) {
                $filesModificationTime[] = $file->getMTime();
            }

            return Hash::shortify(implode(',', $filesModificationTime));
        });
    }

    public function getBurningVersionInt(): int
    {
        [ $majorVersion, $minorVersion, $patchVersion ] = explode('.', $this->version);

        return $majorVersion * 10000 + $minorVersion * 100 + $patchVersion;
    }

    public function getControlDirectory(?string $additionalPath = null): string
    {
        return $this->currentWorkingDir . '/.burning' . $additionalPath;
    }

    /**
     * @return string[]
     */
    public function getTargetDevelopmentPaths(): array
    {
        return Deterministic::withClosure(function () {
            return array_filter(array_map(function (string $path) {
                return realpath($this->currentWorkingDir . DIRECTORY_SEPARATOR . $path) . DIRECTORY_SEPARATOR;
            }, array_filter(array_merge(
                $this->targetComposer['autoload-dev']['files'] ?? [],
                $this->targetComposer['autoload-dev']['psr-4'] ?? [],
                $this->targetComposer['autoload']['exclude-from-classmap'] ?? []
            ))));
        });
    }
}
