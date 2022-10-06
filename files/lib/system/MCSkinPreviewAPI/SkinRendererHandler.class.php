<?php

namespace wcf\system\MCSkinPreviewAPI;

use wcf\system\MCSkinPreviewAPI\adapter\GDSkinRenderer;
use wcf\system\MCSkinPreviewAPI\adapter\ImagickSkinRenderer;
use wcf\system\MCSkinPreviewAPI\adapter\ISkinRenderer;

/* 
 * SkinRendererHandler.class.php - Library to render previews of Minecraft (tm) skins
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

class SkinRendererHandler implements ISkinRenderer
{

    /** @var ISkinRenderer */
    private $adapter;

    /**
     * @inheritDoc
     */
    public function __construct($render_width = 85)
    {
        if (\IMAGE_ADAPTER_TYPE !== 'gd') {
            $this->adapter = new ImagickSkinRenderer($render_width);
        } else {
            $this->adapter = new GDSkinRenderer($render_width);
        }
    }

    /**
     * @inheritDoc
     */
    public function renderSkinFromPath($skin_path, $skin_type = 'steve', $skin_side = 'front')
    {
        return $this->adapter->renderSkinFromResource($skin_path, $skin_type, $skin_side);
    }

    /**
     * @inheritDoc
     */
    public function renderSkinFromResource($skin, $skin_type = 'steve', $skin_side = 'front')
    {
        return $this->adapter->renderSkinFromResource($skin, $skin_type, $skin_side);
    }

    /**
     * @inheritDoc
     */
    public function renderSkinBase64($skin_path, $skin_type = 'steve', $skin_side = 'front')
    {
        return $this->adapter->renderSkinBase64($skin_path, $skin_type, $skin_side);
    }

    /**
     * @inheritDoc
     */
    public function resizeBitmap(&$bmp, $width, $height)
    {
        return $this->adapter->resizeBitmap($bmp, $width, $height);
    }

    /**
     * @inheritDoc
     */
    public function flipRectHorizontal(&$dest, $src, $dst_x, $dst_y, $src_x, $src_y, $src_w, $src_h)
    {
        $this->adapter->flipRectHorizontal($dest, $src, $dst_x, $dst_y, $src_x, $src_y, $src_w, $src_h);
    }

    /**
     * @inheritDoc
     */
    public function flipHorizontal(&$bmp)
    {
        $this->adapter->flipHorizontal($bmp);
    }

    /**
     * @inheritDoc
     */
    public function overlayArmor(&$armor, &$dest, $dst_x, $dst_y, $x, $y, $w, $h)
    {
        $this->adapter->overlayArmor($armor, $dest, $dst_x, $dst_y, $x, $y, $w, $h);
    }

    /**
     * @inheritDoc
     */
    public function isRectTransparent(&$img, $x, $y, $w, $h)
    {
        return $this->adapter->isRectTransparent($img, $x, $y, $w, $h);
    }

    /**
     * @inheritDoc
     */
    public function isNewSkinFormat(&$skin)
    {
        return $this->adapter->isNewSkinFormat($skin);
    }

    /**
     * @inheritDoc
     */
    public function setSkinWidth($width)
    {
        $this->adapter->setSkinWidth($width);
    }

    /**
     * @inheritDoc
     */
    public function writeImage($img, $file_path)
    {
        return $this->adapter->writeImage($img, $file_path);
    }
}