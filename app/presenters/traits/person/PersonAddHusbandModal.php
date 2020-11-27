<?php
/**
 *
 * Created by PhpStorm.
 * Filename: PersonAddHusbandModal.php
 * User: Tomáš Babický
 * Date: 27.11.2020
 * Time: 1:19
 */

namespace Rendix2\FamilyTree\App\Presenters\Traits\Person;


use Nette\Application\UI\Form;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Forms\WeddingForm;

/**
 * Trait PersonAddHusbandModal
 *
 * @package Rendix2\FamilyTree\App\Presenters\Traits\Person
 */
trait PersonAddHusbandModal
{
    /**
     * @return void
     */
    public function handleAddHusband($personId)
    {
        $males = $this->personManager->getMalesPairs($this->getTranslator());
        $females = $this->personManager->getFemalesPairs($this->getTranslator());
        $towns = $this->townManager->getAllPairs();

        $this['addHusbandForm-husbandId']->setItems($males);
        $this['addHusbandForm-_wifeId']->setDefaultValue($personId);
        $this['addHusbandForm-wifeId']->setItems($females)->setDisabled()->setDefaultValue($personId);
        $this['addHusbandForm-townId']->setItems($towns);

        $this->template->modalName = 'addHusband';

        $this->payload->showModal = true;

        $this->redrawControl('modal');
    }

    /**
     * @return Form
     */
    public function createComponentAddHusbandForm()
    {
        $formFactory = new WeddingForm($this->getTranslator());

        $form = $formFactory->create();
        $form->addHidden('_wifeId');
        $form->onAnchor[] = [$this, 'anchorAddHusbandForm'];
        $form->onValidate[] = [$this, 'validateAddHusbandForm'];
        $form->onSuccess[] = [$this, 'saveAddHusbandForm'];

        $form->elementPrototype->setAttribute('class', 'ajax');

        return $form;
    }

    /**
     * @return void
     */
    public function anchorAddHusbandForm()
    {
        $this->redrawControl('modal');
    }

    /**
     * @param Form $form
     */
    public function validateAddHusbandForm(Form $form)
    {
        $husbandControl = $form->getComponent('husbandId');

        $persons = $this->personManager->getMalesPairs($this->getTranslator());

        $husbandControl->setItems($persons);
        $husbandControl->validate();

        $wifeControl = $form->getComponent('wifeId');
        $wifeHiddenControl = $form->getComponent('_wifeId');

        $persons = $this->personManager->getFemalesPairs($this->getTranslator());

        $wifeControl->setItems($persons);
        $wifeControl->setValue($wifeHiddenControl->getValue());
        $wifeControl->validate();

        $townControl = $form->getComponent('townId');

        $towns = $this->townManager->getAllPairs();

        $townControl->setItems($towns);
        $townControl->validate();

        $form->removeComponent($wifeHiddenControl);
    }

    /**
     * @param Form $form
     * @param ArrayHash $values
     */
    public function saveAddHusbandForm(Form $form, ArrayHash $values)
    {
        $this->weddingManager->add($values);

        $this->flashMessage('wedding_added', self::FLASH_SUCCESS);

        $this->payload->showModal = false;

        $this->redrawControl();
    }
}
