<?php

declare(strict_types = 1);

namespace ModulIS\TracyShaming;

use Nette\Neon\Neon;
use Throwable;
use Tracy\Helpers;
use function addcslashes;
use function array_rand;
use function base64_encode;
use function file_get_contents;

final class Panel
{
	private static bool $renderOnceCheck = false;

	private static bool $isRendered = false;

	public function __construct(bool $renderOnceCheck = false)
	{
		if ($renderOnceCheck)
		{
			self::$renderOnceCheck = $renderOnceCheck;
		}
	}

	/**
	 * @return array{tab: string, panel: string}|null
	 */
	public function __invoke(?Throwable $e): ?array
	{
		if($e !== null)
		{
			return null;
		}

		if(self::$renderOnceCheck)
		{
			if(self::$isRendered)
			{
				return null;
			}

			self::$isRendered = true;
		}

		return [
			'tab' => 'Test',
			'panel' => Helpers::capture(function (): void
			{
				$textList = Neon::decodeFile(__DIR__ . DIRECTORY_SEPARATOR . 'data.neon');

				$person = array_rand($textList);

				$message = $textList[$person][array_rand($textList[$person])];
				$message = addcslashes($message, "\x00..\x1F!\"#$%&'()*+,./:;<=>?@[\\]^`{|}~");

				$pet = 'data:image/png;base64,' . base64_encode(file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . 'asset' . DIRECTORY_SEPARATOR . $person));

				require __DIR__ . '/panel.phtml';
			}),
		];
	}

}