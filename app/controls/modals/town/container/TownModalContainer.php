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
use Rendix2\FamilyTree\App\Controls\Modals\Town\Factory\TownDeleteJobModalFactory;
use Rendix2\FamilyTree\App\Controls\Modals\Town\Factory\TownDeletePersonBirthModalFactory;
use Rendix2\FamilyTree\App\Controls\Modals\Town\Factory\TownDeletePersonDeathModalFactory;
use Rendix2\FamilyTree\App\Controls\Modals\Town\Factory\TownDeletePersonGravedModalFactory;
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
     * @var TownDeletePersonBirthModalFactory $townDeletePersonBirthModalFactory
     */
    private $townDeletePersonBirthModalFactory;

    /**
     * @var TownDeletePersonDeathModalFactory $townDeletePersonDeathModalFactory
     */
    private $townDeletePersonDeathModalFactory;

    /**
     * @var TownDeletePersonGravedModalFactory $townDeletePersonGravedModalFactory
     */
    private $townDeletePersonGravedModalFactory;

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
     * @param TownDeletePersonBirthModalFactory $townDeletePersonBirthModalFactory
     * @param TownDeletePersonDeathModalFactory $townDeletePersonDeathModalFactory
     * @param TownDeletePersonGravedModalFactory $townDeletePersonGravedModalFactory
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
        TownDeletePersonBirthModalFactory $townDeletePersonBirthModalFactory,
        TownDeletePersonDeathModalFactory $townDeletePersonDeathModalFactory,
        TownDeletePersonGravedModalFactory $townDeletePersonGravedModalFactory,
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
        $this->townDeletePersonBirthModalFactory = $townDeletePersonBirthModalFactory;
        $this->townDeletePersonDeathModalFactory = $townDeletePersonDeathModalFactory;
        $this->townDeletePersonGravedModalFactory = $townDeletePersonGravedModalFactory;
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
     * @return TownDeletePersonBirthModalFactory
     */
    public function getTownDeletePersonBirthModalFactory()
    {
        return $this->townDeletePersonBirthModalFactory;
    }

    /**
     * @return TownDeletePersonDeathModalFactory
     */
    public function getTownDeletePersonDeathModalFactory()
    {
        return $this->townDeletePersonDeathModalFactory;
    }

    /**
     * @return TownDeletePersonGravedModalFactory
     */
    public function getTownDeletePersonGravedModalFactory()
    {
        return $this->townDeletePersonGravedModalFactory;
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