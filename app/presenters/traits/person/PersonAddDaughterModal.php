<?php
/**
 *
 * Created by PhpStorm.
 * Filename: PersonAddChildModal.php
 * User: Tomáš Babický
 * Date: 03.11.2020
 * Time: 17:09
 */

namespace Rendix2\FamilyTree\App\Presenters\Traits\Person;

use Nette\Application\UI\Form;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Forms\PersonSelectForm;

/**
 * Trait PersonAddDaughterModal
 *
 * @package Rendix2\FamilyTree\App\Presenters\Traits\Person
 */
trait PersonAddDaughterModal
{
    /**
     * @param $personId
     */
    public function handleAddDaughter($personId)
    {
        $this->template->modalName = 'addDaughter';

        $persons = $this->manager->getFemalesPairs($this->getTranslator());

        $this['addDaughterForm-selectedPersonId']->setItems($persons);
        $this['addDaughterForm']->setDefaults(
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
    protected function createComponentAddDaughterForm()
    {
        $formFactory = new PersonSelectForm($this->getTranslator());
        $form = $formFactory->create();

        $form->onAnchor[] = [$this, 'addDaughterFormAnchor'];
        $form->onValidate[] = [$this, 'addDaughterFormValidate'];
        $form->onSuccess[] = [$this, 'addDaughterFormSuccess'];

        return $form;
    }

    /**
     * @return void
     */
    public function addDaughterFormAnchor()
    {
        $this->redrawControl('modal');
    }

    /**
     * @param Form $form
     */
    public function addDaughterFormValidate(Form $form)
    {
        $component = $form->getComponent('selectedPersonId');

        $persons = $this->manager->getFemalesPairs($this->getTranslator());

        $component->setItems($persons);
        $component->validate();
    }

    /**
     * @param Form $form
     * @param ArrayHash $values
     */
    public function addDaughterFormSuccess(Form $form, ArrayHash $values)
    {
        $formData = $form->getHttpData();
        $personId = $this->getParameter('id');
        $selectedPersonId = $formData['selectedPersonId'];

        if ($this->isAjax()) {
            $this->payload->showModal = false;

            $person = $this->item;

            if ($person->gender === 'm') {
                $this->manager->updateByPrimaryKey($selectedPersonId, ['fatherId' => $personId]);
            } else {
                $this->manager->updateByPrimaryKey($selectedPersonId, ['motherId' => $personId]);
            }

            $this->flashMessage('item_updated', self::FLASH_SUCCESS);

            $this->redrawControl('modal');
            $this->redrawControl('flashes');
            $this->redrawControl('daughters');
        } else {
            $this->redirect(':edit', $personId);
        }
    }
}
