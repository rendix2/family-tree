<?php
/**
 *
 * Created by PhpStorm.
 * Filename: CountryAddTownModal.php
 * User: Tomáš Babický
 * Date: 02.12.2020
 * Time: 1:00
 */

namespace Rendix2\FamilyTree\App\Presenters\Traits\Country;

use Nette\Application\UI\Form;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Forms\TownForm;

/**
 * Trait CountryAddTownModal
 *
 * @package Rendix2\FamilyTree\App\Presenters\Traits\Country
 */
trait CountryAddTownModal
{
    /**
     * @param int $countryId
     *
     * @return void
     */
    public function handleCountryAddTown($countryId)
    {
        $countries = $this->countryManager->getPairs('name');

        $this['countryAddTownForm-_countryId']->setValue($countryId);
        $this['countryAddTownForm-countryId']->setItems($countries)
            ->setDisabled()
            ->setDefaultValue($countryId);

        $this->template->modalName = 'countryAddTown';

        $this->payload->showModal = true;

        $this->redrawControl('modal');
    }

    /**
     * @return Form
     */
    protected function createComponentCountryAddTownForm()
    {
        $formFactory = new TownForm($this->getTranslator());

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
        $this->townManager->add($values);

        $towns = $this->townManager->getAllByCountry($values->countryId);

        $this->template->towns = $towns;

        $this->payload->showModal = false;

        $this->flashMessage('town_added', self::FLASH_SUCCESS);

        $this->redrawControl('flashes');
        $this->redrawControl('towns');
    }
}
