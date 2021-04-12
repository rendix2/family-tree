<?php
/**
 *
 * Created by PhpStorm.
 * Filename: SourceManager.php
 * User: Tomáš Babický
 * Date: 02.04.2021
 * Time: 15:16
 */

namespace Rendix2\FamilyTree\App\Model\Managers;

use Rendix2\FamilyTree\App\Filters\SourceFilter;
use Rendix2\FamilyTree\App\Model\CrudManager\CrudManager;
use Rendix2\FamilyTree\App\Model\CrudManager\DefaultContainer;
use Rendix2\FamilyTree\App\Model\Managers\Source\SourceManagerSelectRepository;
use Rendix2\FamilyTree\App\Model\Table\SourceTable;

/**
 * Class SourceManager
 *
 * @package Rendix2\FamilyTree\App\Model\Managers
 */
class SourceManager extends CrudManager
{
    /**
     * @var SourceManagerSelectRepository $sourceSelectRepository
     */
    private $sourceSelectRepository;

    /**
     * SourceManager constructor.
     *
     * @param DefaultContainer              $defaultContainer
     * @param SourceTable                   $table
     * @param SourceFilter                  $sourceFilter
     * @param SourceManagerSelectRepository $sourceSelectRepository
     */
    public function __construct(
        DefaultContainer $defaultContainer,
        SourceTable $table,
        SourceFilter $sourceFilter,
        SourceManagerSelectRepository $sourceSelectRepository
    ) {
        parent::__construct($defaultContainer, $table, $sourceFilter);

        $this->sourceSelectRepository = $sourceSelectRepository;
    }

    /**
     * @return SourceManagerSelectRepository
     */
    public function select()
    {
        return $this->sourceSelectRepository;
    }
}
