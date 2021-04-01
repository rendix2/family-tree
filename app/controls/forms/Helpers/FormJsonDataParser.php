<?php
/**
 *
 * Created by PhpStorm.
 * Filename: FormDataCleaner.php
 * User: Tomáš Babický
 * Date: 05.12.2020
 * Time: 3:17
 */

namespace Rendix2\FamilyTree\App\Controls\Forms\Helpers;

use Nette\Utils\Json;

/**
 * Class FormDataCleaner
 *
 * @package Rendix2\FamilyTree\App\Controls\Forms
 */
class FormJsonDataParser
{
    /**
     * @param string $formData
     *
     * @return array
     */
    public static function parse($formData)
    {
        $formData = Json::decode($formData, Json::FORCE_ARRAY);
        unset($formData['_do'], $formData['_token_']);

        foreach ($formData as $key => $value) {
            if (empty($value) && $value !== false) { // false because of checkboxes
                unset($formData[$key]);
            }
        }

        return $formData;
    }
}
