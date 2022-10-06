<?php

namespace wcf\system\MCSkinPreviewAPI\adapter;

use Imagick;
use wcf\system\MCSkinPreviewAPI\adapter\ISkinRenderer;

/* 
 * ImagickSkinRenderer.class.php - Library to render previews of Minecraft (tm) skins
 * Copyright outadoc (https://github.com/outadoc/MC-SkinPreviewAPI)
 * Modified xXSchrandXx
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */
class ImagickSkinRenderer implements ISkinRenderer
{
    /** @var int the width of the rendered skin (corresponding height will be calculated automatically) */
    private $skin_width;

    /** @var \Imagick */
    private $skinImagick;
    /** @var \Imagick */
    private $renderImagick;

    /**
     * @inheritDoc
     */
    public function __construct($render_width = 85)
    {
        $this->skin_width = $render_width;
        $this->skinImagick = new \Imagick();
        $this->renderImagick = new \Imagick();
    }

    /**
     * @inheritDoc
     */
    public function renderSkinFromPath($skin_path, $skin_type = 'steve', $skin_side = 'front')
    {
        try {
            // Load the skin
            $this->skinImagick->readImage($skin_path);

            return $this->renderSkin($skin_type, $skin_side);
        } catch (\ImagickException $e) {
            if (ENABLE_DEBUG_MODE) {
                \wcf\functions\exception\logThrowable($e);
            }
            return false;
        }
    }

    /**
     * @inheritDoc
     */
    public function renderSkinFromResource($skin, $skin_type = 'steve', $skin_side = 'front')
    {
        try {
            if (\is_string($skin)) {
                $this->skinImagick->readImageBlob($skin);
            }
            return $this->renderSkin($skin_type, $skin_side);
        } catch (\ImagickException $e) {
            if (ENABLE_DEBUG_MODE) {
                \wcf\functions\exception\logThrowable($e);
            }
            return false;
        }
    }

    /**
     * Renders a local Minecraft skin from intern Imagick.
     *
     * @param string $skin_type the skin type; must be 'steve' or 'alex'
     * @param string $skin_side the side of the skin to render; must be 'front', 'back' or 'face'
     *
     * @return \Imagick|false A resource containing the rendered skin.
     *
     * @throws \ImagickException
     */
    private function renderSkin($skin_type = 'steve', $skin_side = 'front')
    {
        if ($skin_side == 'face') {
            // Create the destination image (8*8 transparent png file)
            $this->renderImagick->newImage(8, 8, 'none');
        } else {
            // Create the destination image (16*32 transparent png file)
            $this->renderImagick->newImage(16, 32, 'none');
        }

        // Set the desired arm width (3 or 4 pixels) and check if it's a post-1.8 skin
        $arm_width = ($skin_type === 'alex' ? 3 : 4);
        $is_new_format = $this->isNewSkinFormat($this->skinImagick);

        // Copy all the parts of the skin where they belong to, on a new blank image

        switch ($skin_side) {
            case 'front':
                // Making a preview of the front of the skin

                // Face
                $tmpFace = clone $this->skinImagick;
                $tmpFace->cropImage(8, 8, 8, 8);
                $this->renderImagick->compositeImage($tmpFace, \Imagick::COMPOSITE_OVER, 4, 0);
                $tmpFace->destroy();

                // Chest
                $tmpChest = clone $this->skinImagick;
                $tmpChest->cropImage(8, 12, 20, 20);
                $this->renderImagick->compositeImage($tmpChest, \Imagick::COMPOSITE_OVER, 4, 8);
                $tmpChest->destroy();

                // Right arm
                $tmpRightArm = clone $this->skinImagick;
                $tmpRightArm->cropImage($arm_width, 12, 44, 20);
                $this->renderImagick->compositeImage($tmpRightArm, \Imagick::COMPOSITE_OVER, 4 - $arm_width, 8);
                $tmpRightArm->destroy();

                // Left arm
                if (!$is_new_format || $this->isRectTransparent($this->skinImagick, 36, 52, $arm_width, 12)) {
                    $this->flipRectHorizontal($this->renderImagick, $this->skinImagick, 12, 8, 44, 20, $arm_width, 12);
                } else {
                    $tmpLeftArm = clone $this->skinImagick;
                    $tmpLeftArm->cropImage($arm_width, 12, 36, 52);
                    $this->renderImagick->compositeImage($tmpLeftArm, \Imagick::COMPOSITE_OVER, 12, 8);
                    $tmpLeftArm->destroy();
                }

                // Right leg
                $tmpRightLeg = clone $this->skinImagick;
                $tmpRightLeg->cropImage(4, 12, 4, 20);
                $this->renderImagick->compositeImage($tmpRightLeg, \Imagick::COMPOSITE_OVER, 4, 20);
                $tmpRightArm->destroy();

                // Left leg
                if (!$is_new_format || $this->isRectTransparent($this->skinImagick, 20, 52, 4, 12)) {
                    $this->flipRectHorizontal($this->renderImagick, $this->skinImagick, 8, 20, 4, 20, 4, 12);
                } else {
                    $tmpLeftLeg = clone $this->skinImagick;
                    $tmpLeftLeg->cropImage(4, 12, 20, 52);
                    $this->renderImagick->compositeImage($tmpLeftLeg, \Imagick::COMPOSITE_OVER, 8, 20);
                    $tmpLeftLeg->destroy();
                }

                // Head armor
                $this->overlayArmor($this->skinImagick, $this->renderImagick, 4, 0, 40, 8, 8, 8);

                if ($is_new_format) {
                    // Chest
                    $this->overlayArmor($this->skinImagick, $this->renderImagick, 4, 8, 32, 36, 8, 12);

                    // Right arm
                    $this->overlayArmor($this->skinImagick, $this->renderImagick, 4 - $arm_width, 8, 44, 36, $arm_width, 12);

                    // Left arm
                    $this->overlayArmor($this->skinImagick, $this->renderImagick, 12, 8, 52, 52, $arm_width, 12);

                    // Right leg
                    $this->overlayArmor($this->skinImagick, $this->renderImagick, 4, 20, 4, 36, 4, 12);

                    // Left leg
                    $this->overlayArmor($this->skinImagick, $this->renderImagick, 8, 20, 4, 52, 4, 12);
                }

                // Resize the render: currently, it's a 16*32 file. We usually want it larger.
                return $this->resizeBitmap($this->renderImagick, $this->skin_width, $this->skin_width * 2);
            case 'back':
                // Making a preview of the back of the skin

                // Face
                $tmpFace = clone $this->skinImagick;
                $tmpFace->cropImage(8, 8, 24, 8);
                $this->renderImagick->compositeImage($tmpFace, \Imagick::COMPOSITE_OVER, 4, 0);
                $tmpFace->destroy();

                // Chest
                $tmpChest = clone $this->skinImagick;
                $tmpChest->cropImage(8, 12, 32, 20);
                $this->renderImagick->compositeImage($tmpChest, \Imagick::COMPOSITE_OVER, 4, 8);
                $tmpChest->destroy();

                // Right arm
                $tmpRightArm = clone $this->skinImagick;
                $tmpRightArm->cropImage($arm_width, 12, 48 + $arm_width, 20);
                $this->renderImagick->compositeImage($tmpRightArm, \Imagick::COMPOSITE_OVER, 12, 8);
                $tmpRightArm->destroy();

                // Left arm
                if (!$is_new_format || $this->isRectTransparent($this->skinImagick, 40 + $arm_width, 52, $arm_width, 12)) {
                    $this->flipRectHorizontal($this->renderImagick, $this->skinImagick, 4 - $arm_width, 8, 48 + $arm_width, 20, $arm_width, 12);
                } else {
                    $tmpLeftArm = clone $this->skinImagick;
                    $tmpLeftArm->cropImage($arm_width, 12, 40 + $arm_width, 52);
                    $this->renderImagick->compositeImage($tmpLeftArm, \Imagick::COMPOSITE_OVER, 4 - $arm_width, 8);
                    $tmpLeftArm->destroy();
                }

                // Right leg
                $tmpRightLeg = clone $this->renderImagick;
                $tmpRightArm->cropImage(4, 12, 12, 20);
                $this->renderImagick->compositeImage($tmpRightArm, \Imagick::COMPOSITE_OVER, 8, 20);
                $tmpRightArm->destroy();

                // Left leg
                if (!$is_new_format || $this->isRectTransparent($this->skinImagick, 28, 52, 4, 12)) {
                    $this->flipRectHorizontal($this->renderImagick, $this->skinImagick, 4, 20, 12, 20, 4, 12);
                } else {
                    $tmpLeftLeg = clone $this->skinImagick;
                    $tmpLeftLeg->cropImage(4, 12, 28, 52);
                    $this->renderImagick->compositeImage($tmpLeftLeg, \Imagick::COMPOSITE_OVER, 4, 20);
                    $tmpLeftLeg->destroy();
                }

                // Head armor
                $this->overlayArmor($this->skinImagick, $this->renderImagick, 4, 0, 56, 8, 8, 8);

                if ($is_new_format) {
                    // Chest
                    $this->overlayArmor($this->skinImagick, $this->renderImagick, 4, 8, 32, 36, 8, 12);

                    // Right arm
                    $this->overlayArmor($this->skinImagick, $this->renderImagick, 12, 8, 48 + $arm_width, 36, $arm_width, 12);

                    // Left arm
                    $this->overlayArmor($this->skinImagick, $this->renderImagick, 4 - $arm_width, 8, 56 + $arm_width, 52, $arm_width, 12);

                    // Right leg
                    $this->overlayArmor($this->skinImagick, $this->renderImagick, 8, 20, 12, 36, 4, 12);

                    // Left leg
                    $this->overlayArmor($this->skinImagick, $this->renderImagick, 4, 20, 12, 52, 4, 12);
                }
                // Resize the render: currently, it's a 16*32 file. We usually want it larger.
                return $this->resizeBitmap($this->renderImagick, $this->skin_width, $this->skin_width * 2);
            case 'face':
                // Face
                $tmpFace = clone $this->skinImagick;
                $tmpFace->cropImage(8, 8, 8, 8);
                $this->renderImagick->compositeImage($tmpFace, \Imagick::COMPOSITE_OVER, 0, 0);
                $tmpFace->destroy();

                // Resize the render: currently, it's a 8*8 file. We usually want it larger.
                return $this->resizeBitmap($this->renderImagick, $this->skin_width, $this->skin_width);
        }

        return false;
    }

    /**
     * @inheritDoc
     */
    public function renderSkinBase64($skin_path, $skin_type = 'steve', $skin_side = 'front')
    {
        $data = $this->renderSkinFromPath($skin_path, $skin_type, $skin_side);

        // Write the image to the PHP output buffer
        ob_start();
        if (imagepng($data) === false) {
            $contents = false;
        } else {
            $contents = ob_get_contents();
        }
        ob_end_clean();

        // Encode the contents of the buffer to base 64
        if ($contents == false) {
            return false;
        } else {
            return base64_encode($contents);
        }
    }

    /**
     * @inheritDoc
     * @throws \ImagickException
     */
    public function resizeBitmap(&$bmp, $width, $height)
    {
        // Copy the render to the full-sized image
        $bmp->resizeImage($width, $height, \Imagick::FILTER_UNDEFINED, 100, true);

        return $bmp;
    }

    /**
     * @inheritDoc
     * @throws \ImagickException
     */
    public function flipRectHorizontal(&$dest, $src, $dst_x, $dst_y, $src_x, $src_y, $src_w, $src_h)
    {
        // In Minecraft, some parts of the skins are flipped horizontally, so we have to do that too

        // Copy to a new image, flip and copy back to the original
        $tmp = clone $this->skinImagick;
        $tmp->cropImage($src_w, $src_h, $src_x, $src_y);
        $this->flipHorizontal($tmp);

        $this->renderImagick->compositeImage($tmp, \Imagick::COMPOSITE_OVER, $dst_x, $dst_y);
        $tmp->destroy();

        return true;
    }

    /**
     * @inheritDoc
     * @throws \ImagickException
     */
    public function flipHorizontal(&$bmp)
    {
        $bmp->flopImage();

        return true;
    }

    /**
     * @inheritDoc
     */
    public function overlayArmor(&$armor, &$dest, $dst_x, $dst_y, $x, $y, $w, $h)
    {
        if (!$this->isRectTransparent($armor, $x, $y, $w, $h)) {
            $tmp = clone $armor;
            $tmp->cropImage($w, $h, $x, $y);
            $dest->compositeImage($tmp, \Imagick::COMPOSITE_OVER, $dst_x, $dst_y);
            $tmp->destroy();
        }
        return true;
    }

    /**
     * @inheritDoc
     */
    public function isRectTransparent(&$img, $x, $y, $w, $h)
    {
        $tmp = clone $img;
        $tmp->cropImage($w, $h, $x, $y);
        if ($tmp->getImageAlphaChannel()) {
            $tmp->destroy();
            return true;
        }
        $tmp->destroy();
        // Check for a 8*8 square of pixels starting at ($x;$y)
        for ($i = $x; $i < $x + $w; $i++) {
            for ($j = $y; $j < $y + $h; $j++) {
                // If this pixel isn't the same color as the first one, then return false
                if (
                    $img->getImagePixelColor($i, $j)->getColor() == 255
                ) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * @inheritDoc
     */
    public function isNewSkinFormat(&$skin)
    {
        return ($skin->getImageHeight() == $skin->getImageWidth() && $skin->getImageWidth() == 64);
    }

    /**
     * @inheritDoc
     */
    public function setSkinWidth($width)
    {
        $this->skin_width = $width;
    }

    /**
     * @inheritDoc
     */
    public function writeImage($img, $file_path)
    {
        $img->writeImage($file_path);
    }
}
