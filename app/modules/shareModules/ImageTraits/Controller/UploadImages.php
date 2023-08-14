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

    /**
     * Upload image using dropzone
     *
     * @param array $file
     * @param string $folderName
     * @param array $sizes format: ['device' => 'width x height'], ex: ['1' => '200x400']
     * @return array|boolean
     */
    protected function uploadImageDropzone(array $file, string $folderName = 'other', array $sizes = []): array|bool
    {
        $imgInfo = new FastImage($file['tmp_name']);
        $imgSize = $imgInfo->getSize();
        $imgType = $imgInfo->getType();
        $imgInfo->close();
        
        if ( empty($imgType) ) return false;

        // Get folder to save image
        $imgDir = implode('/', [
            ROOT_UPLOAD_PATH, $folderName
        ]);

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

        $path['0'] = $imgDir;
        if (!empty($sizes)) {
            foreach ($sizes as $device => $size) {
                $width = (int) explode('x', $size)[0] ?? 200;
                $height = (int) explode('x', $size)[1] ?? 100;
                $imgDirByDevice = $imgDir . "/{$size}";

                // Create dir of image if not exist
                if (!is_dir($imgDirByDevice)) {
                    @mkdir($imgDirByDevice, 0755, true);
                    @chmod($imgDirByDevice, 0755);
                }

                $imgNameSrcByDevice = Image::getInstanceFilename($imgDirByDevice . "/{$fileName}");

                if ($imgSize[0] > $width || $imgSize[1] > $height) {
                    $image = new ImageResize($file['tmp_name']);
                    $image->crop($width, $height, true, ImageResize::CROPCENTER);
                    $image->save($imgNameSrcByDevice);
                }
                $path[$device] = $imgDirByDevice;
            }
        }

        // Upload origin image
        @move_uploaded_file($file['tmp_name'], $imgNameSrc)
        || @copy($file['tmp_name'], $imgNameSrc);

        unset($file);
        return [
            'name' => array_pop($name), 
            'path' => $path
        ];
    }

    protected function revertUploadImageDropzone(string $fileName, string $folderName, array $sizes): void
    {
        if (!empty($fileName)) {
            $imgDir = implode('/', [
                ROOT_UPLOAD_PATH, $folderName
            ]);

            foreach ($sizes as $size) {
                $imgDirByDevice = $imgDir . "/{$size}";
                @unlink($imgDirByDevice . "/{$fileName}");
            }

            @unlink($imgDir . "/{$fileName}");
        }
    }
}