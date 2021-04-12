<?php
/**
 *
 * Created by PhpStorm.
 * Filename: WeddingSelectRepository.php
 * User: Tomáš Babický
 * Date: 06.04.2021
 * Time: 22:20
 */

namespace Rendix2\FamilyTree\App\Model\Managers\Wedding;


use Dibi\Connection;
use Nette\Caching\IStorage;
use Rendix2\FamilyTree\App\Filters\WeddingFilter;
use Rendix2\FamilyTree\App\Model\CrudManager\DefaultSelectRepository;

class WeddingSelectRepository extends DefaultSelectRepository
{
    /**
     * @var WeddingCachedSelector $weddingCachedSelector
     */
    private $weddingCachedSelector;

    /**
     * @var WeddingSelector $weddingSelector
     */
    private $weddingSelector;

    /**
     * WeddingSelectRepository constructor.
     *
     * @param Connection            $connection
     * @param IStorage              $storage
     * @param WeddingTable          $table
     * @param WeddingFilter         $filter
     * @param WeddingSelector       $weddingSelector
     * @param WeddingCachedSelector $weddingCachedSelector
     */
    public function __construct(
        Connection $connection,
        IStorage $storage,
        WeddingTable $table,
        WeddingFilter $filter,
        WeddingSelector $weddingSelector,
        WeddingCachedSelector $weddingCachedSelector
    ) {
        parent::__construct($connection, $storage, $table, $filter);

        $this->weddingSelector = $weddingSelector;
        $this->weddingCachedSelector = $weddingCachedSelector;
    }

    /**
     * @return WeddingSelector
     */
    public function getManager()
    {
        return $this->weddingSelector;
    }

    /**
     * @return WeddingCachedSelector
     */
    public function getCachedManager()
    {
        return $this->weddingCachedSelector;
    }
}