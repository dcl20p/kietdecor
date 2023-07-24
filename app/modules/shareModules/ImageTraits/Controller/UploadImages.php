<?php
declare(strict_types=1);

namespace ImageTraits\Controller;
use Models\Entities\Admin;
use Gumlet\ImageResize;

use Zf\Ext\Utilities\Image\{Image,FastImage};

trait UploadImages
{
    /**
     * Upload image of user: avatar / background time line
     *
     * @param array $file
     * @param string $userId
     * @param integer $maxWidth
     * @param integer $maxHeight
     * @return array|boolean
     */
    protected function uploadImage(array $file, string $userId, int $maxWidth = 150, int $maxHeight = 150): mixed
    {
        // Get information of image
        $imgInfo = new FastImage($file['tmp_name']);
        $imgSize = $imgInfo->getSize();
        $imgType = $imgInfo->getType();
        $imgInfo->close();
        
        if ( empty($imgType) ) return false;

        // Get folder to save image
        $userDir = Admin::getUploadFolder($userId);
        $imgDir = implode('/', [
            $userDir, Admin::IMG_FOLDER
        ]);

        // Create dir of user if not exist
        if (!is_dir($userDir)) {
            @mkdir($userDir, 0755, true);
            @chmod($userDir, 0755);
        }

        // Create dir of image if not exist
        if (!is_dir($imgDir)) {
            @mkdir($imgDir, 0755, true);
            @chmod($imgDir, 0755);
        }

        // Make file name
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $fileName = $this->getZfHelper()->getRandomCode([
            'maxLen' => 16, 'id' => time()
        ]) . '.' .$ext;

        $imgNameSrc = Image::getInstanceFilename($imgDir . "/{$fileName}");
        $name = explode('/', $imgNameSrc);

        // Resize to 100x100
        if ($imgSize[0] > $maxWidth || $imgSize[1] > $maxHeight) {
            $image = new ImageResize($file['tmp_name']);
            // $image->resizeToBestFit($maxWidth, $maxHeight);
            $image->crop($maxWidth, $maxHeight, true, ImageResize::CROPCENTER);
            $image->save($imgNameSrc);
        } else {
            @move_uploaded_file($file['tmp_name'], $imgNameSrc)
            || @copy($file['tmp_name'], $imgNameSrc);
        }
        unset($file);
        return ['name' => array_pop($name), 'path' => $imgDir];
    }
}