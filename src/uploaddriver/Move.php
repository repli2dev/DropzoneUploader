<?php

/**
 * This file is part of the AlesWita\DropzoneUploader
 * Copyright (c) 2017 Ales Wita (aleswita+github@gmail.com)
 */

declare(strict_types=1);

namespace AlesWita\DropzoneUploader\UploadDriver;

use AlesWita;
use Nette;


/**
 * @author Ales Wita
 * @license MIT
 */
final class Move extends UploadDriver
{
	/** @var array */
	protected $settings = [
		"dir" => NULL,
	];

	/**
	 * @param array
	 * @return AlesWita\DropzoneUploader\UploadDriver\IUploadDriver
	 */
	public function setSettings(array $settings): AlesWita\DropzoneUploader\UploadDriver\IUploadDriver {
		$settings["dir"] = (isset($settings["dir"]) ? Nette\Utils\Strings::trim($settings["dir"], "\\/") : NULL);
		parent::setSettings($settings);
		return $this;
	}

	/**
	 * @return array
	 */
	public function getUploadedFiles(): array {
		$uploadedFiles = [];
		$path = ($this->folder === NULL ? $this->settings["dir"] : "{$this->settings["dir"]}/{$this->folder}");

		foreach (Nette\Utils\Finder::findFiles("*")->from($path) as $file) {
			$uploadedFiles[] = [
				"name" => $file->getBasename(),
				"size" => $file->getSize(),
				"accepted" => TRUE,
			];
		}

		return $uploadedFiles;
	}

	/**
	 * @param Nette\Http\FileUpload
	 * @return bool
	 */
	public function upload(Nette\Http\FileUpload $file): bool {
		$parent = parent::upload($file);

		if ($parent === TRUE) {
			try {
				$dest = ($this->folder === NULL ? "{$this->settings["dir"]}/{$file->getName()}" : "{$this->settings["dir"]}/{$this->folder}/{$file->getName()}");
				$file->move($dest);
				return TRUE;
			} catch (Nette\InvalidStateException $e) {
			}
		}

		return FALSE;
	}

	/**
	 * @todo not implemented
	 * @param string
	 * @return callable
	 */
	public function download(string $file): callable {
		return function ($httpRequest, $httpResponse) use ($file): void {
			// todo
		};
	}

	/**
	 * @param string
	 * @return bool
	 */
	public function remove(string $file): bool {
		try {
			$path = ($this->folder === NULL ? "{$this->settings["dir"]}/{$file}" : "{$this->settings["dir"]}/{$this->folder}/{$file}");
			Nette\Utils\FileSystem::delete($path);
			return TRUE;
		} catch (Nette\IOException $e) {
		}

		return FALSE;
	}
}
