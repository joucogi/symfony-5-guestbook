<?php

declare(strict_types=1);

namespace App;

use Imagine\Gd\Imagine;
use Imagine\Image\Box;

final class ImageOptimizer {
    private const MAX_WIDTH = 200;
    private const MAX_HEIGHT = 150;

    private $imagine;

    public function __construct() {
        $this->imagine = new Imagine();
    }

    public function resize(string $filename): void {
        [$imageWidth, $imageHeight] = getimagesize($filename);
        $ratio = $imageWidth / $imageHeight;
        $width = self::MAX_WIDTH;
        $height = self::MAX_HEIGHT;
        if ($width / $height > $ratio) {
            $width = $height * $ratio;
        } else {
            $height = $width / $ratio;
        }

        $photo = $this->imagine->open($filename);
        $photo->resize(new Box($width, $height))->save($filename);
    }
}