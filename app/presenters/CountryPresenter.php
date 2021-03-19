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
use Rendix2\FamilyTree\App\Managers\TownSettingsManager;
use Rendix2\FamilyTree\App\Model\Facades\AddressFacade;
use Rendix2\FamilyTree\App\Model\Facades\TownFacade;
use Rendix2\FamilyTree\App\Model\Facades\TownSettingsFacade;
use Rendix2\FamilyTree\App\Presenters\Traits\Country\CountryAddAddressModal;
use Rendix2\FamilyTree\App\Presenters\Traits\Country\CountryAddTownModal;
use Rendix2\FamilyTree\App\Presenters\Traits\Country\CountryDeleteAddressModal;
use Rendix2\FamilyTree\App\Presenters\Traits\country\CountryDeleteCountryFromEditModal;
use Rendix2\FamilyTree\App\Presenters\Traits\Country\CountryDeleteCountryFromListModal;
use Rendix2\FamilyTree\App\Presenters\Traits\Country\CountryDeleteTownModal;

/**
 * Class CountryPresenter
 *
 * @package Rendix2\FamilyTree\App\Presenters
 */
class CountryPresenter extends BasePresenter
{
    use CountryDeleteCountryFromListModal;
    use CountryDeleteCountryFromEditModal;

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
     * @var AddressFilter $addressFilter
     */
    private $addressFilter;

    /**
     * @var CountryFilter $countryFilter
     */
    private $countryFilter;

    /**
     * @var CountryManager $countryManager
     */
    private $countryManager;

    /**
     * @var TownFacade $townFacade
     */
    private $townFacade;

    /**
     * @var TownFilter $townFilter
     */
    private $townFilter;

    /**
     * @var TownSettingsFacade $townSettingsFacade
     */
    private $townSettingsFacade;

    /**
     * @var TownManager $townManager
     */
    private $townManager;

    /**
     * @var TownSettingsManager $townSettingsManager
     */
    private $townSettingsManager;

    /**
     * CountryPresenter constructor.
     *
     * @param AddressManager $addressManager
     * @param AddressFacade $addressFacade
     * @param AddressFilter $addressFilter
     * @param CountryFilter $countryFilter
     * @param CountryManager $countryManager
     * @param TownFacade $townFacade
     * @param TownFilter $townFilter
     * @param TownSettingsFacade $townSettingsFacade
     * @param TownManager $townManager
     * @param TownSettingsManager $townSettingsManager
     */
    public function __construct(
        AddressManager $addressManager,
        AddressFacade $addressFacade,
        AddressFilter $addressFilter,
        CountryFilter $countryFilter,
        CountryManager $countryManager,
        TownFacade $townFacade,
        TownFilter $townFilter,
        TownSettingsFacade $townSettingsFacade,
        TownManager $townManager,
        TownSettingsManager $townSettingsManager
    ) {
        parent::__construct();

        $this->addressManager = $addressManager;
        $this->addressFacade = $addressFacade;

        $this->addressFilter = $addressFilter;

        $this->countryFilter = $countryFilter;

        $this->countryManager = $countryManager;

        $this->townFacade = $townFacade;
        $this->townSettingsFacade = $townSettingsFacade;

        $this->townFilter = $townFilter;

        $this->townManager = $townManager;
        $this->townSettingsManager = $townSettingsManager;
    }

    /**
     * @return void
     */
    public function renderDefault()
    {
        $countries = $this->countryManager->getAllCached();

        $this->template->countries = $countries;
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

            $this['countryForm']->setDefaults((array) $country);
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
            $towns = $this->townSettingsManager->getAllByCountry($id);
            $addresses = $this->addressFacade->getByCountryId($id);
        }

        $country = $this->countryManager->getByPrimaryKeyCached($id);

        $this->template->towns = $towns;
        $this->template->addresses = $addresses;
        $this->template->country = $country;
    }

    /**
     * @return Form
     */
    public function createComponentCountryForm()
    {
        $formFactory = new CountryForm($this->translator);

        $form = $formFactory->create();
        $form->onSuccess[] = [$this, 'countryFormSuccess'];

        return $form;
    }


    /**
     * @param Form $form
     * @param ArrayHash $values
     */
    public function countryFormSuccess(Form $form, ArrayHash $values)
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
