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
use Rendix2\FamilyTree\App\Model\Interfaces\ISelectRepository;

/**
 * Class WeddingSelectRepository
 *
 * @package Rendix2\FamilyTree\App\Model\Managers\Wedding
 */
class WeddingSelectRepository implements ISelectRepository
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
        WeddingSelector $weddingSelector,
        WeddingCachedSelector $weddingCachedSelector
    ) {
        $this->weddingSelector = $weddingSelector;
        $this->weddingCachedSelector = $weddingCachedSelector;
    }

    public function __destruct()
    {
        $this->weddingSelector = null;
        $this->weddingCachedSelector = null;
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