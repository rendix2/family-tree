<?php
/**
 *
 * Created by PhpStorm.
 * Filename: AddressAddTownModal.php
 * User: Tomáš Babický
 * Date: 09.12.2020
 * Time: 0:37
 */

namespace Rendix2\FamilyTree\App\Controls\Modals\Address;

use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Nette\Localization\ITranslator;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Forms\TownForm;
use Rendix2\FamilyTree\App\Managers\CountryManager;
use Rendix2\FamilyTree\App\Managers\TownManager;
use Rendix2\FamilyTree\App\Managers\TownSettingsManager;
use Rendix2\FamilyTree\App\Presenters\BasePresenter;

/**
 * Class AddressAddTownModal
 *
 * @package Rendix2\FamilyTree\App\Controls\Modals\Address
 */
class AddressAddTownModal extends Control
{
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
     * AddressAddTownModal constructor.
     *
     * @param CountryManager $countryManager
     * @param TownManager $townManager
     * @param TownSettingsManager $townSettingsManager
     * @param ITranslator $translator
     */
    public function __construct(
        CountryManager $countryManager,
        TownManager $townManager,
        TownSettingsManager $townSettingsManager,
        ITranslator $translator
    ) {
        parent::__construct();

        $this->countryManager = $countryManager;
        $this->townManager = $townManager;
        $this->townSettingsManager = $townSettingsManager;
        $this->translator = $translator;
    }

    public function render()
    {
        $this['addressAddTownForm']->render();
    }

    /**
     * @return void
     */
    public function handleAddressAddTown()
    {
        $presenter = $this->presenter;

        if (!$presenter->isAjax()) {
            $presenter->redirect('Address:edit', $presenter->getParameter('id'));
        }

        $countries = $this->countryManager->getPairs('name');

        $this['addressAddTownForm-countryId']->setItems($countries);

        $presenter->template->modalName = 'addressAddTown';

        $presenter->payload->showModal = true;

        $presenter->redrawControl('modal');
    }

    /**
     * @return Form
     */
    protected function createComponentAddressAddTownForm()
    {
        $formFactory = new TownForm($this->translator);

        $form = $formFactory->create();
        $form->onAnchor[] = [$this, 'addressAddTownFormAnchor'];
        $form->onValidate[] = [$this, 'addressAddTownFormValidate'];
        $form->onSuccess[] = [$this, 'addressAddTownFormSuccess'];
        $form->elementPrototype->setAttribute('class', 'ajax');

        return $form;
    }

    /**
     * @return void
     */
    public function addressAddTownFormAnchor()
    {
        $presenter = $this->presenter;

        $presenter->redrawControl('modal');
    }

    /**
     * @param Form $form
     */
    public function addressAddTownFormValidate(Form $form)
    {
        $countries = $this->countryManager->getPairs('name');

        $countryControl = $form->getComponent('countryId');
        $countryControl->setItems($countries)
            ->validate();
    }

    /**
     * @param Form $form
     * @param ArrayHash $values
     */
    public function addressAddTownFormSuccess(Form $form, ArrayHash $values)
    {
        $presenter = $this->presenter;

        if (!$presenter->isAjax()) {
            $presenter->redirect('Address:edit', $presenter->getParameter('id'));
        }

        $this->townManager->add($values);

        $towns = $this->townSettingsManager->getPairsCached('name');

        $presenter['addressForm-townId']->setItems($towns);

        $presenter->payload->showModal = false;
        $presenter->payload->snippets = [
            $presenter['addressForm-townId']->getHtmlId() => (string) $presenter['addressForm-townId']->getControl(),
        ];

        $presenter->flashMessage('town_added', BasePresenter::FLASH_SUCCESS);

        $presenter->redrawControl('flashes');
    }
}
