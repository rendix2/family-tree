<?php
/**
 *
 * Created by PhpStorm.
 * Filename: CountryPresenter.php
 * User: Tomáš Babický
 * Date: 04.10.2020
 * Time: 21:48
 */

namespace Rendix2\FamilyTree\App\Presenters;

use Nette\Application\UI\Form;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Filters\AddressFilter;
use Rendix2\FamilyTree\App\Filters\CountryFilter;
use Rendix2\FamilyTree\App\Filters\TownFilter;
use Rendix2\FamilyTree\App\Forms\CountryForm;
use Rendix2\FamilyTree\App\Managers\AddressManager;
use Rendix2\FamilyTree\App\Managers\CountryManager;
use Rendix2\FamilyTree\App\Managers\TownManager;
use Rendix2\FamilyTree\App\Model\Facades\AddressFacade;
use Rendix2\FamilyTree\App\Model\Facades\TownFacade;
use Rendix2\FamilyTree\App\Presenters\Traits\Country\CountryAddAddressModal;
use Rendix2\FamilyTree\App\Presenters\Traits\Country\CountryAddTownModal;
use Rendix2\FamilyTree\App\Presenters\Traits\Country\CountryDeleteAddressModal;
use Rendix2\FamilyTree\App\Presenters\Traits\country\CountryDeleteEditModal;
use Rendix2\FamilyTree\App\Presenters\Traits\Country\CountryDeleteListModal;
use Rendix2\FamilyTree\App\Presenters\Traits\Country\CountryDeleteTownModal;

/**
 * Class CountryPresenter
 *
 * @package Rendix2\FamilyTree\App\Presenters
 */
class CountryPresenter extends BasePresenter
{
    use CountryDeleteListModal;
    use CountryDeleteEditModal;

    use CountryAddAddressModal;
    use CountryAddTownModal;

    use CountryDeleteTownModal;
    use CountryDeleteAddressModal;

    /**
     * @var AddressManager $addressManager
     */
    private $addressManager;

    /**
     * @var AddressFacade $addressFacade
     */
    private $addressFacade;

    /**
     * @var CountryManager $countryManager
     */
    private $countryManager;

    /**
     * @var TownFacade $townFacade
     */
    private $townFacade;

    /**
     * @var TownManager $townManager
     */
    private $townManager;

    /**
     * CountryPresenter constructor.
     *
     * @param AddressManager $addressManager
     * @param AddressFacade $addressFacade
     * @param CountryManager $countryManager
     * @param TownFacade $townFacade
     * @param TownManager $townManager
     */
    public function __construct(
        AddressManager $addressManager,
        AddressFacade $addressFacade,
        CountryManager $countryManager,
        TownFacade $townFacade,
        TownManager $townManager
    ) {
        parent::__construct();

        $this->addressManager = $addressManager;
        $this->addressFacade = $addressFacade;
        $this->countryManager = $countryManager;
        $this->townFacade = $townFacade;
        $this->townManager = $townManager;
    }

    /**
     * @return void
     */
    public function renderDefault()
    {
        $countries = $this->countryManager->getAllCached();

        $this->template->countries = $countries;

        $this->template->addFilter('country', new CountryFilter());
    }

    /**
     * @param int|null $id countryId
     */
    public function actionEdit($id = null)
    {
        if ($id !== null) {
            $country = $this->countryManager->getByPrimaryKeyCached($id);

            if (!$country) {
                $this->error('Item not found.');
            }

            $this['form']->setDefaults((array) $country);
        }
    }

    /**
     * @param int|null $id countryId
     */
    public function renderEdit($id = null)
    {
        if ($id === null) {
            $towns = [];
            $addresses = [];
        } else {
            $towns = $this->townManager->getAllByCountry($id);
            $addresses = $this->addressFacade->getByCountryId($id);
        }

        $country = $this->countryManager->getByPrimaryKeyCached($id);

        $this->template->towns = $towns;
        $this->template->addresses = $addresses;
        $this->template->country = $country;

        $this->template->addFilter('country', new CountryFilter());
        $this->template->addFilter('town', new TownFilter());
        $this->template->addFilter('address', new AddressFilter());
    }

    /**
     * @return Form
     */
    public function createComponentForm()
    {
        $formFactory = new CountryForm($this->getTranslator());

        $form = $formFactory->create();
        $form->onSuccess[] = [$this, 'saveForm'];

        return $form;
    }


    /**
     * @param Form $form
     * @param ArrayHash $values
     */
    public function saveForm(Form $form, ArrayHash $values)
    {
        $id = $this->getParameter('id');

        if ($id) {
            $this->countryManager->updateByPrimaryKey($id, $values);

            $this->flashMessage('country_saved', self::FLASH_SUCCESS);
        } else {
            $id = $this->countryManager->add($values);

            $this->flashMessage('country_added', self::FLASH_SUCCESS);
        }

        $this->redirect('Country:edit', $id);
    }
}
