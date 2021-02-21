<?php
/**
 *
 * Created by PhpStorm.
 * Filename: WeddingAddTownModal.php
 * User: Tomáš Babický
 * Date: 21.02.2021
 * Time: 1:25
 */

namespace Rendix2\FamilyTree\App\Controls\Modals\Wedding;

use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Forms\TownForm;

/**
 * Class WeddingAddTownModal
 *
 * @package Rendix2\FamilyTree\App\Controls\Modals\Wedding
 */
class WeddingAddTownModal extends Control
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
        $formFactory = new TownForm($this->translator);

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

        $towns = $this->townSettingsManager->getAllPairsCached();

        $this['weddingForm-townId']->setItems($towns);

        $this->payload->showModal = false;

        $this->flashMessage('town_added', self::FLASH_SUCCESS);

        $this->redrawControl('flashes');
        $this->redrawControl('jsFormCallback');

        $this->payload->snippets = [
            $this['weddingForm-townId']->getHtmlId() => (string) $this['weddingForm-townId']->getControl(),
        ];
    }
}
