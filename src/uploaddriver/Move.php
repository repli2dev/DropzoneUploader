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
class Move extends UploadDriver
{
	/** @var array */
	protected $settings = [
		'dir' => null,
	];


	/**
	 * @param array
	 * @return AlesWita\DropzoneUploader\UploadDriver\IUploadDriver
	 */
	public function setSettings(array $settings): AlesWita\DropzoneUploader\UploadDriver\IUploadDriver
	{
		$settings['dir'] = (isset($settings['dir']) ? Nette\Utils\Strings::trim($settings['dir'], '\\/') : null);
		parent::setSettings($settings);
		return $this;
	}


	/**
	 * @return array
	 */
	public function getUploadedFiles(): array
	{
		$uploadedFiles = [];
		$path = ($this->folder === null ? $this->settings['dir'] : $this->settings['dir'] . '/' . $this->folder);

		try {
			foreach (Nette\Utils\Finder::findFiles('*')->from($path) as $file) {
				$uploadedFiles[] = [
					'name' => $file->getBasename(),
					'size' => $file->getSize(),
					'accepted' => true,
				];
			}
		} catch (\UnexpectedValueException $e) {
		}

		return $uploadedFiles;
	}


	/**
	 * @param Nette\Http\FileUpload $file
     * @param AlesWita\DropzoneUploader\ChunkInfo|null $chunkInfo
	 * @return bool
	 */
	public function upload(Nette\Http\FileUpload $file, ?AlesWita\DropzoneUploader\ChunkInfo $chunkInfo = null): bool
	{
		$parent = parent::upload($file);

		if ($parent === true) {
			try {
				$dest = $this->folder === null ? $this->settings['dir'] . '/' . $file->getName() : $this->settings['dir'] . '/' . $this->folder . '/' . $file->getName();
                if ($chunkInfo !== null) {
                    $dest .= '.' . $chunkInfo->getChunkIndex();
                }
				$file->move($dest);
                if ($chunkInfo !== null && $chunkInfo->isLastChunk()) {
                    foreach (range(0, $chunkInfo->getTotalChunkCount() - 1) as $i) {
                        $chunkFile = $dest . '.' . $i;
                        $chunkFileContent = file_get_contents($chunkFile);
                        if ($chunkFileContent === false) {
                            return false;
                        }
                        file_put_contents($dest, $chunkFileContent, FILE_APPEND);
                    }
                    foreach (range(0, $chunkInfo->getTotalChunkCount() - 1) as $i) {
                        // Remove all chunk files
                        $chunkFile = $dest . '.' . $i;
                        if (file_exists($chunkFile) && !unlink($chunkFile)) {
                            return true;
                        }
                    }
                }
				return true;
			} catch (Nette\InvalidStateException $e) {
			}
		}

		return false;
	}


	/**
	 * @param string
	 * @return callable
	 */
	public function download(string $file): callable
	{
		return function ($httpRequest, $httpResponse) use ($file): void {
			$fileResponse = new Nette\Application\Responses\FileResponse(($this->folder === null ? $this->settings['dir'] . '/' . $file : $this->settings['dir'] . '/' . $this->folder . '/' . $file));
			$fileResponse->send($httpRequest, $httpResponse);
		};
	}


	/**
	 * @param string
	 * @return bool
	 */
	public function remove(string $file): bool
	{
		try {
			$path = $this->folder === null ? $this->settings['dir'] . '/' . $file : $this->settings['dir'] . '/' . $this->folder . '/' . $file;
			$dir = dirname($path);
			Nette\Utils\FileSystem::delete($path);

			if (is_dir($dir) && count(scandir($dir)) === 2) {// 2, because '.' and '..'
				Nette\Utils\FileSystem::delete($dir);// remove empty folder
			}
			return true;
		} catch (Nette\IOException $e) {
		}

		return false;
	}
}
