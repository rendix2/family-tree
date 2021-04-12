<?php
/**
 *
 * Created by PhpStorm.
 * Filename: ILanguageSelector.php
 * User: Tomáš Babický
 * Date: 05.04.2021
 * Time: 1:22
 */

namespace Rendix2\FamilyTree\App\Model\Managers\Language\Interfaces;

use Rendix2\FamilyTree\App\Model\Interfaces\ISelector;

/**
 * Interface ILanguageSelector
 *
 * @package Rendix2\FamilyTree\App\Model\Managers\Language\Interfaces
 */
interface ILanguageSelector extends ISelector
{
    public function pairsForSelect();
}
