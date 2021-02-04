<?php
/**
 *
 * Created by PhpStorm.
 * Filename: FileHelper.php
 * User: Tomáš Babický
 * Date: 04.02.2021
 * Time: 23:49
 */

namespace Rendix2\FamilyTree\App\Model;

/**
 * Class FileHelper
 *
 * @package Rendix2\FamilyTree\App\Model
 */
class FileHelper
{
    /**
     * @param string $extension
     *
     * @return string
     */
    public static function getFileType($extension)
    {
        switch ($extension)
        {
            case 'jpg':
            case 'jpeg':
            case 'gif':
            case 'png':
            case 'tif':
            case 'bmp':
                return 'image';

            case 'rtf':
            case 'txt':
                return 'text';

            case 'doc':
            case 'docx':
                return 'word';

            case 'pdf':
                return 'pdf';

            case 'xlsx':
            case 'xlsm':
            case 'xlsb':
            case 'xltx':
            case 'xls':
            case 'xlt':
                return 'excel';

            case 'pptx':
            case 'pptm':
            case 'ppt':
            case 'xps':
            case 'potx':
            case 'potm':
            case 'pot':
            case 'thmx':
            case 'ppsx':
            case 'ppsm':
            case 'pps':
            case 'ppam':
            case 'ppa':
            case 'odp':
                return 'powerpoint';

            case '7z':
            case 'zip':
            case 'rar':
            case 'tar':
                return 'archive';

            default:
                return 'unknown';
        }
    }
}
