<?php
/**
 *
 * Created by PhpStorm.
 * Filename: AddTownModal.php
 * User: Tomáš Babický
 * Date: 26.11.2020
 * Time: 0:40
 */

namespace Rendix2\FamilyTree\App\Presenters\Traits\Wedding;

use Nette\Application\UI\Form;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Forms\TownForm;

/**
 * Trait AddTownModal
 *
 * @package Rendix2\FamilyTree\App\Presenters\Traits\Wedding
 */
trait WeddingAddTownModal
{
    /**
     * @return void
     */
    public function handleWeddingAddTown()
    {
        $countries = $this->countryManager->getPairs('name');

        $this['weddingAddTownForm-countryId']->setItems($countries);

        $this->template->modalName = 'weddingAddTown';

        $this->payload->showModal = true;

        $this->redrawControl('modal');
    }

    /**
     * @return Form
     */
    protected function createComponentWeddingAddTownForm()
    {
        $formFactory = new TownForm($this->getTranslator());

        $form = $formFactory->create();
        $form->onAnchor[] = [$this, 'weddingAddTownFormAnchor'];
        $form->onValidate[] = [$this, 'weddingAddTownFormValidate'];
        $form->onSuccess[] = [$this, 'weddingAddTownFormSuccess'];
        $form->elementPrototype->setAttribute('class', 'ajax');

        return $form;
    }

    /**
     * @return void
     */
    public function weddingAddTownFormAnchor()
    {
        $this->redrawControl('modal');
    }

    /**
     * @param Form $form
     */
    public function weddingAddTownFormValidate(Form $form)
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
    public function weddingAddTownFormSuccess(Form $form, ArrayHash $values)
    {
        $this->townManager->add($values);

        $towns = $this->townManager->getAllPairsCached();

        $this['form-townId']->setItems($towns);

        $this->payload->showModal = false;

        $this->flashMessage('town_added', self::FLASH_SUCCESS);

        $this->redrawControl('flashes');
        $this->redrawControl('formWrapper');
    }
}
