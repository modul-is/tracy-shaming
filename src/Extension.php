<?php

declare(strict_types = 1);

namespace ModulIS\TracyShamming;

use Nette\DI\CompilerExtension;
use Nette\PhpGenerator\Literal;
use Nette\Schema\Expect;
use Nette\Schema\Schema;
use stdClass;
use Tracy\BlueScreen;
use Tracy\Debugger;

/**
 * @property-read stdClass $config
 */
final class Extension extends CompilerExtension
{

	public function getConfigSchema(): Schema
	{
		return Expect::structure([]);
	}

	public function loadConfiguration(): void
	{
		parent::loadConfiguration();

		self::setupPanel(Debugger::getBlueScreen(), true);
	}

	public function beforeCompile(): void
	{
		parent::beforeCompile();
		$builder = $this->getContainerBuilder();

		$this->getInitialization()->addBody('?::setupPanel($this->getService(?));', [
			new Literal(self::class),
			$builder->getDefinitionByType(BlueScreen::class)->getName(),
		]);
	}

	public static function setupPanel(BlueScreen $blueScreen, bool $renderOnceCheck = false): void
	{
		$blueScreen->addPanel(new Panel($renderOnceCheck));
	}

}