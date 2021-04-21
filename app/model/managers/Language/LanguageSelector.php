<?php
/**
 *
 * Created by PhpStorm.
 * Filename: LanguageSelector.php
 * User: Tomáš Babický
 * Date: 05.04.2021
 * Time: 1:23
 */

namespace Rendix2\FamilyTree\App\Model\Managers\Language;

use Dibi\Connection;
use Rendix2\FamilyTree\App\Filters\LanguageFilter;
use Rendix2\FamilyTree\App\Model\CrudManager\DefaultSelector;
use Rendix2\FamilyTree\App\Model\Managers\Language\Interfaces\ILanguageSelector;

/**
 * Class LanguageSelector
 *
 * @package Rendix2\FamilyTree\App\Model\Managers\Language
 */
class LanguageSelector extends DefaultSelector implements ILanguageSelector
{
    /**
     * LanguageSelector constructor.
     *
     * @param Connection    $connection
     * @param LanguageTable $table
     */
    public function __construct(
        Connection $connection,
        LanguageTable $table,
        LanguageFilter $filter
    ) {
        parent::__construct($connection, $table, $filter);
    }

    public function pairsForSelect()
    {
        return $this->getAllFluent()
            ->fetchPairs('langName', 'langName');
    }
}
