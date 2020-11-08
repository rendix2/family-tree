<?php
/**
 *
 * Created by PhpStorm.
 * Filename: PersonAddSonModal.php
 * User: Tomáš Babický
 * Date: 04.11.2020
 * Time: 11:04
 */

namespace Rendix2\FamilyTree\App\Presenters\Traits\Person;

use Nette\Application\UI\Form;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Forms\PersonSelectForm;

/**
 * Trait PersonAddSonModal
 *
 * @package Rendix2\FamilyTree\App\Presenters\Traits\Person
 */
trait PersonAddSonModal
{
    /**
     * @param $personId
     */
    public function handleAddSon($personId)
    {
        $this->template->modalName = 'addSon';

        $persons = $this->manager->getMalesPairs($this->getTranslator());

        $this['addSonForm-selectedPersonId']->setItems($persons);
        $this['addSonForm']->setDefaults(
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
    protected function createComponentAddSonForm()
    {
        $formFactory = new PersonSelectForm($this->getTranslator());
        $form = $formFactory->create();

        $form->onSuccess[] = [$this, 'addSonFormSuccess'];
        $form->onAnchor[] = [$this, 'addSonFormAnchor'];
        $form->onValidate[] = [$this, 'addSonFormValidate'];

        return $form;
    }

    /**
     * @param Form $form
     *
     * @return void
     */
    public function addSonFormAnchor(Form $form)
    {
        $this->redrawControl('modal');
    }

    /**
     * @param Form $form
     * @param ArrayHash $values
     */
    public function addSonFormValidate(Form $form, ArrayHash $values)
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
    public function addSonFormSuccess(Form $form, ArrayHash $values)
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
            $this->redrawControl('sons');
        } else {
            $this->redirect(':edit', $personId);
        }
    }
}
