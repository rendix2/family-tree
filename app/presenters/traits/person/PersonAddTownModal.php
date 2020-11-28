<?php
/**
 *
 * Created by PhpStorm.
 * Filename: PersonAddTown.php
 * User: Tomáš Babický
 * Date: 26.11.2020
 * Time: 22:38
 */

namespace Rendix2\FamilyTree\App\Presenters\Traits\Person;

use Nette\Application\UI\Form;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Forms\TownForm;

trait PersonAddTownModal
{
    /**
     * @return void
     */
    public function handleAddTown()
    {
        $countries = $this->countryManager->getPairs('name');

        $this['addTownForm-countryId']->setITems($countries);

        $this->template->modalName = 'addTown';

        $this->payload->showModal = true;

        $this->redrawControl('modal');
    }

    /**
     * @return Form
     */
    public function createComponentAddTownForm()
    {
        $formFactory = new TownForm($this->getTranslator());

        $form = $formFactory->create();
        $form->onAnchor[] = [$this, 'addTownFormAnchor'];
        $form->onValidate[] = [$this, 'addTownFormValidate'];
        $form->onSuccess[] = [$this, 'saveTownForm'];

        $form->elementPrototype->setAttribute('class', 'ajax');

        return $form;
    }

    /**
     * @return void
     */
    public function addTownFormAnchor()
    {
        $this->redrawControl('modal');
    }

    /**
     * @param Form $form
     */
    public function addTownFormValidate(Form $form)
    {
        $countries = $this->countryManager->getPairs('name');

        $countryControl = $form->getComponent('countryId');
        $countryControl->setItems($countries);
        $countryControl->validate();
    }

    /**
     * @param Form $form
     * @param ArrayHash $values
     */
    public function saveTownForm(Form $form, ArrayHash $values)
    {
        $this->townManager->add($values);

        $towns = $this->townManager->getAllPairsCached();

        $this['form-birthTownId']->setItems($towns);
        $this['form-deathTownId']->setItems($towns);
        $this['form-gravedTownId']->setItems($towns);

        $this->flashMessage('town_added', self::FLASH_SUCCESS);

        $this->payload->showModal = false;

        $this->redrawControl('formWrappers');
        $this->redrawControl('flashes');
    }
}
