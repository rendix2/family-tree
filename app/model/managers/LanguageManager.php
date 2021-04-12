<?php
/**
 *
 * Created by PhpStorm.
 * Filename: LanguageManager.php
 * User: Tomáš Babický
 * Date: 02.04.2021
 * Time: 15:05
 */

namespace Rendix2\FamilyTree\App\Model\Managers;

use Rendix2\FamilyTree\App\Filters\LanguageFilter;
use Rendix2\FamilyTree\App\Model\CrudManager\DefaultContainer;
use Rendix2\FamilyTree\App\Model\Managers\Language\LanguageSelectRepository;
use Rendix2\FamilyTree\App\Model\Managers\Language\LanguageTable;
use Rendix2\FamilyTree\App\Model\CrudManager\CrudManager;

/**
 * Class LanguageManager
 *
 * @package Rendix2\FamilyTree\App\Model\Managers
 */
class LanguageManager extends CrudManager
{
    /**
     * @var LanguageSelectRepository $languageSelectRepository
     */
    private $languageSelectRepository;

    /**
     * LanguageManager constructor.
     *
     * @param DefaultContainer         $defaultContainer
     * @param LanguageFilter           $languageFilter
     * @param LanguageTable            $table
     * @param LanguageSelectRepository $languageSelectRepository
     */
    public function __construct(
        DefaultContainer $defaultContainer,
        LanguageFilter $languageFilter,
        LanguageTable $table,
        LanguageSelectRepository $languageSelectRepository
    ) {
        parent::__construct($defaultContainer, $table, $languageFilter);

        $this->languageSelectRepository = $languageSelectRepository;
    }

    /**
     * @return LanguageSelectRepository
     */
    public function select()
    {
        return $this->languageSelectRepository;
    }
}
