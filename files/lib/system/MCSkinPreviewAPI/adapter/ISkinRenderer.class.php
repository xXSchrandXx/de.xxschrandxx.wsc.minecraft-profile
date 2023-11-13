<?php

namespace wcf\system\MCSkinPreviewAPI\adapter;

/*
 * ISkinRenderer.class.php - Library to render previews of Minecraft (tm) skins
 * Copyright outadoc (https://github.com/outadoc/MC-SkinPreviewAPI)
 * Modified xXSchrandXx (https://github.com/xXSchrandXx/MC-SkinPreviewAPI)
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

interface ISkinRenderer
{
    /**
     * Creates a new skin renderer. It can then be used to render as many skins as you like.
     *
     * @param int $render_width the width of the rendered skin (corresponding height will be calculated automatically)
     */
    public function __construct($render_width = 85);

    /**
     * Renders a local Minecraft skin using its path.
     *
     * @param string $skin_path the path to the skin that is to be rendered
     * @param string $skin_type the skin type; must be 'steve' or 'alex'
     * @param string $skin_side the side of the skin to render; must be 'front', 'back' or 'face'
     *
     * @return \GdImage|\Imagick|resource|false A resource containing the rendered skin.
     */
    public function renderSkinFromPath($skin_path, $skin_type = 'steve', $skin_side = 'front');

    /**
     * Renders a local Minecraft skin from its bitmap.
     *
     * @param resource|\GdImage|string $skin a resource containing the actual skin to render
     * @param string $skin_type the skin type; must be 'steve' or 'alex'
     * @param string $skin_side the side of the skin to render; must be 'front', 'back' or 'face'
     *
     * @return \GdImage|\Imagick|resource|false A resource containing the rendered skin.
     */
    public function renderSkinFromResource($skin, $skin_type = 'steve', $skin_side = 'front');

    /**
     * Renders a Minecraft skin as a base 64 string.
     *
     * @param string $skin_path the path to the skin that is to be rendered
     * @param string $skin_type the skin type; must be 'steve' or 'alex'
     * @param string $skin_side the side of the skin to render; must be 'front', 'back' or 'face'
     *
     * @return string|false the rendered skin, encoded as a PNG base64 string or false on error.
     */
    public function renderSkinBase64($skin_path, $skin_type = 'steve', $skin_side = 'front');

    /**
     * Resizes a bitmap to the specified width. The height will be calculated automatically.
     *
     * @param \GdImage|\Imagick|resource $bmp the image to be resized
     * @param int $width the width of the final bitmap
     * @param int $width the height of the final bitmap
     *
     * @return \GdImage|\Imagick|resource|false the resized image
     */
    public function resizeBitmap(&$bmp, $width, $height);

    /**
     * Flips a part of a bitmap horizontally and draws it onto another bitmap.
     * Behaves like imagecopy.
     *
     * @param \GdImage|\Imagick|resource $dest the bitmap we will be drawing onto
     * @param \GdImage|\Imagick|resource $src the bitmap that contains the pixels to flip
     * @param int $dst_x x-coordinate of destination point
     * @param int $dst_y y-coordinate of destination point
     * @param int $src_x x-coordinate of source point
     * @param int $src_y y-coordinate of source point
     * @param int $src_w source width
     * @param int $src_h source height
     *
     * @return bool false on error.
     */
    public function flipRectHorizontal(&$dest, $src, $dst_x, $dst_y, $src_x, $src_y, $src_w, $src_h);

    /**
     * Flips all the pixels of a bitmap horizontally.
     *
     * @param \GdImage|\Imagick|resource $bmp the bitmap to flip
     *
     * @return bool false on error.
     */
    public function flipHorizontal(&$bmp);

    /**
     * Overlays an armor part onto a destination.
     *
     * @param \GdImage|\Imagick|resource $armor the bitmap containing an armor part
     * @param \GdImage|\Imagick|resource $dest the bitmap to draw the armor on to
     * @param int $dst_x x-coordinate of destination point
     * @param int $dst_y y-coordinate of destination point
     * @param int $x x-coordinate of source point
     * @param int $y y-coordinate of source point
     * @param int $w source width
     * @param int $h source height
     *
     * @return bool false on error.
     */
    public function overlayArmor(&$armor, &$dest, $dst_x, $dst_y, $x, $y, $w, $h);

    /**
     * Checks if all the pixels of a determined area are either transparent or black.
     *
     * @param \GdImage|\Imagick|resource $img the bitmap containing the pixels to check
     * @param int $x x-coordinate of source point
     * @param int $y y-coordinate of source point
     * @param int $w source width
     * @param int $h source height
     *
     * @return bool true if the rectangle is completely black or transparent, false if it's not
     */
    public function isRectTransparent(&$img, $x, $y, $w, $h);

    /**
     * Checks if a skin is of the post-1.8 format.
     *
     * @param \GdImage|\Imagick|resource $skin the skin to check
     *
     * @return bool true if the skin is in post-1.8 format, else false
     */
    public function isNewSkinFormat(&$skin);

    /**
     * Changes the width of the skins to be rendered.
     *
     * @param int $width the width of the skin preview
     */
    public function setSkinWidth($width);

    /**
     * Writes the image to the given path.
     *
     * @param \GdImage|\Imagick|resource $img the bitmap to save.
     * @param string $file_path the path of the file.
     *
     * @return bool weather save was successful
     */
    public function writeImage($img, $file_path);
}
