<?php
/**
 *
 * Created by PhpStorm.
 * Filename: WeddingModalContainer.php
 * User: Tomáš Babický
 * Date: 23.03.2021
 * Time: 14:10
 */

namespace Rendix2\FamilyTree\App\Controls\Modals\Wedding\Container;

use Rendix2\FamilyTree\App\Controls\Modals\Wedding\Factory\WeddingAddAddressModalFactory;
use Rendix2\FamilyTree\App\Controls\Modals\Wedding\Factory\WeddingAddTownModalFactory;
use Rendix2\FamilyTree\App\Controls\Modals\Wedding\Factory\WeddingDeleteWeddingFromEditModalFactory;
use Rendix2\FamilyTree\App\Controls\Modals\Wedding\Factory\WeddingDeleteWeddingFromListModalFactory;

/**
 * Class WeddingModalContainer
 *
 * @package Rendix2\FamilyTree\App\Controls\Modals\Wedding\Container
 */
class WeddingModalContainer
{
    /**
     * @var WeddingAddAddressModalFactory $weddingAddAddressModalFactory
     */
    private $weddingAddAddressModalFactory;

    /**
     * @var WeddingAddTownModalFactory $weddingAddTownModalFactory
     */
    private $weddingAddTownModalFactory;

    /**
     * @var WeddingDeleteWeddingFromEditModalFactory $weddingDeleteWeddingFromEditModalFactory
     */
    private $weddingDeleteWeddingFromEditModalFactory;

    /**
     * @var WeddingDeleteWeddingFromListModalFactory $weddingDeleteWeddingFromListModalFactory
     */
    private $weddingDeleteWeddingFromListModalFactory;

    /**
     * WeddingModalContainer constructor.
     *
     * @param WeddingAddAddressModalFactory $weddingAddAddressModalFactory
     * @param WeddingAddTownModalFactory $weddingAddTownModalFactory
     * @param WeddingDeleteWeddingFromEditModalFactory $weddingDeleteWeddingFromEditModalFactory
     * @param WeddingDeleteWeddingFromListModalFactory $weddingDeleteWeddingFromListModalFactory
     */
    public function __construct(
        WeddingAddAddressModalFactory $weddingAddAddressModalFactory,
        WeddingAddTownModalFactory $weddingAddTownModalFactory,
        WeddingDeleteWeddingFromEditModalFactory $weddingDeleteWeddingFromEditModalFactory,
        WeddingDeleteWeddingFromListModalFactory $weddingDeleteWeddingFromListModalFactory
    ) {
        $this->weddingAddAddressModalFactory = $weddingAddAddressModalFactory;
        $this->weddingAddTownModalFactory = $weddingAddTownModalFactory;
        $this->weddingDeleteWeddingFromEditModalFactory = $weddingDeleteWeddingFromEditModalFactory;
        $this->weddingDeleteWeddingFromListModalFactory = $weddingDeleteWeddingFromListModalFactory;
    }

    /**
     * @return WeddingAddAddressModalFactory
     */
    public function getWeddingAddAddressModalFactory()
    {
        return $this->weddingAddAddressModalFactory;
    }

    /**
     * @return WeddingAddTownModalFactory
     */
    public function getWeddingAddTownModalFactory()
    {
        return $this->weddingAddTownModalFactory;
    }

    /**
     * @return WeddingDeleteWeddingFromEditModalFactory
     */
    public function getWeddingDeleteWeddingFromEditModalFactory()
    {
        return $this->weddingDeleteWeddingFromEditModalFactory;
    }

    /**
     * @return WeddingDeleteWeddingFromListModalFactory
     */
    public function getWeddingDeleteWeddingFromListModalFactory()
    {
        return $this->weddingDeleteWeddingFromListModalFactory;
    }
}
