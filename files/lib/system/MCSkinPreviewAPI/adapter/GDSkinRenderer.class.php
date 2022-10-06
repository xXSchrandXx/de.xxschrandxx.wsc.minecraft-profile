<?php

namespace wcf\system\MCSkinPreviewAPI\adapter;

/*
 * GDSkinRenderer.class.php - Library to render previews of Minecraft (tm) skins
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

class GDSkinRenderer implements ISkinRenderer
{

    /** @var int the width of the rendered skin (corresponding height will be calculated automatically) */
    private $skin_width;

    /**
     * @inheritDoc
     */
    public function __construct($render_width = 85)
    {
        $this->skin_width = $render_width;
    }

    /**
     * @inheritDoc
     */
    public function renderSkinFromPath($skin_path, $skin_type = 'steve', $skin_side = 'front')
    {
        // Load the skin
        $skin = imagecreatefrompng($skin_path);

        // If for some reason we couldn't download the file, use a steve skin instead
        if ($skin === false) {
            return false;
        }

        return $this->renderSkin($skin, $skin_type, $skin_side);
    }

    /**
     * @inheritDoc
     */
    public function renderSkinFromResource($skin, $skin_type = 'steve', $skin_side = 'front')
    {
        if (\is_string($skin)) {
            $skin = imagecreatefromstring($skin);
        }

        // If for some reason we couldn't download the file, use a steve skin instead
        if ($skin === false) {
            return false;
        }

        return $this->renderSkin($skin, $skin_type, $skin_side);
    }

    /**
     * Renders a local Minecraft skin from intern resource.
     *
     * @param \GdImage|resource $skin a resource containing the actual skin to render
     * @param string $skin_type the skin type; must be 'steve' or 'alex'
     * @param string $skin_side the side of the skin to render; must be 'front', 'back' or 'face'
     *
     * @return \GdImage|false A resource containing the rendered skin.
     */
    private function renderSkin($skin, $skin_type = 'steve', $skin_side = 'front')
    {
        if ($skin_side == 'face') {
            // Create the destination image (8*8 transparent png file)
            $preview = imagecreatetruecolor(8, 8);
        } else {
            // Create the destination image (16*32 transparent png file)
            $preview = imagecreatetruecolor(16, 32);
        }

        if ($preview === false) {
            return false;
        }

        // Set the desired arm width (3 or 4 pixels) and check if it's a post-1.8 skin
        $arm_width = ($skin_type === 'alex' ? 3 : 4);
        $is_new_format = $this->isNewSkinFormat($skin);

        // Let's have a transparent background!
        $transparent = imagecolorallocatealpha($preview, 255, 255, 255, 127);
        if ($transparent === false) {
            return false;
        }
        if (imagefill($preview, 0, 0, $transparent) === false) {
            return false;
        }

        // Copy all the parts of the skin where they belong to, on a new blank image

        switch ($skin_side) {
            case 'front':
                // Making a preview of the front of the skin

                // Face
                if (imagecopy($preview, $skin, 4, 0, 8, 8, 8, 8) === false) {
                    return false;
                }

                // Chest
                if (imagecopy($preview, $skin, 4, 8, 20, 20, 8, 12) === false) {
                    return false;
                }

                // Right arm
                if (imagecopy($preview, $skin, 4 - $arm_width, 8, 44, 20, $arm_width, 12) === false) {
                    return false;
                }

                // Left arm
                if (!$is_new_format || $this->isRectTransparent($skin, 36, 52, $arm_width, 12)) {
                    if ($this->flipRectHorizontal($preview, $skin, 12, 8, 44, 20, $arm_width, 12) === false) {
                        return false;
                    }
                } else {
                    if (imagecopy($preview, $skin, 12, 8, 36, 52, $arm_width, 12) === false) {
                        return false;
                    }
                }

                // Right leg
                if (imagecopy($preview, $skin, 4, 20, 4, 20, 4, 12) === false) {
                    return false;
                }

                // Left leg
                if (!$is_new_format || $this->isRectTransparent($skin, 20, 52, 4, 12)) {
                    if ($this->flipRectHorizontal($preview, $skin, 8, 20, 4, 20, 4, 12) === false) {
                        return false;
                    }
                } else {
                    if (imagecopy($preview, $skin, 8, 20, 20, 52, 4, 12) === false) {
                        return false;
                    }
                }

                // Head armor
                if ($this->overlayArmor($skin, $preview, 4, 0, 40, 8, 8, 8) === false) {
                    return false;
                }

                if ($is_new_format) {
                    // Chest
                    if ($this->overlayArmor($skin, $preview, 4, 8, 32, 36, 8, 12) === false) {
                        return false;
                    }

                    // Right arm
                    if ($this->overlayArmor($skin, $preview, 4 - $arm_width, 8, 44, 36, $arm_width, 12) === false) {
                        return false;
                    }

                    // Left arm
                    if ($this->overlayArmor($skin, $preview, 12, 8, 52, 52, $arm_width, 12) === false) {
                        return false;
                    }

                    // Right leg
                    if ($this->overlayArmor($skin, $preview, 4, 20, 4, 36, 4, 12) === false) {
                        return false;
                    }

                    // Left leg
                    if ($this->overlayArmor($skin, $preview, 8, 20, 4, 52, 4, 12) === false) {
                        return false;
                    }
                }

                // Resize the render: currently, it's a 16*32 file. We usually want it larger.
                return $this->resizeBitmap($preview, $this->skin_width, $this->skin_width * 2);
            case 'back':
                // Making a preview of the back of the skin

                // Face
                if (imagecopy($preview, $skin, 4, 0, 24, 8, 8, 8) === false) {
                    return false;
                }

                // Chest
                if (imagecopy($preview, $skin, 4, 8, 32, 20, 8, 12) === false) {
                    return false;
                }

                // Right arm
                if (imagecopy($preview, $skin, 12, 8, 48 + $arm_width, 20, $arm_width, 12) === false) {
                    return false;
                }

                // Left arm
                if (!$is_new_format || $this->isRectTransparent($skin, 40 + $arm_width, 52, $arm_width, 12)) {
                    if ($this->flipRectHorizontal($preview, $skin, 4 - $arm_width, 8, 48 + $arm_width, 20, $arm_width, 12) === false) {
                        return false;
                    }
                } else {
                    if (imagecopy($preview, $skin, 4 - $arm_width, 8, 40 + $arm_width, 52, $arm_width, 12) === false) {
                        return false;
                    }
                }

                // Right leg
                if (imagecopy($preview, $skin, 8, 20, 12, 20, 4, 12) === false) {
                    return false;
                }

                // Left leg
                if (!$is_new_format || $this->isRectTransparent($skin, 28, 52, 4, 12)) {
                    if ($this->flipRectHorizontal($preview, $skin, 4, 20, 12, 20, 4, 12) === false) {
                        return false;
                    }
                } else {
                    if (imagecopy($preview, $skin, 4, 20, 28, 52, 4, 12) === false) {
                        return false;
                    }
                }

                // Head armor
                if ($this->overlayArmor($skin, $preview, 4, 0, 56, 8, 8, 8) === false) {
                    return false;
                }

                if ($is_new_format) {
                    // Chest
                    if ($this->overlayArmor($skin, $preview, 4, 8, 32, 36, 8, 12) === false) {
                        return false;
                    }

                    // Right arm
                    if ($this->overlayArmor($skin, $preview, 12, 8, 48 + $arm_width, 36, $arm_width, 12) === false) {
                        return false;
                    }

                    // Left arm
                    if ($this->overlayArmor($skin, $preview, 4 - $arm_width, 8, 56 + $arm_width, 52, $arm_width, 12) === false) {
                        return false;
                    }

                    // Right leg
                    if ($this->overlayArmor($skin, $preview, 8, 20, 12, 36, 4, 12) === false) {
                        return false;
                    }

                    // Left leg
                    if ($this->overlayArmor($skin, $preview, 4, 20, 12, 52, 4, 12) === false) {
                        return false;
                    }
                }
                // Resize the render: currently, it's a 16*32 file. We usually want it larger.
                return $this->resizeBitmap($preview, $this->skin_width, $this->skin_width * 2);
            case 'face':
                // Face
                if (imagecopy($preview, $skin, 0, 0, 8, 8, 8, 8) === false) {
                    return false;
                }

                // Resize the render: currently, it's a 8*8 file. We usually want it larger.
                return $this->resizeBitmap($preview, $this->skin_width, $this->skin_width);
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
        return base64_encode($contents);
    }

    /**
     * @inheritDoc
     */
    public function resizeBitmap(&$bmp, $width, $height)
    {
        $fullsize = imagecreatetruecolor($width, $height);

        if ($fullsize === false) {
            return false;
        }
        
        if (imagesavealpha($fullsize, true) === false) {
            return false;
        }

        // Fill the render with a transparent background
        $transparent = imagecolorallocatealpha($fullsize, 255, 255, 255, 127);

        if ($transparent === false) {
            return false;
        }

        if (imagefill($fullsize, 0, 0, $transparent) === false) {
            return false;
        }

        // Copy the render to the full-sized image
        if (imagecopyresized($fullsize, $bmp, 0, 0, 0, 0, imagesx($fullsize), imagesy($fullsize), imagesx($bmp), imagesy($bmp)) === false) {
            return false;
        }

        return $fullsize;
    }

    /**
     * @inheritDoc
     */
    public function flipRectHorizontal(&$dest, $src, $dst_x, $dst_y, $src_x, $src_y, $src_w, $src_h)
    {
        // In Minecraft, some parts of the skins are flipped horizontally, so we have to do that too
        // Uses the same parameters as imagecopy

        $tmp = imagecreatetruecolor($src_w, $src_h);

        if ($tmp === false) {
            return false;
        }

        // Sets a transparent background
        if (imagesavealpha($tmp, true) === false) {
            return false;
        }

        $transparent = imagecolorallocatealpha($tmp, 255, 255, 255, 127);
        if ($transparent === false) {
            return false;
        }

        if (imagefill($tmp, 0, 0, $transparent) === false) {
            return false;
        }

        // Copy to a new image, flip and copy back to the original
        if (imagecopy($tmp, $src, 0, 0, $src_x, $src_y, $src_w, $src_h) === false) {
            return false;
        }
        $this->flipHorizontal($tmp);
        if (imagecopy($dest, $tmp, $dst_x, $dst_y, 0, 0, $src_w, $src_h) === false) {
            return false;
        }

        if (imagedestroy($tmp) === false) {
            return false;
        }

        return true;
    }

    /**
     * @inheritDoc
     */
    public function flipHorizontal(&$bmp)
    {
        $size_x = imagesx($bmp);
        if ($size_x === false) {
            return false;
        }

        $size_y = imagesy($bmp);
        if ($size_y === false) {
            return false;
        }

        $tmp = imagecreatetruecolor($size_x, $size_y);
        if ($tmp === false) {
            return false;
        }

        // Sets a transparent background
        if (imagesavealpha($tmp, true) === false) {
            return false;
        }
        $transparent = imagecolorallocatealpha($tmp, 255, 255, 255, 127);
        if ($transparent === false) {
            return false;
        }
        if (imagefill($tmp, 0, 0, $transparent) === false) {
            return false;
        }

        $x = imagecopyresampled($tmp, $bmp, 0, 0, ($size_x - 1), 0, $size_x, $size_y, 0 - $size_x, $size_y);

        if ($x) {
            $bmp = $tmp;
            return true;
        } else {
            return false;
        }
    }

    /**
     * @inheritDoc
     */
    public function overlayArmor(&$armor, &$dest, $dst_x, $dst_y, $x, $y, $w, $h)
    {
        if (!$this->isRectTransparent($armor, $x, $y, $w, $h)) {
            if (imagecopy($dest, $armor, $dst_x, $dst_y, $x, $y, $w, $h) === false) {
                return false;
            }
        }
        return true;
    }

    /**
     * @inheritDoc
     */
    public function isRectTransparent(&$img, $x, $y, $w, $h)
    {
        $transparent = imagecolorallocatealpha($img, 255, 255, 255, 127);
        if ($transparent === false) {
            return false;
        }
        $black = imagecolorallocatealpha($img, 255, 255, 255, 0);
        if ($black === false) {
            return false;
        }

        // Check for a 8*8 square of pixels starting at ($x;$y)
        for ($i = $x; $i < $x + $w; $i++) {
            for ($j = $y; $j < $y + $h; $j++) {

                // If this pixel isn't the same color as the first one, then return false
                if (
                    imagecolorat($img, $i, $j) != $transparent
                    && imagecolorat($img, $i, $j) != $black
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
        return (imagesy($skin) == imagesx($skin) && imagesx($skin) == 64);
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
        return \imagepng($img, $file_path);
    }
}
