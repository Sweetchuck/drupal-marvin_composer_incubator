<?php

declare(strict_types = 1);

namespace Drush\Commands\marvin_composer_incubator;

use Drupal\marvin_incubator\CommandsBaseTrait;
use Drush\Commands\marvin_composer\ComposerCommandsBase;
use Robo\Collection\CollectionBuilder;
use Symfony\Component\Console\Input\InputInterface;

class ComposerValidateCommands extends ComposerCommandsBase {

  use CommandsBaseTrait;

  /**
   * @hook on-event marvin:git-hook:pre-commit
   *
   * @phpstan-return array<string, marvin-task-definition>
   */
  public function onEventMarvinGitHookPreCommit(InputInterface $input): array {
    $package = $this->normalizeManagedDrupalExtensionName($input->getArgument('packagePath'));

    return [
      'marvin:lint:composer-validate' => [
        'task' => $this->composerValidate([$package['name']]),
      ],
    ];
  }

  /**
   * Runs `composer validate`.
   *
   * @param string[] $packageNames
   *   Package names. See `drush marvin:managed-drupal-extension:list` for
   *   allowed values.
   *
   * @command marvin:lint:composer-validate
   * @bootstrap none
   *
   * @marvinArgPackages packageNames
   */
  public function composerValidate(array $packageNames): CollectionBuilder {
    $cb = $this->collectionBuilder();

    $managedDrupalExtensions = $this->getManagedDrupalExtensions();
    foreach ($packageNames as $packageName) {
      $package = $managedDrupalExtensions[$packageName];
      $cb->addTask($this->getTaskComposerValidate($package['path']));
    }

    return $cb;
  }

}
