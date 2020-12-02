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
     * @param int $personId
     *
     * @return void
     */
    public function handleAddHusband($personId)
    {
        $males = $this->personManager->getMalesPairs($this->getTranslator());
        $females = $this->personManager->getFemalesPairs($this->getTranslator());
        $towns = $this->townManager->getAllPairs();
        $addresses = $this->addressFacade->getPairs();

        $this['addHusbandForm-husbandId']->setItems($males);
        $this['addHusbandForm-_wifeId']->setDefaultValue($personId);
        $this['addHusbandForm-wifeId']->setItems($females)->setDisabled()->setDefaultValue($personId);
        $this['addHusbandForm-townId']->setItems($towns);
        $this['addHusbandForm-addressId']->setItems($addresses);

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
        $males = $this->personManager->getMalesPairs($this->getTranslator());

        $husbandControl = $form->getComponent('husbandId');
        $husbandControl->setItems($males)
            ->validate();

        $females = $this->personManager->getFemalesPairs($this->getTranslator());

        $wifeHiddenControl = $form->getComponent('_wifeId');

        $wifeControl = $form->getComponent('wifeId');
        $wifeControl->setItems($females);
        $wifeControl->setValue($wifeHiddenControl->getValue())
            ->validate();

        $towns = $this->townManager->getAllPairs();

        $townControl = $form->getComponent('townId');
        $townControl->setItems($towns)
            ->validate();

        $addresses = $this->addressFacade->getPairs();

        $townControl = $form->getComponent('addressId');
        $townControl->setItems($addresses)
            ->validate();

        $form->removeComponent($wifeHiddenControl);
    }

    /**
     * @param Form $form
     * @param ArrayHash $values
     */
    public function saveAddHusbandForm(Form $form, ArrayHash $values)
    {
        $this->weddingManager->add($values);

        $this->prepareWeddings($values->wifeId);

        $this->payload->showModal = false;

        $this->flashMessage('wedding_added', self::FLASH_SUCCESS);

        $this->redrawControl('husbands');
        $this->redrawControl('flashes');
    }
}
