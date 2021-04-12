<?php
/**
 *
 * Created by PhpStorm.
 * Filename: LanguageFIlter.php
 * User: Tomáš Babický
 * Date: 12.04.2021
 * Time: 0:35
 */

namespace Rendix2\FamilyTree\App\Filters;

use Rendix2\FamilyTree\App\Model\Entities\LanguageEntity;

/**
 * Class LanguageFilter
 *
 * @package Rendix2\FamilyTree\App\Filters
 */
class LanguageFilter implements IFilter
{

    /**
     * @param LanguageEntity $languageEntity
     *
     * @return string
     */
    public function __invoke(LanguageEntity $languageEntity)
    {
        return $languageEntity->langName;
    }
}
