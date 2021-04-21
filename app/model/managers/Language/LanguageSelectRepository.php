<?php
/**
 *
 * Created by PhpStorm.
 * Filename: LanguageSelectRepository.php
 * User: Tomáš Babický
 * Date: 05.04.2021
 * Time: 1:24
 */

namespace Rendix2\FamilyTree\App\Model\Managers\Language;

use Dibi\Connection;
use Nette\Caching\IStorage;
use Rendix2\FamilyTree\App\Model\Interfaces\ISelectRepository;

/**
 * Class LanguageSelectRepository
 *
 * @package Rendix2\FamilyTree\App\Model\Managers\Language
 */
class LanguageSelectRepository implements ISelectRepository
{
    /**
     * @var LanguageCachedSelector $languageCachedSelector
     */
    private $languageCachedSelector;

    /**
     * @var LanguageSelector $languageSelector
     */
    private $languageSelector;

    /**
     * LanguageSelectRepository constructor.
     *
     * @param Connection             $connection
     * @param IStorage               $storage
     * @param LanguageTable          $table
     * @param LanguageSelector       $languageSelector
     * @param LanguageCachedSelector $languageCachedSelector
     */
    public function __construct(
        LanguageSelector $languageSelector,
        LanguageCachedSelector $languageCachedSelector
    ) {
        $this->languageSelector = $languageSelector;
        $this->languageCachedSelector = $languageCachedSelector;
    }

    public function __destruct()
    {
        $this->languageSelector = null;
        $this->languageCachedSelector = null;
    }

    /**
     * @return LanguageSelector
     */
    public function getManager()
    {
        return $this->languageSelector;
    }

    /**
     * @return LanguageCachedSelector
     */
    public function getCachedManager()
    {
        return $this->languageCachedSelector;
    }
}
