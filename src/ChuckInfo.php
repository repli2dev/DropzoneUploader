<?php

/**
 * This file is part of the AlesWita\DropzoneUploader
 * Copyright (c) 2017 Ales Wita (aleswita+github@gmail.com)
 */

declare(strict_types=1);

namespace AlesWita\DropzoneUploader;

use AlesWita;
use Nette;


class ChunkInfo
{
    /** @var string */
    private $uuid;

    /** @var int */
    private $chunkIndex;

    /** @var int */
    private $totalFileSize;

    /** @var int */
    private $chunkSize;
    /** @var int */
    private $totalChunkCount;

    /** @var int */
    private $chunkByteOffset;

	private function __construct()
    {

    }

    public static function attemptToCreateFromHttpData(array $httpData): ?self
    {
        $dzuuid = $httpData['dzuuid'] ?? null;
        $dzchunkindex = $httpData['dzchunkindex'] ?? null;
        $dztotalfilesize = $httpData['dztotalfilesize'] ?? null;
        $dzchunksize = $httpData['dzchunksize'] ?? null;
        $dztotalchunkcount = $httpData['dztotalchunkcount'] ?? null;
        $dzchunkbyteoffset = $httpData['dzchunkbyteoffset'] ?? null;
        if ($dzuuid === null || $dzchunkindex === null || $dztotalfilesize === null || $dzchunksize === null || $dztotalchunkcount === null || $dzchunkbyteoffset === null) {
            return null;
        }
        $obj = new self;
        $obj->uuid = $dzuuid;
        $obj->chunkIndex = (int) $dzchunkindex;
        $obj->totalFileSize = (int) $dztotalfilesize;
        $obj->chunkSize = (int) $dzchunksize;
        $obj->totalChunkCount = (int) $dztotalchunkcount;
        $obj->chunkByteOffset = (int) $dzchunkbyteoffset;
        return $obj;
    }

    public function getUuid(): string
    {
        return $this->uuid;
    }

    public function getChunkIndex(): int
    {
        return $this->chunkIndex;
    }

    public function getTotalFileSize(): int
    {
        return $this->totalFileSize;
    }

    public function getChunkSize(): int
    {
        return $this->chunkSize;
    }

    public function getTotalChunkCount(): int
    {
        return $this->totalChunkCount;
    }

    public function getChunkByteOffset(): int
    {
        return $this->chunkByteOffset;
    }

    public function getChunkEndByteOffset(): int
    {
        return $this->chunkByteOffset + $this->chunkSize;
    }

    public function isLastChunk(): bool
    {
        return $this->chunkIndex === $this->totalChunkCount - 1;
    }
}
