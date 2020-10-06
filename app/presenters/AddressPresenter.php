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
use Rendix2\FamilyTree\App\BootstrapRenderer;
use Rendix2\FamilyTree\App\Filters\PersonFilter;
use Rendix2\FamilyTree\App\Forms\AddressPersonForm;
use Rendix2\FamilyTree\App\Managers\AddressManager;
use Rendix2\FamilyTree\App\Managers\CountryManager;
use Rendix2\FamilyTree\App\Managers\Person2AddressManager;
use Rendix2\FamilyTree\App\Managers\PersonManager;
use Rendix2\FamilyTree\App\Managers\TownManager;

/**
 * Class AddressPresenter
 *
 * @package Rendix2\FamilyTree\App\Presenters
 */
class AddressPresenter extends BasePresenter
{
    use CrudPresenter {
        actionEdit as traitActionEdit;
        saveForm as traitSaveForm;
    }

    /**
     * @var AddressManager $manager
     */
    private $manager;

    /**
     * @var CountryManager $countryManager
     */
    private $countryManager;

    /**
     * @var Person2AddressManager $person2AddressManager
     */
    private $person2AddressManager;

    /**
     * @var PersonManager $personManager
     */
    private $personManager;

    /**
     * @var TownManager $townManager
     */
    private $townManager;

    /**
     * AddressPresenter constructor.
     *
     * @param AddressManager $addressManager
     * @param CountryManager $countryManager
     * @param Person2AddressManager $person2AddressManager
     * @param PersonManager $personManager
     * @param TownManager $townManager
     */
    public function __construct(
        AddressManager $addressManager,
        CountryManager $countryManager,
        Person2AddressManager $person2AddressManager,
        PersonManager $personManager,
        TownManager $townManager
    ) {
        parent::__construct();

        $this->manager = $addressManager;
        $this->countryManager = $countryManager;
        $this->person2AddressManager = $person2AddressManager;
        $this->personManager = $personManager;
        $this->townManager = $townManager;
    }

    /**
     * @return void
     */
    public function renderDefault()
    {
        $addresses = $this->manager->getAllJoinedCountryJoinedTown();

        $this->template->addresses = $addresses;
    }

    /**
     * @param int|null $id
     */
    public function actionEdit($id = null)
    {
        $countries = $this->countryManager->getPairs('name');

        $this['form-countryId']->setItems($countries);

        if ($id !== null) {
            $this->item = $item = $this->manager->getByPrimaryKey($id);

            if (!$item) {
                $this->error('Item not found.');
            }

            $towns = $this->townManager->getPairsByCountry($this->item->countryId);

            $this['form-townId']
                ->setPrompt($this->getTranslator()->translate('address_select_town'))
                ->setItems($towns)
                ->setRequired('address_town_required');

            $this['form-countryId']->setDisabled(true);
            $this['form']->setDefaults($item);
        }
    }

    /**
     * @param int $value
     */
    public function handleSelectCountry($value)
    {
        if ($value) {
            $towns = $this->townManager->getPairsByCountry($value);

            $this['form-townId']->setPrompt($this->getTranslator()->translate('address_select_town'))
                ->setRequired('address_town_required')
                ->setItems($towns);

            $this['form']->setDefaults(['countryId' => $value]);
        } else {
            $this['form-townId']->setPrompt($this->getTranslator()->translate('address_select_town'))->setItems([]);
        }

        $this->redrawControl('formWrapper');
        $this->redrawControl('country');
        $this->redrawControl('town');
        $this->redrawControl('js');
    }

    /**
     * @param int $id
     */
    public function actionPersons($id)
    {
    }

    /**
     * @param int|null $id
     *
     * @return void
     */
    public function renderEdit($id = null)
    {
        $persons = $this->person2AddressManager->getFluentByRightJoined($id)->fetchAll();

        $this->template->persons = $persons;

        $this->template->addFilter('person', new PersonFilter($this->getTranslator()));
    }

    /**
     * @return Form
     */
    protected function createComponentForm()
    {
        $form = new Form();

        $form->setTranslator($this->getTranslator());

        $form->addProtection();

        $form->addSelect('countryId', $this->getTranslator()->translate('address_country'))
            ->setTranslator(null)
            ->setRequired('address_country_required')
            ->setPrompt($this->getTranslator()->translate('address_select_country'));

        $form->addSelect('townId', $this->getTranslator()->translate('address_town'))
            ->setTranslator(null)
            ->setPrompt($this->getTranslator()->translate('address_select_town'));

        $form->addText('street', 'address_street');
        $form->addInteger('streetNumber', 'address_street_number')
            ->setNullable();
        $form->addInteger('houseNumber', 'address_house_number')
            ->setNullable();

        $form->addSubmit('send', 'save');

        $form->onSuccess[] = [$this, 'saveForm'];
        $form->onRender[] = [BootstrapRenderer::class, 'makeBootstrap4'];

        return $form;
    }

    /**
     * @param Form $form
     * @param ArrayHash $values
     */
    public function saveForm(Form $form, ArrayHash $values)
    {
        $values->townId = (int)$form->getHttpData()['townId'];

        $this->traitSaveForm($form, $values);
    }

    /**
     * @return AddressPersonForm
     */
    public function createComponentPersonsForm()
    {
        return new AddressPersonForm(
            $this->getTranslator(),
            $this->personManager,
            $this->person2AddressManager,
            $this->manager
        );
    }
}
