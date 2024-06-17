<?php

declare(strict_types=1);

namespace Atoolo\Runtime\Composer;

use Composer\Composer;
use Composer\Json\JsonFile;
use Composer\Json\JsonManipulator;
use JsonException;
use RuntimeException;

class ComposerJson
{
    private JsonFile $jsonFile;

    private string $content;

    /**
     * @var array{
     *   autoload?: array{
     *     files?: array<string>
     *   }
     * }
     */
    private array $jsonContent;

    public function __construct(private readonly Composer $composer)
    {
    }

    public function getPath(): string
    {
        return $this->jsonFile->getPath();
    }

    /**
     * @throws JsonException
     */
    public function load(string $composerJsonFile): void
    {
        $composerJsonFile = realpath($composerJsonFile);
        if ($composerJsonFile === false) {
            throw new RuntimeException(
                "Failed to resolve composer.json file: $composerJsonFile"
            );
        }
        $this->jsonFile = new JsonFile($composerJsonFile);
        $content = @file_get_contents($composerJsonFile);
        if ($content === false) {
            throw new RuntimeException(
                "Failed to read composer.json file: $composerJsonFile"
            );
        }
        $this->content = $content;
        $jsonContent = json_decode(
            $this->content,
            true,
            512,
            JSON_THROW_ON_ERROR
        );

        if (!is_array($jsonContent)) {
            throw new JsonException(
                "Failed to parse composer.json file: $composerJsonFile"
            );
        }

        $this->jsonContent = $jsonContent;
    }

    /**
     * @throws JsonException
     */
    public function addAutoloadFile(string $autoloadFile): bool
    {
        $autoloadFiles = $this->jsonContent['autoload']['files'] ?? [];

        if (in_array($autoloadFile, $autoloadFiles)) {
            return false;
        }
        $autoloadFiles[] = $autoloadFile;
        $manipulator = new JsonManipulator($this->content);
        $manipulator->addSubNode('autoload', 'files', $autoloadFiles);
        file_put_contents(
            $this->jsonFile->getPath(),
            $manipulator->getContents()
        );
        $this->load($this->jsonFile->getPath());

        $this->updateAutoloadConfig();

        return true;
    }

    public function removeAutoloadFile(string $autoloadFile): bool
    {
        $autoloadFiles = $this->jsonContent['autoload']['files'] ?? [];

        $key = array_search($autoloadFile, $autoloadFiles, true);
        if ($key === false) {
            return false;
        }
        unset($autoloadFiles[$key]);
        $manipulator = new JsonManipulator($this->content);
        if (empty($autoloadFiles)) {
            $manipulator->removeSubNode('autoload', 'files');
            unset($this->jsonContent['autoload']['files']);
        } else {
            $manipulator->addSubNode('autoload', 'files', $autoloadFiles);
            $this->jsonContent['autoload']['files'] = $autoloadFiles;
        }
        file_put_contents(
            $this->jsonFile->getPath(),
            $manipulator->getContents()
        );

        $this->updateAutoloadConfig();

        return true;
    }

    public function updateAutoloadConfig(): void
    {
        $this->composer->getPackage()->setAutoload(
            $this->jsonContent['autoload'] ?? []
        );
    }
}
