<?php
declare(strict_types=1);

namespace ImageTraits\Controller;
use Models\Entities\Admin;
use Models\Utilities\ImageUrl;
use Gumlet\ImageResize;

use Zf\Ext\Utilities\Image\{Image,FastImage};

trait UploadImages
{
    const ALLOW_EXT = [
        'jpg', 'png', 'jpeg'
    ];
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
        $imgName = $file['name'];
        
        if ( empty($imgType) || !in_array($imgType, self::ALLOW_EXT)) {
            return [
                'success' => false,
                'msg' => 'Hình ảnh không đúng định dạng.'
            ];
        }

        if (!$this->checkSizeImage($imgSize, $sizes)) {
            return [
                'success' => false,
                'msg' => 'Hình ảnh có kích thước nhỏ hơn yêu cầu.'
            ];
        }

        // Get folder to save image
        $imgDir = ImageUrl::getPathImageUpload($folderName);

        // Create dir of image if not exist
        if (!is_dir($imgDir)) {
            @mkdir($imgDir, 0755, true);
            @chmod($imgDir, 0755);
        }

        // Make file name
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

        $fileName = crc32(implode([
                $imgName, 
                $imgSize[0], $imgSize[1],
            ])
        ). '.' .$ext;

        $imgNameSrc = Image::getInstanceFilename($imgDir . "/{$fileName}", null, null, false);
        $name = explode('/', $imgNameSrc);

        $path['0'] = $imgDir;
        if (!empty($sizes)) {
            foreach ($sizes as $device => $size) {
                $width = explode('x', $size)[0] 
                    ? (explode('x', $size)[0] == 'auto' ? 0 : (int) explode('x', $size)[0]) 
                    : 0;
                $height = explode('x', $size)[1] 
                    ? (explode('x', $size)[1] == 'auto' ? 0 : (int) explode('x', $size)[1]) 
                    : 0;
                $imgDirByDevice = $imgDir . "/{$size}";

                // Create dir of image if not exist
                if (!is_dir($imgDirByDevice)) {
                    @mkdir($imgDirByDevice, 0755, true);
                    @chmod($imgDirByDevice, 0755);
                }

                $imgNameSrcByDevice = Image::getInstanceFilename($imgDirByDevice . "/{$fileName}", null, null, false);
                if (!file_exists($imgNameSrcByDevice)) {
                    $image = new ImageResize($file['tmp_name']);
                    if ($width == 0) {
                        $image->resizeToHeight($height);
                    } else if ($height == 0) {
                        $image->resizeToWidth($width);
                    } else {
                        $image->crop($width, $height, true, ImageResize::CROPCENTER);
                    }
                    $image->save($imgNameSrcByDevice);
                }
                
                $path[$device] = $imgDirByDevice;
            }
        } 
        // Create 1 image with origin size
        if (!file_exists($imgNameSrc)) {
            @move_uploaded_file($file['tmp_name'], $imgNameSrc)
            || @copy($file['tmp_name'], $imgNameSrc);
        }

        unset($file);
        return [
            'success' => true,
            'name' => array_pop($name), 
            'path' => $path
        ];
    }

    /**
     * Revert upload image when uploaded error
     *
     * @param array|string $fileNames
     * @param string $folderName
     * @param array $sizes
     * @return void
     */
    protected function revertUploadImageDropzone(array|string $fileNames, string $folderName, array $sizes = []): void
    {
        if (!empty($fileNames)) {
            if (!is_array($fileNames)) $fileNames = [$fileNames];
            $imgDir = ImageUrl::getPathImageUpload($folderName);

            foreach ($fileNames as $fileName) {
                if (empty($size)) {
                    foreach (glob($imgDir . '/*', GLOB_ONLYDIR) as $imgDirByDevice) {
                        if (file_exists($pathFile = $imgDirByDevice . "/{$fileName}")) {
                            @unlink($pathFile);
                        }
                    }
                } else {
                    foreach ($sizes as $size) {
                        $imgDirByDevice = $imgDir . "/{$size}";
                        if (file_exists($pathFile = $imgDirByDevice . "/{$fileName}")) {
                            @unlink($pathFile);
                        }
                    }
                }

                if (file_exists($pathRoot = $imgDir . "/{$fileName}"))
                    @unlink($pathRoot);
            }
        }
    }
    /**
     * Check size of image
     *
     * @param array $imgSize
     * @param array $sizes
     * @return boolean
     */
    public function checkSizeImage(array $imgSize, array $sizes): bool
    {
        if (empty($imgSize[0]) && empty($imgSize[1])) return false;

        foreach ($sizes as $size) {
            $validWidth = explode('x', $size)[0];
            $validHeight = explode('x', $size)[1];

            if ($imgSize[0] < $validWidth && $imgSize[1] < $validHeight)
                return false;
        }
        return true;
    }
}