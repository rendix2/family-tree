<?php
/**
 *
 * Created by PhpStorm.
 * Filename: SourceSelectRepository.php
 * User: Tomáš Babický
 * Date: 06.04.2021
 * Time: 2:53
 */

namespace Rendix2\FamilyTree\App\Model\Managers\Source;


use Dibi\Connection;
use Nette\Caching\IStorage;
use Rendix2\FamilyTree\App\Filters\SourceFilter;
use Rendix2\FamilyTree\App\Model\CrudManager\DefaultSelectRepository;
use Rendix2\FamilyTree\App\Model\Table\SourceTable;

class SourceManagerSelectRepository extends DefaultSelectRepository
{
    /**
     * @var SourceManagerCachedSelector $sourcCachedSelector
     */
    private $sourcCachedSelector;

    /**
     * @var SourceManagerSelector $sourceSelector
     */
    private $sourceSelector;

    /**
     * SourceSelectRepository constructor.
     *
     * @param Connection                  $connection
     * @param IStorage                    $storage
     * @param SourceFilter                $sourceFilter
     * @param SourceTable                 $table
     * @param SourceManagerCachedSelector $sourceCachedSelector
     * @param SourceManagerSelector       $sourceSelector
     */
    public function __construct(
        Connection $connection,
        IStorage $storage,
        SourceFilter $sourceFilter,
        SourceTable $table,
        SourceManagerCachedSelector $sourceCachedSelector,
        SourceManagerSelector $sourceSelector
    ) {
        parent::__construct($connection, $storage, $table, $sourceFilter);

        $this->sourceSelector = $sourceSelector;
        $this->sourcCachedSelector = $sourceCachedSelector;
    }

    /**
     * @return SourceManagerSelector
     */
    public function getManager()
    {
        return $this->sourceSelector;
    }

    /**
     * @return SourceManagerCachedSelector
     */
    public function getCachedManager()
    {
        return $this->sourcCachedSelector;
    }
}
