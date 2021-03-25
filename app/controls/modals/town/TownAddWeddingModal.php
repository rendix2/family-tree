<?php
/**
 *
 * Created by PhpStorm.
 * Filename: TownAddModalWedding.php
 * User: Tomáš Babický
 * Date: 02.12.2020
 * Time: 1:58
 */

namespace Rendix2\FamilyTree\App\Controls\Modals\Town;

use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Forms\Settings\WeddingSettings;
use Rendix2\FamilyTree\App\Forms\WeddingForm;
use Rendix2\FamilyTree\App\Presenters\BasePresenter;

/**
 * Class TownAddWeddingModal
 *
 * @package Rendix2\FamilyTree\App\Controls\Modals\Town
 */
class TownAddWeddingModal extends Control
{
    /**
     * @param int $townId
     *
     * @return void
     */
    public function handleTownAddWedding($townId)
    {
        $presenter = $this->presenter;

        $males = $this->personSettingsManager->getMalesPairs($this->translator);
        $females = $this->personSettingsManager->getFemalesPairs($this->translator);
        $towns = $this->townSettingsManager->getAllPairs();
        $addresses = $this->addressFacade->getByTownPairs($townId);

        $this['townAddWeddingForm-husbandId']->setItems($males);
        $this['townAddWeddingForm-wifeId']->setItems($females);
        $this['townAddWeddingForm-_townId']->setDefaultValue($townId);
        $this['townAddWeddingForm-townId']->setItems($towns)
            ->setDisabled()
            ->setDefaultValue($townId);
        $this['townAddWeddingForm-addressId']->setItems($addresses);

        $presenter->template->modalName = 'townAddWedding';

        $presenter->payload->showModal = true;

        $presenter->redrawControl('modal');
    }

    /**
     * @return Form
     */
    protected function createComponentTownAddWeddingForm()
    {
        $weddingSettings = new WeddingSettings();

        $formFactory = new WeddingForm($this->translator, $weddingSettings);

        $form = $formFactory->create();
        $form->addHidden('_townId');
        $form->onAnchor[] = [$this, 'townAddWeddingFormAnchor'];
        $form->onValidate[] = [$this, 'townAddWeddingFormValidate'];
        $form->onSuccess[] = [$this, 'townAddWeddingFormSuccess'];
        $form->elementPrototype->setAttribute('class', 'ajax');

        return $form;
    }

    /**
     * @return void
     */
    public function townAddWeddingFormAnchor()
    {
        $presenter = $this->presenter;

        $presenter->redrawControl('modal');
    }

    /**
     * @param Form $form
     */
    public function townAddWeddingFormValidate(Form $form)
    {
        $persons = $this->personManager->getMalesPairs($this->translator);

        $husbandControl = $form->getComponent('husbandId');
        $husbandControl->setItems($persons)
            ->validate();

        $persons = $this->personManager->getFemalesPairs($this->translator);

        $wifeControl = $form->getComponent('wifeId');
        $wifeControl->setItems($persons)
            ->validate();

        $towns = $this->townManager->getAllPairs();

        $townHiddenControl = $form->getComponent('_townId');
        $townControl = $form->getComponent('townId');
        $townControl->setItems($towns)
            ->setValue($townHiddenControl->getValue())
            ->validate();

        $addresses = $this->addressFacade->getByTownPairs($townHiddenControl->getValue());

        $addressControl = $form->getComponent('addressId');
        $addressControl->setItems($addresses)
            ->validate();

        $form->removeComponent($townHiddenControl);
    }

    /**
     * @param Form $form
     * @param ArrayHash $values
     */
    public function townAddWeddingFormSuccess(Form $form, ArrayHash $values)
    {
        $presenter = $this->presenter;

        $this->weddingManager->add($values);

        $weddings = $this->weddingFacade->getByTownIdCached($values->townId);

        $presenter->template->weddings = $weddings;

        $this->flashMessage('wedding_added', BasePresenter::FLASH_SUCCESS);

        $presenter->payload->showModal = false;

        $presenter->redrawControl('flashes');
        $presenter->redrawControl('weddings');
    }
}
