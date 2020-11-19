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
use Rendix2\FamilyTree\App\Filters\AddressFilter;
use Rendix2\FamilyTree\App\Filters\CountryFilter;
use Rendix2\FamilyTree\App\Filters\TownFilter;
use Rendix2\FamilyTree\App\Forms\CountryForm;
use Rendix2\FamilyTree\App\Managers\CountryManager;
use Rendix2\FamilyTree\App\Managers\TownManager;
use Rendix2\FamilyTree\App\Model\Facades\AddressFacade;
use Rendix2\FamilyTree\App\Presenters\Traits\Country\CountryAddressDeleteModal;
use Rendix2\FamilyTree\App\Presenters\Traits\Country\CountryTownDeleteModal;

/**
 * Class CountryPresenter
 *
 * @package Rendix2\FamilyTree\App\Presenters
 */
class CountryPresenter extends BasePresenter
{
    use CrudPresenter {
        actionEdit as traitActionEdit;
    }

    use CountryTownDeleteModal;
    use CountryAddressDeleteModal;

    /**
     * @var CountryManager $manager
     */
    private $manager;

    /**
     * @var TownManager $townManager
     */
    private $townManager;

    /**
     * @var AddressFacade $addressFacade
     */
    private $addressFacade;

    /**
     * CountryPresenter constructor.
     *
     * @param AddressFacade $addressFacade
     * @param CountryManager $countryManager
     * @param TownManager $townManager
     */
    public function __construct(
        AddressFacade $addressFacade,
        CountryManager $countryManager,
        TownManager $townManager
    ) {
        parent::__construct();

        $this->addressFacade = $addressFacade;
        $this->manager = $countryManager;
        $this->townManager = $townManager;
    }

    /**
     * @return void
     */
    public function renderDefault()
    {
        $countries = $this->manager->getAllCached();

        $this->template->countries = $countries;
    }

    /**
     * @param int|null $id
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

        $this->template->towns = $towns;
        $this->template->addresses = $addresses;
        $this->template->country = $this->item;

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
}