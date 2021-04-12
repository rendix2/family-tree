<?php
/**
 *
 * Created by PhpStorm.
 * Filename: LanguageCachedSelector.php
 * User: Tomáš Babický
 * Date: 05.04.2021
 * Time: 1:25
 */

namespace Rendix2\FamilyTree\App\Model\Managers\Language;

use Nette\Caching\IStorage;
use Rendix2\FamilyTree\App\Model\CrudManager\DefaultCachedSelector;
use Rendix2\FamilyTree\App\Model\Managers\Language\Interfaces\ILanguageSelector;

/**
 * Class LanguageCachedSelector
 *
 * @package Rendix2\FamilyTree\App\Model\Managers\Language
 */
class LanguageCachedSelector extends DefaultCachedSelector implements ILanguageSelector
{
    /**
     * LanguageCachedSelector constructor.
     *
     * @param IStorage         $storage
     * @param LanguageSelector $selector
     */
    public function __construct(
        IStorage $storage,
        LanguageSelector $selector
    ) {
        parent::__construct($storage, $selector);
    }

    public function pairsForSelect()
    {
        return $this->getCache()->call([$this->getSelector(), 'pairsForSelect']);
    }
}
