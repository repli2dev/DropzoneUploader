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
interface IUploadDriver
{
	/**
	 * @param array
	 * @return AlesWita\DropzoneUploader\UploadDriver\IUploadDriver
	 */
	function setSettings(array $settings): AlesWita\DropzoneUploader\UploadDriver\IUploadDriver;

	/**
	 * @param string|null
	 * @return AlesWita\DropzoneUploader\UploadDriver\IUploadDriver
	 */
	function setFolder(?string $folder): AlesWita\DropzoneUploader\UploadDriver\IUploadDriver;

	/**
	 * @return array
	 */
	function getSettings(): array;

	/**
	 * @return string|null
	 */
	function getFolder(): ?string;

	/**
	 * @return array
	 */
	function getUploadedFiles(): array;

	/**
	 * @param Nette\Http\FileUpload $file
     * @param AlesWita\DropzoneUploader\ChunkInfo|null $chunkInfo
	 * @return bool
	 */
	function upload(Nette\Http\FileUpload $file, ?AlesWita\DropzoneUploader\ChunkInfo $chunkInfo = null): bool;

	/**
	 * @param string
	 * @return callable
	 */
	function download(string $file): callable;

	/**
	 * @param string
	 * @return bool
	 */
	function remove(string $file): bool;
}
