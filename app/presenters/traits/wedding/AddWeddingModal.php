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
    public function handleWeddingAddWedding()
    {
        $males = $this->personManager->getMalesPairs($this->getTranslator());
        $females = $this->personManager->getFemalesPairs($this->getTranslator());
        $towns = $this->townManager->getAllPairs();

        $this['weddingAddWeddingForm-husbandId']->setItems($males);
        $this['weddingAddWeddingForm-wifeId']->setItems($females);
        $this['weddingAddWeddingForm-townId']->setItems($towns);

        $this->template->modalName = 'weddingAddWedding';

        $this->payload->showModal = true;

        $this->redrawControl('modal');
    }

    /**
     * @return Form
     */
    protected function createComponentWeddingAddWeddingForm()
    {
        $formFactory = new WeddingForm($this->getTranslator());

        $form = $formFactory->create();
        $form->onAnchor[] = [$this, 'weddingAddWeddingFormAnchor'];
        $form->onValidate[] = [$this, 'weddingAddWeddingFormValidate'];
        $form->onSuccess[] = [$this, 'weddingAddWeddingFormSuccess'];
        $form->elementPrototype->setAttribute('class', 'ajax');

        return $form;
    }

    /**
     * @return void
     */
    public function weddingAddWeddingFormAnchor()
    {
        $this->redrawControl('modal');
    }

    /**
     * @param Form $form
     */
    public function weddingAddWeddingFormValidate(Form $form)
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
    public function weddingAddWeddingFormSuccess(Form $form, ArrayHash $values)
    {
        $this->weddingManager->add($values);

        $this->payload->showModal = false;

        $this->flashMessage('wedding_added', self::FLASH_SUCCESS);

        $this->redrawControl();
    }
}
