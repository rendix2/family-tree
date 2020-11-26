<?php
/**
 *
 * Created by PhpStorm.
 * Filename: AddWeddingModal.php
 * User: Tomáš Babický
 * Date: 25.11.2020
 * Time: 0:20
 */

namespace Rendix2\FamilyTree\App\Presenters\Traits\Wedding;

use Nette\Application\UI\Form;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Forms\WeddingForm;

/**
 * Trait AddWeddingModal
 *
 * @package Rendix2\FamilyTree\App\Presenters\Traits\Wedding
 */
trait AddWeddingModal
{
    /**
     * @return void
     */
    public function handleAddWedding()
    {
        $males = $this->personManager->getMalesPairs($this->getTranslator());
        $females = $this->personManager->getFemalesPairs($this->getTranslator());
        $towns = $this->townManager->getAllPairs();

        $this['addWeddingForm-husbandId']->setItems($males);
        $this['addWeddingForm-wifeId']->setItems($females);
        $this['addWeddingForm-townId']->setItems($towns);

        $this->template->modalName = 'addWedding';

        $this->payload->showModal = true;

        $this->redrawControl('modal');
    }

    /**
     * @return Form
     */
    public function createComponentAddWeddingForm()
    {
        $formFactory = new WeddingForm($this->getTranslator());

        $form = $formFactory->create();
        $form->onAnchor[] = [$this, 'addWeddingFormAnchor'];
        $form->onValidate[] = [$this, 'addWeddingFormValidate'];
        $form->onSuccess[] = [$this, 'saveWeddingForm'];

        $form->elementPrototype->setAttribute('class', 'ajax');

        return $form;
    }

    /**
     * @return void
     */
    public function addWeddingFormAnchor()
    {
        $this->redrawControl('modal');
    }

    /**
     * @param Form $form
     */
    public function addWeddingFormValidate(Form $form)
    {
        $husbandControl = $form->getComponent('husbandId');

        $persons = $this->personManager->getMalesPairs($this->getTranslator());

        $husbandControl->setItems($persons);
        $husbandControl->validate();

        $wifeControl = $form->getComponent('wifeId');

        $persons = $this->personManager->getFemalesPairs($this->getTranslator());

        $wifeControl->setItems($persons);
        $wifeControl->validate();

        $townControl = $form->getComponent('townId');

        $towns = $this->townManager->getAllPairs();

        $townControl->setItems($towns);
        $townControl->validate();
    }

    /**
     * @param Form $form
     * @param ArrayHash $values
     */
    public function saveWeddingForm(Form $form, ArrayHash $values)
    {
        $this->weddingManager->add($values);

        $this->flashMessage('wedding_added', self::FLASH_SUCCESS);

        $this->payload->showModal = false;

        $this->redrawControl();
    }
}
