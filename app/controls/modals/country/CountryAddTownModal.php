<?php
/**
 *
 * Created by PhpStorm.
 * Filename: CountryAddTownModal.php
 * User: Tomáš Babický
 * Date: 02.12.2020
 * Time: 1:00
 */

namespace Rendix2\FamilyTree\App\Controls\Modals\Country;

use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Nette\Localization\ITranslator;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Forms\TownForm;
use Rendix2\FamilyTree\App\Managers\CountryManager;
use Rendix2\FamilyTree\App\Managers\TownManager;
use Rendix2\FamilyTree\App\Presenters\BasePresenter;

/**
 * Class CountryAddTownModal
 *
 * @package Rendix2\FamilyTree\App\Controls\Modals\Country
 */
class CountryAddTownModal extends Control
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
     * @var ITranslator $translator
     */
    private $translator;

    /**
     * CountryAddTownModal constructor.
     *
     * @param CountryManager $countryManager
     * @param TownManager $townManager
     * @param ITranslator $translator
     */
    public function __construct(
        CountryManager $countryManager,
        TownManager $townManager,
        ITranslator $translator
    ) {
        parent::__construct();

        $this->countryManager = $countryManager;
        $this->townManager = $townManager;
        $this->translator = $translator;
    }

    /**
     * @param int $countryId
     *
     * @return void
     */
    public function handleCountryAddTown($countryId)
    {
        $presenter = $this->presenter;

        $countries = $this->countryManager->getPairs('name');

        $this['countryAddTownForm-_countryId']->setValue($countryId);
        $this['countryAddTownForm-countryId']->setItems($countries)
            ->setDisabled()
            ->setDefaultValue($countryId);

        $this->template->modalName = 'countryAddTown';

        $presenter->payload->showModal = true;

        $this->redrawControl('modal');
    }

    /**
     * @return Form
     */
    protected function createComponentCountryAddTownForm()
    {
        $formFactory = new TownForm($this->translator);

        $form = $formFactory->create();
        $form->addHidden('_countryId');
        $form->onAnchor[] = [$this, 'countryAddTownFormAnchor'];
        $form->onValidate[] = [$this, 'countryAddTownFormValidate'];
        $form->onSuccess[] = [$this, 'countrySuccessTownFormSuccess'];
        $form->elementPrototype->setAttribute('class', 'ajax');

        return $form;
    }

    /**
     * @return void
     */
    public function countryAddTownFormAnchor()
    {
        $this->redrawControl('modal');
    }

    /**
     * @param Form $form
     */
    public function countryAddTownFormValidate(Form $form)
    {
        $countries = $this->countryManager->getPairs('name');

        $countryHiddenControl = $form->getComponent('_countryId');

        $countryControl = $form->getComponent('countryId');
        $countryControl->setItems($countries)
            ->setValue($countryHiddenControl->getValue())
            ->validate();

        $form->removeComponent($countryHiddenControl);
    }

    /**
     * @param Form $form
     * @param ArrayHash $values
     */
    public function countrySuccessTownForm(Form $form, ArrayHash $values)
    {
        $presenter = $this->presenter;

        $this->townManager->add($values);

        $towns = $this->townManager->getAllByCountry($values->countryId);

        $this->template->towns = $towns;

        $presenter->payload->showModal = false;

        $this->flashMessage('town_added', BasePresenter::FLASH_SUCCESS);

        $this->redrawControl('flashes');
        $this->redrawControl('towns');
    }
}
