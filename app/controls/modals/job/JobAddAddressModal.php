<?php
/**
 *
 * Created by PhpStorm.
 * Filename: JobAddAddressModal.php
 * User: Tomáš Babický
 * Date: 03.12.2020
 * Time: 0:46
 */

namespace Rendix2\FamilyTree\App\Controls\Modals\Job;

use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Nette\Localization\ITranslator;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Forms\AddressForm;
use Rendix2\FamilyTree\App\Forms\FormJsonDataParser;
use Rendix2\FamilyTree\App\Forms\Settings\AddressSettings;
use Rendix2\FamilyTree\App\Managers\AddressManager;
use Rendix2\FamilyTree\App\Managers\CountryManager;
use Rendix2\FamilyTree\App\Managers\TownManager;
use Rendix2\FamilyTree\App\Managers\TownSettingsManager;
use Rendix2\FamilyTree\App\Model\Facades\AddressFacade;
use Rendix2\FamilyTree\App\Presenters\BasePresenter;

/**
 * Class JobAddAddressModal
 *
 * @package Rendix2\FamilyTree\App\Controls\Modals\Job
 */
class JobAddAddressModal extends Control
{
    /**
     * @var AddressFacade $addressFacade
     */
    private $addressFacade;

    /**
     * @var AddressManager $addressManager
     */
    private $addressManager;

    /**
     * @var CountryManager $countryManager
     */
    private $countryManager;

    /**
     * @var TownManager $townManager
     */
    private $townManager;

    /**
     * @var TownSettingsManager $townSettingsManager
     */
    private $townSettingsManager;

    /**
     * @var ITranslator $translator
     */
    private $translator;

    /**
     * JobAddAddressModal constructor.
     *
     * @param AddressFacade $addressFacade
     * @param AddressManager $addressManager
     * @param CountryManager $countryManager
     * @param TownManager $townManager
     * @param TownSettingsManager $townSettingsManager
     * @param ITranslator $translator
     */
    public function __construct(
        AddressFacade $addressFacade,
        AddressManager $addressManager,
        CountryManager $countryManager,
        TownManager $townManager,
        TownSettingsManager $townSettingsManager,
        ITranslator $translator
    ) {
        parent::__construct();

        $this->addressFacade = $addressFacade;
        $this->addressManager = $addressManager;
        $this->countryManager = $countryManager;
        $this->townManager = $townManager;
        $this->townSettingsManager = $townSettingsManager;
        $this->translator = $translator;
    }

    public function render()
    {
        $this['jobAddAddressForm']->render();
    }

    /**
     * @return void
     */
    public function handleJobAddAddress()
    {
        $presenter = $this->presenter;

        $countries = $this->countryManager->getPairs('name');

        $this['jobAddAddressForm-countryId']->setItems($countries);

        $presenter->template->modalName = 'jobAddAddress';

        $presenter->payload->showModal = true;

        $presenter->redrawControl('modal');
        $presenter->redrawControl('js');
    }

    /**
     * @param int $countryId countryId
     * @param string $formData
     */
    public function handleJobAddAddressSelectCountry($countryId, $formData)
    {
        $presenter = $this->presenter;

        if (!$presenter->isAjax()) {
            $presenter->redirect('Job:edit', $presenter->getParameter('id'));
        }

        $countries = $this->countryManager->getPairs('name');

        $formDataParsed = FormJsonDataParser::parse($formData);
        unset($formDataParsed['townId']);

        if ($countryId) {
            $this['jobAddAddressForm-countryId']->setItems($countries)
                ->setDefaultValue($countryId);

            $towns = $this->townSettingsManager->getPairsByCountry($countryId);

            $this['jobAddAddressForm-townId']
                ->setItems($towns);
        } else {
            $this['jobAddAddressForm-countryId']->setItems($countries)
                ->setDefaultValue(null);

            $this['jobAddAddressForm-townId']->setItems([]);
        }

        $this['jobAddAddressForm']->setDefaults($formDataParsed);

        $presenter->payload->snippets = [
            $this['jobAddAddressForm-townId']->getHtmlId() => (string) $this['jobAddAddressForm-townId']->getControl(),
        ];

        $presenter->redrawControl('jsFormCallback');
    }

    /**
     * @return Form
     */
    protected function createComponentJobAddAddressForm()
    {
        $addressSettings = new AddressSettings();
        $addressSettings->selectCountryHandle = $this->link('jobAddAddressSelectCountry!');

        $formFactory = new AddressForm($this->translator, $addressSettings);

        $form = $formFactory->create();
        $form->addHidden('_townId');
        $form->onValidate[] = [$this, 'jobAddAddressFormValidate'];
        $form->onSuccess[] = [$this, 'jobAddAddressFormSuccess'];
        $form->elementPrototype->setAttribute('class', 'ajax');

        return $form;
    }

    /**
     * @param Form $form
     */
    public function jobAddAddressFormValidate(Form $form)
    {
        $countries = $this->countryManager->getPairs('name');

        $countryControl = $form->getComponent('countryId');
        $countryControl->setItems($countries)
            ->validate();

        $towns = $this->townManager->getPairsByCountry($countryControl->getValue());

        $townHiddenControl = $form->getComponent('_townId');

        $townControl = $form->getComponent('townId');
        $townControl->setItems($towns)
            ->validate();

        $form->removeComponent($townHiddenControl);
    }

    /**
     * @param Form $form
     * @param ArrayHash $values
     */
    public function jobAddAddressFormSuccess(Form $form, ArrayHash $values)
    {
        $presenter = $this->presenter;

        $this->addressManager->add($values);

        $addresses = $this->addressFacade->getPairsCached();

        $presenter['jobForm-addressId']->setItems($addresses);

        $presenter->payload->showModal = false;

        $presenter->flashMessage('address_added', BasePresenter::FLASH_SUCCESS);

        $presenter->payload->snippets = [
            $presenter['jobForm-addressId']->getHtmlId() => (string) $presenter['jobForm-addressId']->getControl(),
        ];

        $presenter->redrawControl('flashes');
        $presenter->redrawControl('jsFormCallback');
    }
}
