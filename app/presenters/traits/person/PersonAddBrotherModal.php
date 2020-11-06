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

trait PersonAddBrotherModal
{
    /**
     * @param $personId
     */
    public function handleAddBrother($personId)
    {
        $this->template->modalName = 'addBrother';

        $persons = $this->manager->getMalesPairs($this->getTranslator());

        $this['addBrotherForm-selectedPersonId']->setItems($persons);
        $this['addBrotherForm']->setDefaults(
            [
                'personId' => $personId,
            ]
        );

        if ($this->isAjax()) {
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

        $persons = $this->manager->getMalesPairs($this->getTranslator());

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

            $this->payload->showModal = false;

            $person = $this->item;

            $this->manager->updateByPrimaryKey($selectedPersonId,
                [
                    'fatherId' => $person->fatherId,
                    'motherId' => $person->motherId
                ]
            );

            $this->flashMessage('item_updated', self::FLASH_SUCCESS);

            $this->redrawControl('modal');
            $this->redrawControl('flashes');
            $this->redrawControl('brothers');
        } else {
            $this->redirect(':edit', $this->getParameter('id'));
        }
    }
}
