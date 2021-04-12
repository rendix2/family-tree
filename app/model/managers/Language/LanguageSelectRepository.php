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
use Rendix2\FamilyTree\App\Model\CrudManager\DefaultSelectRepository;

/**
 * Class LanguageSelectRepository
 *
 * @package Rendix2\FamilyTree\App\Model\Managers\Language
 */
class LanguageSelectRepository extends DefaultSelectRepository
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
        Connection $connection,
        IStorage $storage,
        LanguageTable $table,
        LanguageSelector $languageSelector,
        LanguageCachedSelector $languageCachedSelector
    ) {
        parent::__construct($connection, $storage, $table);

        $this->languageSelector = $languageSelector;
        $this->languageCachedSelector = $languageCachedSelector;
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
