<?php
/**
 *
 * Created by PhpStorm.
 * Filename: AddressModalContainer.php
 * User: Tomáš Babický
 * Date: 23.03.2021
 * Time: 14:35
 */

namespace Rendix2\FamilyTree\App\Controls\Modals\Address\Container;

use Rendix2\FamilyTree\App\Controls\Modals\Address\Factory\AddressAddCountryModalFactory;
use Rendix2\FamilyTree\App\Controls\Modals\Address\Factory\AddressAddJobModalFactory;
use Rendix2\FamilyTree\App\Controls\Modals\Address\Factory\AddressAddPersonAddressModalFactory;
use Rendix2\FamilyTree\App\Controls\Modals\Address\Factory\AddressAddTownModalFactory;
use Rendix2\FamilyTree\App\Controls\Modals\Address\Factory\AddressAddWeddingModalFactory;
use Rendix2\FamilyTree\App\Controls\Modals\Address\Factory\AddressDeleteAddressFromEditModalFactory;
use Rendix2\FamilyTree\App\Controls\Modals\Address\Factory\AddressDeleteAddressFromListModalFactory;
use Rendix2\FamilyTree\App\Controls\Modals\Address\Factory\AddressDeleteAddressJobModalFactory;
use Rendix2\FamilyTree\App\Controls\Modals\Address\Factory\AddressDeleteBirthPersonModalFactory;
use Rendix2\FamilyTree\App\Controls\Modals\Address\Factory\AddressDeleteDeathPersonModalFactory;
use Rendix2\FamilyTree\App\Controls\Modals\Address\Factory\AddressDeleteGravedPersonModalFactory;
use Rendix2\FamilyTree\App\Controls\Modals\Address\Factory\AddressDeleteJobModalFactory;
use Rendix2\FamilyTree\App\Controls\Modals\Address\Factory\AddressDeletePersonAddressModalFactory;
use Rendix2\FamilyTree\App\Controls\Modals\Address\Factory\AddressDeleteWeddingAddressModalFactory;
use Rendix2\FamilyTree\App\Controls\Modals\Address\Factory\AddressDeleteWeddingModalFactory;

/**
 * Class AddressModalContainer
 *
 * @package Rendix2\FamilyTree\App\Controls\Modals\Address\Container
 */
class AddressModalContainer
{
    /**
     * @var AddressAddCountryModalFactory $addressAddCountryModalFactory
     */
    private $addressAddCountryModalFactory;

    /**
     * @var AddressAddJobModalFactory $addressAddJobModalFactory
     */
    private $addressAddJobModalFactory;

    /**
     * @var AddressAddPersonAddressModalFactory $addressAddPersonAddressModalFactory
     */
    private $addressAddPersonAddressModalFactory;

    /**
     * @var AddressAddTownModalFactory $addressAddTownModalFactory
     */
    private $addressAddTownModalFactory;

    /**
     * @var AddressAddWeddingModalFactory $addressAddWeddingModalFactory
     */
    private $addressAddWeddingModalFactory;

    /**
     * @var AddressDeleteAddressFromEditModalFactory $ddressDeleteAddressFromEditModalFactory
     */
    private $addressDeleteAddressFromEditModalFactory;

    /**
     * @var AddressDeleteAddressFromListModalFactory $addressDeleteAddressFromListModalFactory
     */
    private $addressDeleteAddressFromListModalFactory;

    /**
     * @var AddressDeleteAddressJobModalFactory $addressDeleteAddressJobModalFactory
     */
    private $addressDeleteAddressJobModalFactory;

    /**
     * @var AddressDeleteBirthPersonModalFactory $addressDeleteBirthPersonModalFactory
     */
    private $addressDeleteBirthPersonModalFactory;

    /**
     * @var AddressDeleteDeathPersonModalFactory $addressDeleteDeathPersonModalFactory
     */
    private $addressDeleteDeathPersonModalFactory;

    /**
     * @var AddressDeleteGravedPersonModalFactory $addressDeleteGravedPersonModalFactory
     */
    private $addressDeleteGravedPersonModalFactory;

    /**
     * @var AddressDeleteJobModalFactory $addressDeleteJobModalFactory
     */
    private $addressDeleteJobModalFactory;

    /**
     * @var AddressDeletePersonAddressModalFactory $addressDeletePersonAddressModalFactory
     */
    private $addressDeletePersonAddressModalFactory;

    /**
     * @var AddressDeleteWeddingAddressModalFactory $addressDeleteWeddingAddressModalFactory
     */
    private $addressDeleteWeddingAddressModalFactory;

    /**
     * @var AddressDeleteWeddingModalFactory $addressDeleteWeddingModalFactory
     */
    private $addressDeleteWeddingModalFactory;

    /**
     * AddressModalContainer constructor.
     *
     * @param AddressAddCountryModalFactory $addressAddCountryModalFactory
     * @param AddressAddJobModalFactory $addressAddJobModalFactory
     * @param AddressAddPersonAddressModalFactory $addressAddPersonAddressModalFactory
     * @param AddressAddTownModalFactory $addressAddTownModalFactory
     * @param AddressAddWeddingModalFactory $addressAddWeddingModalFactory
     * @param AddressDeleteAddressFromEditModalFactory $addressDeleteAddressFromEditModalFactory
     * @param AddressDeleteAddressFromListModalFactory $addressDeleteAddressFromListModalFactory
     * @param AddressDeleteAddressJobModalFactory $addressDeleteAddressJobModalFactory
     * @param AddressDeleteBirthPersonModalFactory $addressDeleteBirthPersonModalFactory
     * @param AddressDeleteDeathPersonModalFactory $addressDeleteDeathPersonModalFactory
     * @param AddressDeleteGravedPersonModalFactory $addressDeleteGravedPersonModalFactory
     * @param AddressDeleteJobModalFactory $addressDeleteJobModalFactory
     * @param AddressDeletePersonAddressModalFactory $addressDeletePersonAddressModalFactory
     * @param AddressDeleteWeddingAddressModalFactory $addressDeleteWeddingAddressModalFactory
     * @param AddressDeleteWeddingModalFactory $addressDeleteWeddingModalFactory
     */
    public function __construct(
        AddressAddCountryModalFactory $addressAddCountryModalFactory,
        AddressAddJobModalFactory $addressAddJobModalFactory,
        AddressAddPersonAddressModalFactory $addressAddPersonAddressModalFactory,
        AddressAddTownModalFactory $addressAddTownModalFactory,
        AddressAddWeddingModalFactory $addressAddWeddingModalFactory,
        AddressDeleteAddressFromEditModalFactory $addressDeleteAddressFromEditModalFactory,
        AddressDeleteAddressFromListModalFactory $addressDeleteAddressFromListModalFactory,
        AddressDeleteAddressJobModalFactory $addressDeleteAddressJobModalFactory,
        AddressDeleteBirthPersonModalFactory $addressDeleteBirthPersonModalFactory,
        AddressDeleteDeathPersonModalFactory $addressDeleteDeathPersonModalFactory,
        AddressDeleteGravedPersonModalFactory $addressDeleteGravedPersonModalFactory,
        AddressDeleteJobModalFactory $addressDeleteJobModalFactory,
        AddressDeletePersonAddressModalFactory $addressDeletePersonAddressModalFactory,
        AddressDeleteWeddingAddressModalFactory $addressDeleteWeddingAddressModalFactory,
        AddressDeleteWeddingModalFactory $addressDeleteWeddingModalFactory
    ) {
        $this->addressAddCountryModalFactory = $addressAddCountryModalFactory;
        $this->addressAddJobModalFactory = $addressAddJobModalFactory;
        $this->addressAddPersonAddressModalFactory = $addressAddPersonAddressModalFactory;
        $this->addressAddTownModalFactory = $addressAddTownModalFactory;
        $this->addressAddWeddingModalFactory = $addressAddWeddingModalFactory;
        $this->addressDeleteAddressFromEditModalFactory = $addressDeleteAddressFromEditModalFactory;
        $this->addressDeleteAddressFromListModalFactory = $addressDeleteAddressFromListModalFactory;
        $this->addressDeleteAddressJobModalFactory = $addressDeleteAddressJobModalFactory;
        $this->addressDeleteBirthPersonModalFactory = $addressDeleteBirthPersonModalFactory;
        $this->addressDeleteDeathPersonModalFactory = $addressDeleteDeathPersonModalFactory;
        $this->addressDeleteGravedPersonModalFactory = $addressDeleteGravedPersonModalFactory;
        $this->addressDeleteJobModalFactory = $addressDeleteJobModalFactory;
        $this->addressDeletePersonAddressModalFactory = $addressDeletePersonAddressModalFactory;
        $this->addressDeleteWeddingAddressModalFactory = $addressDeleteWeddingAddressModalFactory;
        $this->addressDeleteWeddingModalFactory = $addressDeleteWeddingModalFactory;
    }

    /**
     * @return AddressAddCountryModalFactory
     */
    public function getAddressAddCountryModalFactory()
    {
        return $this->addressAddCountryModalFactory;
    }

    /**
     * @return AddressAddJobModalFactory
     */
    public function getAddressAddJobModalFactory()
    {
        return $this->addressAddJobModalFactory;
    }

    /**
     * @return AddressAddPersonAddressModalFactory
     */
    public function getAddressAddPersonAddressModalFactory()
    {
        return $this->addressAddPersonAddressModalFactory;
    }

    /**
     * @return AddressAddTownModalFactory
     */
    public function getAddressAddTownModalFactory()
    {
        return $this->addressAddTownModalFactory;
    }

    /**
     * @return AddressAddWeddingModalFactory
     */
    public function getAddressAddWeddingModalFactory()
    {
        return $this->addressAddWeddingModalFactory;
    }

    /**
     * @return AddressDeleteAddressFromEditModalFactory
     */
    public function getAddressDeleteAddressFromEditModalFactory()
    {
        return $this->addressDeleteAddressFromEditModalFactory;
    }

    /**
     * @return AddressDeleteAddressFromListModalFactory
     */
    public function getAddressDeleteAddressFromListModalFactory()
    {
        return $this->addressDeleteAddressFromListModalFactory;
    }

    /**
     * @return AddressDeleteAddressJobModalFactory
     */
    public function getAddressDeleteAddressJobModalFactory()
    {
        return $this->addressDeleteAddressJobModalFactory;
    }

    /**
     * @return AddressDeleteBirthPersonModalFactory
     */
    public function getAddressDeleteBirthPersonModalFactory()
    {
        return $this->addressDeleteBirthPersonModalFactory;
    }

    /**
     * @return AddressDeleteDeathPersonModalFactory
     */
    public function getAddressDeleteDeathPersonModalFactory()
    {
        return $this->addressDeleteDeathPersonModalFactory;
    }

    /**
     * @return AddressDeleteGravedPersonModalFactory
     */
    public function getAddressDeleteGravedPersonModalFactory()
    {
        return $this->addressDeleteGravedPersonModalFactory;
    }

    /**
     * @return AddressDeleteJobModalFactory
     */
    public function getAddressDeleteJobModalFactory()
    {
        return $this->addressDeleteJobModalFactory;
    }

    /**
     * @return AddressDeletePersonAddressModalFactory
     */
    public function getAddressDeletePersonAddressModalFactory()
    {
        return $this->addressDeletePersonAddressModalFactory;
    }

    /**
     * @return AddressDeleteWeddingAddressModalFactory
     */
    public function getAddressDeleteWeddingAddressModalFactory()
    {
        return $this->addressDeleteWeddingAddressModalFactory;
    }

    /**
     * @return AddressDeleteWeddingModalFactory
     */
    public function getAddressDeleteWeddingModalFactory()
    {
        return $this->addressDeleteWeddingModalFactory;
    }
}
