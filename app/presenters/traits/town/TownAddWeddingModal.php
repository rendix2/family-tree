<?php
/**
 *
 * Created by PhpStorm.
 * Filename: TownAddModalWedding.php
 * User: Tomáš Babický
 * Date: 02.12.2020
 * Time: 1:58
 */

namespace Rendix2\FamilyTree\App\Presenters\Traits\Town;

use Nette\Application\UI\Form;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Forms\Settings\WeddingSettings;
use Rendix2\FamilyTree\App\Forms\WeddingForm;

/**
 * Trait TownAddWeddingModal
 *
 * @package Rendix2\FamilyTree\App\Presenters\Traits\Town
 */
trait TownAddWeddingModal
{
    /**
     * @param int $townId
     *
     * @return void
     */
    public function handleTownAddWedding($townId)
    {
        $males = $this->personManager->getMalesPairs($this->getTranslator());
        $females = $this->personManager->getFemalesPairs($this->getTranslator());
        $towns = $this->townManager->getAllPairs();
        $addresses = $this->addressFacade->getByTownPairs($townId);

        $this['townAddWeddingForm-husbandId']->setItems($males);
        $this['townAddWeddingForm-wifeId']->setItems($females);
        $this['townAddWeddingForm-_townId']->setDefaultValue($townId);
        $this['townAddWeddingForm-townId']->setItems($towns)
            ->setDisabled()
            ->setDefaultValue($townId);
        $this['townAddWeddingForm-addressId']->setItems($addresses);

        $this->template->modalName = 'townAddWedding';

        $this->payload->showModal = true;

        $this->redrawControl('modal');
    }

    /**
     * @return Form
     */
    protected function createComponentTownAddWeddingForm()
    {
        $weddingSettings = new WeddingSettings();

        $formFactory = new WeddingForm($this->getTranslator(), $weddingSettings);

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
        $this->redrawControl('modal');
    }

    /**
     * @param Form $form
     */
    public function townAddWeddingFormValidate(Form $form)
    {
        $persons = $this->personManager->getMalesPairs($this->getTranslator());

        $husbandControl = $form->getComponent('husbandId');
        $husbandControl->setItems($persons)
            ->validate();

        $persons = $this->personManager->getFemalesPairs($this->getTranslator());

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
        $this->weddingManager->add($values);

        $weddings = $this->weddingFacade->getByTownIdCached($values->townId);

        $this->template->weddings = $weddings;

        $this->flashMessage('wedding_added', self::FLASH_SUCCESS);

        $this->payload->showModal = false;

        $this->redrawControl('flashes');
        $this->redrawControl('weddings');
    }
}
