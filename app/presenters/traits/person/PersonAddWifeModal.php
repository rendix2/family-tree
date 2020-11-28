<?php
/**
 *
 * Created by PhpStorm.
 * Filename: PersonAddWifeModal.php
 * User: Tomáš Babický
 * Date: 27.11.2020
 * Time: 1:19
 */

namespace Rendix2\FamilyTree\App\Presenters\Traits\Person;


use Nette\Application\UI\Form;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Forms\WeddingForm;

trait PersonAddWifeModal
{
    /**
     * @param int $personId
     *
     * @return void
     */
    public function handleAddWife($personId)
    {
        $males = $this->personManager->getMalesPairs($this->getTranslator());
        $females = $this->personManager->getFemalesPairs($this->getTranslator());
        $towns = $this->townManager->getAllPairs();
        $addresses = $this->addressFacade->getPairs();

        $this['addWifeForm-_husbandId']->setDefaultValue($personId);
        $this['addWifeForm-husbandId']->setItems($males)->setDisabled()->setDefaultValue($personId);
        $this['addWifeForm-wifeId']->setItems($females);
        $this['addWifeForm-townId']->setItems($towns);
        $this['addWifeForm-addressId']->setItems($addresses);

        $this->template->modalName = 'addWife';

        $this->payload->showModal = true;

        $this->redrawControl('modal');
    }

    /**
     * @return Form
     */
    public function createComponentAddWifeForm()
    {
        $formFactory = new WeddingForm($this->getTranslator());

        $form = $formFactory->create();
        $form->addHidden('_husbandId');
        $form->onAnchor[] = [$this, 'anchorAddWifeForm'];
        $form->onValidate[] = [$this, 'validateAddWifeForm'];
        $form->onSuccess[] = [$this, 'saveAddWifeForm'];

        $form->elementPrototype->setAttribute('class', 'ajax');

        return $form;
    }

    /**
     * @return void
     */
    public function anchorAddWifeForm()
    {
        $this->redrawControl('modal');
    }

    /**
     * @param Form $form
     */
    public function validateAddWifeForm(Form $form)
    {
        $persons = $this->personManager->getMalesPairs($this->getTranslator());

        $husbandHiddenControl = $form->getComponent('_husbandId');

        $husbandControl = $form->getComponent('husbandId');
        $husbandControl->setItems($persons);
        $husbandControl->setValue($husbandHiddenControl->getValue());
        $husbandControl->validate();

        $persons = $this->personManager->getFemalesPairs($this->getTranslator());

        $wifeControl = $form->getComponent('wifeId');
        $wifeControl->setItems($persons);
        $wifeControl->validate();

        $towns = $this->townManager->getAllPairs();

        $townControl = $form->getComponent('townId');
        $townControl->setItems($towns);
        $townControl->validate();

        $addresses = $this->addressFacade->getPairs();

        $addressControl = $form->getComponent('addressId');
        $addressControl->setItems($addresses);
        $addressControl->validate();

        $form->removeComponent($husbandHiddenControl);
    }

    /**
     * @param Form $form
     * @param ArrayHash $values
     */
    public function saveAddWifeForm(Form $form, ArrayHash $values)
    {
        $this->weddingManager->add($values);

        $this->prepareWeddings($values->husbandId);

        $this->payload->showModal = false;

        $this->flashMessage('wedding_added', self::FLASH_SUCCESS);

        $this->redrawControl('husbands');
        $this->redrawControl('flashes');
    }
}
