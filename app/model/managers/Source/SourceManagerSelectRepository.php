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
use Rendix2\FamilyTree\App\Model\Interfaces\ISelectRepository;
use Rendix2\FamilyTree\App\Model\Table\SourceTable;

/**
 * Class SourceManagerSelectRepository
 *
 * @package Rendix2\FamilyTree\App\Model\Managers\Source
 */
class SourceManagerSelectRepository implements ISelectRepository
{
    /**
     * @var SourceManagerCachedSelector $cachedSelector
     */
    private $cachedSelector;

    /**
     * @var SourceManagerSelector $selector
     */
    private $selector;

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
        SourceManagerCachedSelector $sourceCachedSelector,
        SourceManagerSelector $sourceSelector
    ) {
        $this->selector = $sourceSelector;
        $this->cachedSelector = $sourceCachedSelector;
    }

    public function __destruct()
    {
        $this->selector = null;
        $this->cachedSelector = null;
    }

    /**
     * @return SourceManagerSelector
     */
    public function getManager()
    {
        return $this->selector;
    }

    /**
     * @return SourceManagerCachedSelector
     */
    public function getCachedManager()
    {
        return $this->cachedSelector;
    }
}
