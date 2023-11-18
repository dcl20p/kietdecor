<?php
namespace Models\Utilities;

class ImageUrl
{
    /**
     * Undocumented function
     *
     * @param string $fileName
     * @param array $sizes
     * @param string $title
     * @param string $folderName
     * @param bool $useFitSize
     * @return string
     */
    public static function generateImage(string $fileName, array $sizes, string $title, string $folderName, bool $useFitSize = false): string 
    {
        $folderBySize = $strSize = 'width="700" height="450"';
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

        return '<img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAYkAAAD9AQMAAABz1xmdAAAAAXNSR0IB2cksfwAAAAlwSFlzAAAOxAAADsQBlSsOGwAAAANQTFRFysrKceY6JgAAACFJREFUeJztwQEBAAAAgJD+r+4ICgAAAAAAAAAAAAAAABoyZwABrSWl8wAAAABJRU5ErkJggg==" 
            class="lazyload" data-src="'.$pathFile.'" alt="'.$title.'" '.$strSize.' title="'.$title.'" />';
    }
}