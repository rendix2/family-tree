<?php
/**
 *
 * Created by PhpStorm.
 * Filename: AdressPresenter.php
 * User: Tomáš Babický
 * Date: 29.08.2020
 * Time: 2:12
 */

namespace Rendix2\FamilyTree\App\Presenters;

use Nette\Application\UI\Form;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Facades\Person2AddressFacade;
use Rendix2\FamilyTree\App\Facades\PersonFacade;
use Rendix2\FamilyTree\App\Facades\WeddingFacade;
use Rendix2\FamilyTree\App\Filters\AddressFilter;
use Rendix2\FamilyTree\App\Filters\CountryFilter;
use Rendix2\FamilyTree\App\Filters\DurationFilter;
use Rendix2\FamilyTree\App\Filters\JobFilter;
use Rendix2\FamilyTree\App\Filters\PersonFilter;
use Rendix2\FamilyTree\App\Filters\TownFilter;
use Rendix2\FamilyTree\App\Forms\AddressForm;
use Rendix2\FamilyTree\App\Managers\AddressManager;
use Rendix2\FamilyTree\App\Managers\CountryManager;
use Rendix2\FamilyTree\App\Managers\JobManager;
use Rendix2\FamilyTree\App\Managers\Person2AddressManager;
use Rendix2\FamilyTree\App\Managers\PersonManager;
use Rendix2\FamilyTree\App\Managers\TownManager;
use Rendix2\FamilyTree\App\Managers\WeddingManager;
use Rendix2\FamilyTree\App\Model\Facades\AddressFacade;
use Rendix2\FamilyTree\App\Model\Facades\JobFacade;
use Rendix2\FamilyTree\App\Presenters\Traits\Address\AddressDeleteAddressJobModal;
use Rendix2\FamilyTree\App\Presenters\Traits\Address\AddressDeleteBirthPersonModal;
use Rendix2\FamilyTree\App\Presenters\Traits\Address\AddressDeleteDeathPersonModal;
use Rendix2\FamilyTree\App\Presenters\Traits\Address\AddressDeleteGravedPersonModal;
use Rendix2\FamilyTree\App\Presenters\Traits\Address\AddressDeleteJobModal;
use Rendix2\FamilyTree\App\Presenters\Traits\Address\AddressDeletePersonAddressModal;
use Rendix2\FamilyTree\App\Presenters\Traits\Address\AddressDeleteAddressEditModal;
use Rendix2\FamilyTree\App\Presenters\Traits\Address\AddressDeleteAddressListModal;
use Rendix2\FamilyTree\App\Presenters\Traits\Address\AddressDeleteWeddingAddressModal;
use Rendix2\FamilyTree\App\Presenters\Traits\Address\AddressDeleteWeddingModal;

/**
 * Class AddressPresenter
 *
 * @package Rendix2\FamilyTree\App\Presenters
 */
class AddressPresenter extends BasePresenter
{
    use AddressDeleteAddressEditModal;
    use AddressDeleteAddressListModal;

    use AddressDeletePersonAddressModal;

    use AddressDeleteAddressJobModal;
    use AddressDeleteJobModal;

    use AddressDeleteBirthPersonModal;
    use AddressDeleteDeathPersonModal;
    use AddressDeleteGravedPersonModal;

    use AddressDeleteWeddingModal;
    use AddressDeleteWeddingAddressModal;

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
     * @var JobFacade $jobFacade
     */
    private $jobFacade;

    /**
     * @var JobManager $jobManager
     */
    private $jobManager;

    /**
     * @var Person2AddressFacade $person2AddressFacade
     */
    private $person2AddressFacade;

    /**
     * @var Person2AddressManager $person2AddressManager
     */
    private $person2AddressManager;

    /**
     * @var PersonFacade $personFacade
     */
    private $personFacade;

    /**
     * @var PersonManager $personManager
     */
    private $personManager;

    /**
     * @var TownManager $townManager
     */
    private $townManager;

    /**
     * @var WeddingManager $weddingManager
     */
    private $weddingManager;

    /**
     * @var WeddingFacade $weddingFacade
     */
    private $weddingFacade;

    /**
     * AddressPresenter constructor.
     *
     * @param AddressManager $addressManager
     * @param AddressFacade $addressFacade
     * @param CountryManager $countryManager
     * @param JobFacade $jobFacade
     * @param JobManager $jobManager
     * @param Person2AddressFacade $person2AddressFacade
     * @param Person2AddressManager $person2AddressManager
     * @param PersonFacade $personFacade
     * @param PersonManager $personManager
     * @param TownManager $townManager
     * @param WeddingFacade $weddingFacade
     * @param WeddingManager $weddingManager
     */
    public function __construct(
        AddressManager $addressManager,
        AddressFacade $addressFacade,
        CountryManager $countryManager,
        JobFacade $jobFacade,
        JobManager $jobManager,
        Person2AddressFacade $person2AddressFacade,
        Person2AddressManager $person2AddressManager,
        PersonFacade $personFacade,
        PersonManager $personManager,
        TownManager $townManager,
        WeddingFacade $weddingFacade,
        WeddingManager $weddingManager
    ) {
        parent::__construct();

        $this->addressManager = $addressManager;
        $this->addressFacade = $addressFacade;
        $this->countryManager = $countryManager;
        $this->jobFacade = $jobFacade;
        $this->jobManager = $jobManager;
        $this->person2AddressFacade = $person2AddressFacade;
        $this->person2AddressManager = $person2AddressManager;
        $this->personFacade = $personFacade;
        $this->personManager = $personManager;
        $this->townManager = $townManager;
        $this->weddingFacade = $weddingFacade;
        $this->weddingManager = $weddingManager;
    }

    /**
     * @return void
     */
    public function renderDefault()
    {
        $addresses = $this->addressFacade->getAllCached();

        $this->template->addresses = $addresses;

        $this->template->addFilter('address', new AddressFilter());
        $this->template->addFilter('country', new CountryFilter());
        $this->template->addFilter('town', new TownFilter());
    }

    /**
     * @param int|null $id addressId
     */
    public function actionEdit($id = null)
    {
        $countries = $this->countryManager->getPairs('name');

        $this['form-countryId']->setItems($countries);

        if ($id !== null) {
             $address = $this->addressFacade->getByPrimaryKeyCached($id);

            if (!$address) {
                $this->error('Item not found.');
            }

            $towns = $this->townManager->getPairsByCountry($address->town->country->id);

            $this['form-townId']
                ->setPrompt($this->getTranslator()->translate('address_select_town'))
                ->setItems($towns)
                ->setRequired('address_town_required')
                ->setValue($address->town->id);

            $this['form-countryId']->setDefaultValue($address->town->country->id);
            $this['form']->setDefaults((array) $address);
        }
    }

    /**
     * @param int|null $id addressId
     *
     * @return void
     */
    public function renderEdit($id = null)
    {
        if ($id === null) {
            $address = [];
            $jobs = [];
            $persons = [];

            $birthPersons = [];
            $deathPersons = [];
            $gravedPersons = [];

            $weddings = [];
        } else {
            $address = $this->addressFacade->getByPrimaryKeyCached($id);
            $jobs = $this->jobManager->getByAddressId($id);
            $persons = $this->person2AddressFacade->getByRightCached($id);

            $birthPersons = $this->personManager->getByBirthAddressId($id);
            $deathPersons = $this->personManager->getByDeathAddressId($id);
            $gravedPersons = $this->personManager->getByGravedAddressId($id);

            $weddings = $this->weddingFacade->getByAddressId($id);
        }

        $this->template->persons = $persons;
        $this->template->jobs = $jobs;
        $this->template->address = $address;
        $this->template->weddings = $weddings;

        $this->template->birthPersons = $birthPersons;
        $this->template->deathPersons = $deathPersons;
        $this->template->gravedPersons = $gravedPersons;

        $this->template->addFilter('address', new AddressFilter());
        $this->template->addFilter('duration', new DurationFilter($this->getTranslator()));
        $this->template->addFilter('job', new JobFilter());
        $this->template->addFilter('person', new PersonFilter($this->getTranslator(), $this->getHttpRequest()));
        $this->template->addFilter('town', new TownFilter());
    }

    /**
     * @param int $value countryId
     */
    public function handleSelectCountry($value)
    {
        if ($this->isAjax()) {
            if ($value) {
                $towns = $this->townManager->getPairsByCountry($value);

                $this['form-townId']->setPrompt($this->getTranslator()->translate('address_select_town'))
                ->setRequired('address_town_required')
                ->setItems($towns);

                $this['form-countryId']->setDefaultValue($value);
            } else {
                $this['form-townId']->setPrompt($this->getTranslator()->translate('address_select_town'))->setItems([]);
            }

            $this->redrawControl('formWrapper');
            $this->redrawControl('js');
        }
    }

    /**
     * @return Form
     */
    protected function createComponentForm()
    {
        $formFactory = new AddressForm($this->getTranslator());

        $form = $formFactory->create($this);
        $form->onSuccess[] = [$this, 'saveForm'];

        return $form;
    }

    /**
     * @param Form $form
     * @param ArrayHash $values
     */
    public function saveForm(Form $form, ArrayHash $values)
    {
        $values->townId = (int)$form->getHttpData()['townId'];

        $id = $this->getParameter('id');

        if ($id) {
            $this->addressManager->updateByPrimaryKey($id, $values);

            $this->flashMessage('address_saved', self::FLASH_SUCCESS);
        } else {
            $id = $this->addressManager->add($values);

            $this->flashMessage('address_added', self::FLASH_SUCCESS);
        }

        $this->redirect('Address:edit', $id);
    }
}
