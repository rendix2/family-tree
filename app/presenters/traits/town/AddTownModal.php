<?php
/**
 *
 * Created by PhpStorm.
 * Filename: AddTownModal.php
 * User: Tomáš Babický
 * Date: 26.11.2020
 * Time: 0:40
 */

namespace Rendix2\FamilyTree\App\Presenters\Traits\Town;

use Nette\Application\UI\Form;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Forms\TownForm;

/**
 * Trait AddTownModal
 *
 * @package Rendix2\FamilyTree\App\Presenters\Traits\Town
 */
trait AddTownModal
{
    /**
     * @return void
     */
    public function handleTownAddTown()
    {
        $countries = $this->countryManager->getPairs('name');

        $this['townAddTownForm-countryId']->setItems($countries);

        $this->template->modalName = 'townAddTown';

        $this->payload->showModal = true;

        $this->redrawControl('modal');
    }

    /**
     * @return Form
     */
    protected function createComponentTownAddTownForm()
    {
        $formFactory = new TownForm($this->getTranslator());

        $form = $formFactory->create();
        $form->onAnchor[] = [$this, 'townAddTownFormAnchor'];
        $form->onValidate[] = [$this, 'townAddTownFormValidate'];
        $form->onSuccess[] = [$this, 'townAddTownFormSuccess'];
        $form->elementPrototype->setAttribute('class', 'ajax');

        return $form;
    }

    /**
     * @return void
     */
    public function townAddTownFormAnchor()
    {
        $this->redrawControl('modal');
    }

    /**
     * @param Form $form
     */
    public function townAddTownFormValidate(Form $form)
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
    public function townAddTownFormSuccess(Form $form, ArrayHash $values)
    {
        $this->townManager->add($values);

        $this->payload->showModal = false;

        $this->flashMessage('town_added', self::FLASH_SUCCESS);

        $this->redrawControl('flashes');
    }
}
