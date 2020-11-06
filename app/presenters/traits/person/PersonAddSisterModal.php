<?php
/**
 *
 * Created by PhpStorm.
 * Filename: PersonAddSisterModal.php
 * User: Tomáš Babický
 * Date: 05.11.2020
 * Time: 15:18
 */

namespace Rendix2\FamilyTree\App\Presenters\Traits\Person;


use Nette\Application\UI\Form;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Forms\PersonSelectForm;

trait PersonAddSisterModal
{
    /**
     * @param $personId
     */
    public function handleAddSister($personId)
    {
        $this->template->modalName = 'addSister';

        $persons = $this->manager->getFemalesPairs($this->getTranslator());

        $this['addSisterForm-selectedPersonId']->setItems($persons);
        $this['addSisterForm']->setDefaults(
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
    protected function createComponentAddSisterForm()
    {
        $formFactory = new PersonSelectForm($this->getTranslator());
        $form = $formFactory->create();

        $form->onAnchor[] = [$this, 'addSisterFormAnchor'];
        $form->onValidate[] = [$this, 'addSisterFormValidate'];
        $form->onSuccess[] = [$this, 'addSisterFormSuccess'];

        return $form;
    }

    /**
     * @return void
     */
    public function addSisterFormAnchor()
    {
        $this->redrawControl('modal');
    }

    /**
     * @param Form $form
     */
    public function addSisterFormValidate(Form $form)
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
    public function addSisterFormSuccess(Form $form, ArrayHash $values)
    {
        $formData = $form->getHttpData();
        $personId = $this->getParameter('id');
        $selectedPersonId = $formData['selectedPersonId'];

        if ($this->isAjax()) {
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
            $this->redrawControl('sisters');
        } else {
            $this->redirect(':edit', $personId);
        }
    }    
}
