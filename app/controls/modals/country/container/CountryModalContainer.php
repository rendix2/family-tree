<?php
/**
 *
 * Created by PhpStorm.
 * Filename: CountryModalContainer.php
 * User: Tomáš Babický
 * Date: 23.03.2021
 * Time: 15:51
 */

namespace Rendix2\FamilyTree\App\Controls\Modals\Country\Container;

use Rendix2\FamilyTree\App\Controls\Modals\Country\Factory\CountryAddAddressModalFactory;
use Rendix2\FamilyTree\App\Controls\Modals\Country\Factory\CountryAddTownModalFactory;
use Rendix2\FamilyTree\App\Controls\Modals\Country\Factory\CountryDeleteAddressModalFactory;
use Rendix2\FamilyTree\App\Controls\Modals\Country\Factory\CountryDeleteCountryFromEditModalFactory;
use Rendix2\FamilyTree\App\Controls\Modals\Country\Factory\CountryDeleteCountryFromListModalFactory;
use Rendix2\FamilyTree\App\Controls\Modals\Country\Factory\CountryDeleteTownModalFactory;

/**
 * Class CountryModalContainer
 *
 * @package Rendix2\FamilyTree\App\Controls\Modals\Country\Container
 */
class CountryModalContainer
{

    /**
     * @var CountryAddAddressModalFactory $countryAddAddressModalFactory
     */
    private $countryAddAddressModalFactory;

    /**
     * @var CountryAddTownModalFactory $countryAddTownModalFactory
     */
    private $countryAddTownModalFactory;

    /**
     * @var CountryDeleteAddressModalFactory $countryDeleteAddressModalFactory
     */
    private $countryDeleteAddressModalFactory;

    /**
     * @var CountryDeleteCountryFromEditModalFactory $countryDeleteCountryFromEditModalFactory
     */
    private $countryDeleteCountryFromEditModalFactory;

    /**
     * @var CountryDeleteCountryFromListModalFactory $countryDeleteCountryFromListModalFactory
     */
    private $countryDeleteCountryFromListModalFactory;

    /**
     * @var CountryDeleteTownModalFactory $countryDeleteTownModalFactory
     */
    private $countryDeleteTownModalFactory;

    /**
     * CountryModalContainer constructor.
     *
     * @param CountryAddAddressModalFactory $countryAddAddressModalFactory
     * @param CountryAddTownModalFactory $countryAddTownModalFactory
     * @param CountryDeleteAddressModalFactory $countryDeleteAddressModalFactory
     * @param CountryDeleteCountryFromEditModalFactory $countryDeleteCountryFromEditModalFactory
     * @param CountryDeleteCountryFromListModalFactory $countryDeleteCountryFromListModalFactory
     * @param CountryDeleteTownModalFactory $countryDeleteTownModalFactory
     */
    public function __construct(
        CountryAddAddressModalFactory $countryAddAddressModalFactory,
        CountryAddTownModalFactory $countryAddTownModalFactory,
        CountryDeleteAddressModalFactory $countryDeleteAddressModalFactory,
        CountryDeleteCountryFromEditModalFactory $countryDeleteCountryFromEditModalFactory,
        CountryDeleteCountryFromListModalFactory $countryDeleteCountryFromListModalFactory,
        CountryDeleteTownModalFactory $countryDeleteTownModalFactory
    ) {
        $this->countryAddAddressModalFactory = $countryAddAddressModalFactory;
        $this->countryAddTownModalFactory = $countryAddTownModalFactory;
        $this->countryDeleteAddressModalFactory = $countryDeleteAddressModalFactory;
        $this->countryDeleteCountryFromEditModalFactory = $countryDeleteCountryFromEditModalFactory;
        $this->countryDeleteCountryFromListModalFactory = $countryDeleteCountryFromListModalFactory;
        $this->countryDeleteTownModalFactory = $countryDeleteTownModalFactory;
    }

    /**
     * @return CountryAddAddressModalFactory
     */
    public function getCountryAddAddressModalFactory()
    {
        return $this->countryAddAddressModalFactory;
    }

    /**
     * @return CountryAddTownModalFactory
     */
    public function getCountryAddTownModalFactory()
    {
        return $this->countryAddTownModalFactory;
    }

    /**
     * @return CountryDeleteAddressModalFactory
     */
    public function getCountryDeleteAddressModalFactory()
    {
        return $this->countryDeleteAddressModalFactory;
    }

    /**
     * @return CountryDeleteCountryFromEditModalFactory
     */
    public function getCountryDeleteCountryFromEditModalFactory()
    {
        return $this->countryDeleteCountryFromEditModalFactory;
    }

    /**
     * @return CountryDeleteCountryFromListModalFactory
     */
    public function getCountryDeleteCountryFromListModalFactory()
    {
        return $this->countryDeleteCountryFromListModalFactory;
    }

    /**
     * @return CountryDeleteTownModalFactory
     */
    public function getCountryDeleteTownModalFactory()
    {
        return $this->countryDeleteTownModalFactory;
    }
}
