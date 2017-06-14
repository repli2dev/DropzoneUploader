<?php

/**
 * This file is part of the AlesWita\Components\DropzoneUploader
 * Copyright (c) 2017 Ales Wita (aleswita+github@gmail.com)
 */

declare(strict_types=1);

namespace AlesWita\Components\DropzoneUploader\Tests\App\Service;

use Nette;


/**
 * @author Ales Wita
 * @license MIT
 */
final class FakeTranslator implements Nette\Localization\ITranslator
{
	/**
	 * @param string
	 * @param int
	 * @return string
	 */
	public function translate($message, $count = NULL): string {
		return $message;
	}
}