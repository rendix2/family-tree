<?php
/**
 *
 * Created by PhpStorm.
 * Filename: PersonAddBrotherModal.php
 * User: Tomáš Babický
 * Date: 05.11.2020
 * Time: 15:18
 */

namespace Rendix2\FamilyTree\App\Presenters\Traits\Person;

use Nette\Application\UI\Form;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Forms\PersonSelectForm;

/**
 * Trait PersonAddBrotherModal
 *
 * @package Rendix2\FamilyTree\App\Presenters\Traits\Person
 */
trait PersonAddBrotherModal
{
    /**
     * @param int$personId
     */
    public function handleAddBrother($personId)
    {
        if ($this->isAjax()) {
            $persons = $this->personManager->getMalesPairs($this->getTranslator());

            $this['addBrotherForm-selectedPersonId']->setItems($persons);
            $this['addBrotherForm']->setDefaults(
                [
                    'personId' => $personId,
                ]
            );

            $this->template->modalName = 'addBrother';

            $this->payload->showModal = true;

            $this->redrawControl('modal');
        }
    }

    /**
     * @return Form
     */
    protected function createComponentAddBrotherForm()
    {
        $formFactory = new PersonSelectForm($this->getTranslator());

        $form = $formFactory->create();
        $form->onSuccess[] = [$this, 'addBrotherFormSuccess'];
        $form->onAnchor[] = [$this, 'addBrotherFormAnchor'];
        $form->onValidate[] = [$this, 'addBrotherFormValidate'];

        return $form;
    }

    /**
     * @param Form $form
     *
     * @return void
     */
    public function addBrotherFormAnchor(Form $form)
    {
        $this->redrawControl('modal');
    }

    /**
     * @param Form $form
     * @param ArrayHash $values
     */
    public function addBrotherFormValidate(Form $form, ArrayHash $values)
    {
        $component = $form->getComponent('selectedPersonId');

        $persons = $this->personManager->getMalesPairs($this->getTranslator());

        $component->setItems($persons);
        $component->validate();
    }

    /**
     * @param Form $form
     * @param ArrayHash $values
     */
    public function addBrotherFormSuccess(Form $form, ArrayHash $values)
    {
        if ($this->isAjax()) {
            $formData = $form->getHttpData();
            $selectedPersonId = $formData['selectedPersonId'];

            $person = $this->personFacade->getByPrimaryKey($values->personId);

            $this->personManager->updateByPrimaryKey($selectedPersonId,
                [
                    'fatherId' => $person->father->id,
                    'motherId' => $person->mother->id
                ]
            );

            $this->payload->showModal = false;

            $this->flashMessage('item_updated', self::FLASH_SUCCESS);

            $this->redrawControl('flashes');
            $this->redrawControl('brothers');
        } else {
            $this->redirect('Person:edit', $this->getParameter('id'));
        }
    }
}
