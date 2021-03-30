<?php
/**
 *
 * Created by PhpStorm.
 * Filename: TownModalContainer.php
 * User: Tomáš Babický
 * Date: 23.03.2021
 * Time: 15:18
 */

namespace Rendix2\FamilyTree\App\Controls\Modals\Town\Container;


use Rendix2\FamilyTree\App\Controls\Modals\Town\Factory\TownAddAddressModalFactory;
use Rendix2\FamilyTree\App\Controls\Modals\Town\Factory\TownAddCountryModalFactory;
use Rendix2\FamilyTree\App\Controls\Modals\Town\Factory\TownAddJobModalFactory;
use Rendix2\FamilyTree\App\Controls\Modals\Town\Factory\TownAddWeddingModalFactory;
use Rendix2\FamilyTree\App\Controls\Modals\Town\Factory\TownDeleteAddressModalFactory;
use Rendix2\FamilyTree\App\Controls\Modals\Town\Factory\TownDeleteBirthPersonModalFactory;
use Rendix2\FamilyTree\App\Controls\Modals\Town\Factory\TownDeleteDeathPersonModalFactory;
use Rendix2\FamilyTree\App\Controls\Modals\Town\Factory\TownDeleteGravedPersonModalFactory;
use Rendix2\FamilyTree\App\Controls\Modals\Town\Factory\TownDeleteJobModalFactory;
use Rendix2\FamilyTree\App\Controls\Modals\Town\Factory\TownDeleteTownFromEditModalFactory;
use Rendix2\FamilyTree\App\Controls\Modals\Town\Factory\TownDeleteTownFromListModalFactory;
use Rendix2\FamilyTree\App\Controls\Modals\Town\Factory\TownDeleteTownJobModalFactory;
use Rendix2\FamilyTree\App\Controls\Modals\Town\Factory\TownDeleteWeddingModalFactory;

/**
 * Class TownModalContainer
 *
 * @package Rendix2\FamilyTree\App\Controls\Modals\Town\Container
 */
class TownModalContainer
{
    /**
     * @var TownAddAddressModalFactory $townAddAddressModalFactory
     */
    private $townAddAddressModalFactory;

    /**
     * @var TownAddCountryModalFactory $townAddCountryModalFactory
     */
    private $townAddCountryModalFactory;

    /**
     * @var TownAddJobModalFactory $townAddJobModalFactory
     */
    private $townAddJobModalFactory;

    /**
     * @var TownAddWeddingModalFactory $townAddWeddingModalFactory
     */
    private $townAddWeddingModalFactory;

    /**
     * @var TownDeleteAddressModalFactory $townDeleteAddressModalFactory
     */
    private $townDeleteAddressModalFactory;

    /**
     * @var TownDeleteJobModalFactory $townDeleteJobModalFactory
     */
    private $townDeleteJobModalFactory;

    /**
     * @var TownDeleteBirthPersonModalFactory $townDeleteBirthPersonModalFactory
     */
    private $townDeleteBirthPersonModalFactory;

    /**
     * @var TownDeleteDeathPersonModalFactory $townDeleteDeathPersonModalFactory
     */
    private $townDeleteDeathPersonModalFactory;

    /**
     * @var TownDeleteGravedPersonModalFactory $townDeleteGravedPersonModalFactory
     */
    private $townDeleteGravedPersonModalFactory;

    /**
     * @var TownDeleteTownFromEditModalFactory $$townDeleteTownFromEditModalFactory
     */
    private $townDeleteTownFromEditModalFactory;

    /**
     * @var TownDeleteTownFromListModalFactory $townDeleteTownFromListModalFactory
     */
    private $townDeleteTownFromListModalFactory;

    /**
     * @var TownDeleteTownJobModalFactory $townDeleteTownJobModalFactory
     */
    private $townDeleteTownJobModalFactory;

    /**
     * @var TownDeleteWeddingModalFactory $townDeleteWeddingModalFactory
     */
    private $townDeleteWeddingModalFactory;

    /**
     * TownModalContainer constructor.
     *
     * @param TownAddAddressModalFactory $townAddAddressModalFactory
     * @param TownAddCountryModalFactory $townAddCountryModalFactory
     * @param TownAddJobModalFactory $townAddJobModalFactory
     * @param TownAddWeddingModalFactory $townAddWeddingModalFactory
     * @param TownDeleteAddressModalFactory $townDeleteAddressModalFactory
     * @param TownDeleteJobModalFactory $townDeleteJobModalFactory
     * @param TownDeleteBirthPersonModalFactory $townDeletePersonBirthModalFactory
     * @param TownDeleteDeathPersonModalFactory $townDeletePersonDeathModalFactory
     * @param TownDeleteGravedPersonModalFactory $townDeletePersonGravedModalFactory
     * @param TownDeleteTownFromEditModalFactory $townDeleteTownFromEditModalFactory
     * @param TownDeleteTownFromListModalFactory $townDeleteTownFromListModalFactory
     * @param TownDeleteTownJobModalFactory $townDeleteTownJobModalFactory
     * @param TownDeleteWeddingModalFactory $townDeleteWeddingModalFactory
     */
    public function __construct(
        TownAddAddressModalFactory $townAddAddressModalFactory,
        TownAddCountryModalFactory $townAddCountryModalFactory,
        TownAddJobModalFactory $townAddJobModalFactory,
        TownAddWeddingModalFactory $townAddWeddingModalFactory,
        TownDeleteAddressModalFactory $townDeleteAddressModalFactory,
        TownDeleteJobModalFactory $townDeleteJobModalFactory,
        TownDeleteBirthPersonModalFactory $townDeletePersonBirthModalFactory,
        TownDeleteDeathPersonModalFactory $townDeletePersonDeathModalFactory,
        TownDeleteGravedPersonModalFactory $townDeletePersonGravedModalFactory,
        TownDeleteTownFromEditModalFactory $townDeleteTownFromEditModalFactory,
        TownDeleteTownFromListModalFactory $townDeleteTownFromListModalFactory,
        TownDeleteTownJobModalFactory $townDeleteTownJobModalFactory,
        TownDeleteWeddingModalFactory $townDeleteWeddingModalFactory
    ) {
        $this->townAddAddressModalFactory = $townAddAddressModalFactory;
        $this->townAddCountryModalFactory = $townAddCountryModalFactory;
        $this->townAddJobModalFactory = $townAddJobModalFactory;
        $this->townAddWeddingModalFactory = $townAddWeddingModalFactory;
        $this->townDeleteAddressModalFactory = $townDeleteAddressModalFactory;
        $this->townDeleteJobModalFactory = $townDeleteJobModalFactory;
        $this->townDeleteBirthPersonModalFactory = $townDeletePersonBirthModalFactory;
        $this->townDeleteDeathPersonModalFactory = $townDeletePersonDeathModalFactory;
        $this->townDeleteGravedPersonModalFactory = $townDeletePersonGravedModalFactory;
        $this->townDeleteTownFromEditModalFactory = $townDeleteTownFromEditModalFactory;
        $this->townDeleteTownFromListModalFactory = $townDeleteTownFromListModalFactory;
        $this->townDeleteTownJobModalFactory = $townDeleteTownJobModalFactory;
        $this->townDeleteWeddingModalFactory = $townDeleteWeddingModalFactory;
    }

    /**
     * @return TownAddAddressModalFactory
     */
    public function getTownAddAddressModalFactory()
    {
        return $this->townAddAddressModalFactory;
    }

    /**
     * @return TownAddCountryModalFactory
     */
    public function getTownAddCountryModalFactory()
    {
        return $this->townAddCountryModalFactory;
    }

    /**
     * @return TownAddJobModalFactory
     */
    public function getTownAddJobModalFactory()
    {
        return $this->townAddJobModalFactory;
    }

    /**
     * @return TownAddWeddingModalFactory
     */
    public function getTownAddWeddingModalFactory()
    {
        return $this->townAddWeddingModalFactory;
    }

    /**
     * @return TownDeleteAddressModalFactory
     */
    public function getTownDeleteAddressModalFactory()
    {
        return $this->townDeleteAddressModalFactory;
    }

    /**
     * @return TownDeleteJobModalFactory
     */
    public function getTownDeleteJobModalFactory()
    {
        return $this->townDeleteJobModalFactory;
    }

    /**
     * @return TownDeleteBirthPersonModalFactory
     */
    public function getTownDeleteBirthPersonModalFactory()
    {
        return $this->townDeleteBirthPersonModalFactory;
    }

    /**
     * @return TownDeleteDeathPersonModalFactory
     */
    public function getTownDeleteDeathPersonModalFactory()
    {
        return $this->townDeleteDeathPersonModalFactory;
    }

    /**
     * @return TownDeleteGravedPersonModalFactory
     */
    public function getTownDeleteGravedPersonModalFactory()
    {
        return $this->townDeleteGravedPersonModalFactory;
    }

    /**
     * @return TownDeleteTownFromEditModalFactory
     */
    public function getTownDeleteTownFromEditModalFactory()
    {
        return $this->townDeleteTownFromEditModalFactory;
    }

    /**
     * @return TownDeleteTownFromListModalFactory
     */
    public function getTownDeleteTownFromListModalFactory()
    {
        return $this->townDeleteTownFromListModalFactory;
    }

    /**
     * @return TownDeleteTownJobModalFactory
     */
    public function getTownDeleteTownJobModalFactory()
    {
        return $this->townDeleteTownJobModalFactory;
    }

    /**
     * @return TownDeleteWeddingModalFactory
     */
    public function getTownDeleteWeddingModalFactory()
    {
        return $this->townDeleteWeddingModalFactory;
    }
}