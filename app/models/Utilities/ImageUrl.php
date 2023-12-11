<?php
namespace Models\Utilities;

class ImageUrl
{
    /**
     * Generate image
     *
     * @param string $fileName
     * @param array $sizes
     * @param string $title
     * @param string $folderName
     * @param bool $useFitSize
     * @return string
     */
    public static function generateImage(string $fileName, array $sizes = [], string $title = '', string $folderName = '', bool $useFitSize = false, string $class = '', $onlySrc = false): string 
    {
        $folderBySize = '';
        $strSize = 'width="700" height="450"';
        if (isset($sizes[DEVICE_ENV])) {
            $folderBySize = $sizes[DEVICE_ENV];
            $sizeByDevice = explode('x', $sizes[DEVICE_ENV]);
            $strSize = $useFitSize ? 'width="'.$sizeByDevice[0].'" height="'.$sizeByDevice[1].'"' : $strSize;
        }
        
        $pathFile = implode(DIRECTORY_SEPARATOR, [
            '/uploads', $folderName, $folderBySize, $fileName
        ]);

        if (!file_exists(ROOT_PUBLIC_PATH .$pathFile)) {
            $pathFile = implode(DIRECTORY_SEPARATOR, [
                '/uploads', 'default', "notfound_{$folderBySize}.jpg"
            ]);
        }

        if ($onlySrc) {
            return $pathFile;
        } else {
            return '<img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAYkAAAD9AQMAAABz1xmdAAAAAXNSR0IB2cksfwAAAAlwSFlzAAAOxAAADsQBlSsOGwAAAANQTFRFysrKceY6JgAAACFJREFUeJztwQEBAAAAgJD+r+4ICgAAAAAAAAAAAAAAABoyZwABrSWl8wAAAABJRU5ErkJggg==" 
                class="lazyload '.$class.'" data-src="'.$pathFile.'" alt="'.$title.'" '.$strSize.' title="'.$title.'" />';
        }
    }

    /**
     * Get src image
     *
     * @param string $fileName
     * @param string $folderName
     * @param boolean $isFullUrl
     * @param boolean $isDefault
     * @return string
     */
    public static function generateUrlImage(string $fileName, string $folderName = '', bool $isFullUrl = false, bool $isDefault = true): string
    {
        $pathFile = implode(DIRECTORY_SEPARATOR, [
            '/uploads', $folderName, $fileName
        ]);

        if (!file_exists(ROOT_PUBLIC_PATH .$pathFile)) {
            if (!$isDefault) return '';
            
            $pathFile = implode(DIRECTORY_SEPARATOR, [
                '/uploads', 'default', "notfound_500x300.jpg"
            ]);
        }

        return $isFullUrl ? FULL_MAIN_DOMAIN . $pathFile : $pathFile;
    }

    /**
     * Generate image origin
     *
     * @param string $fileName
     * @param array $sizes
     * @param string $title
     * @param string $folderName
     * @param string $class
     * @return string
     */
    public static function generateImageOrigin(string $fileName, array $sizes, string $title, string $folderName, string $class = ''): string
    {
        $folderBySize = $sizes[1];
        $pathFile = implode(DIRECTORY_SEPARATOR, [
            '/uploads', $folderName, $folderBySize, $fileName
        ]);

        if (!file_exists(ROOT_PUBLIC_PATH .$pathFile)) {
            $pathFile = implode(DIRECTORY_SEPARATOR, [
                '/uploads', 'default', "notfound_{$folderBySize}.jpg"
            ]);
        }

        return '<img src="'.$pathFile.'" 
            class="'.$class.'" alt="'.$title.'" title="'.$title.'" />';
    }
}