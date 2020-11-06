<?php

namespace Rendix2\FamilyTree\App\Presenters\Traits\Name;

use Nette\Application\UI\Form;
use Nette\Forms\Controls\SubmitButton;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Forms\DeleteModalForm;

/**
 *
 * Created by PhpStorm.
 * Filename: NamePersonNameDeleteModal.php
 * User: Tomáš Babický
 * Date: 29.10.2020
 * Time: 15:52
 */

trait NamePersonNameDeleteModal
{
    /**
     * @param int $nameId
     */
    public function handleDeletePersonNameItem($nameId)
    {
        $this->template->modalName = 'deletePersonNameItem';

        $this['deletePersonNameForm']->setDefaults(
            [
                'nameId' => $nameId,
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
    protected function createComponentDeletePersonNameForm()
    {
        $formFactory = new DeleteModalForm($this->getTranslator());
        $form = $formFactory->create($this, 'deletePersonNameFormOk');

        $form->addHidden('nameId');

        return $form;
    }

    /**
     * @param SubmitButton $submitButton
     * @param ArrayHash $values
     */
    public function deletePersonNameFormOk(SubmitButton $submitButton, ArrayHash $values)
    {
        if ($this->isAjax()) {
            $this->manager->deleteByPrimaryKey($values->nameId);

            $this->template->modalName = 'deletePersonNameItem';

            $this->payload->showModal = false;

            $this->flashMessage('item_deleted', self::FLASH_SUCCESS);

            $this->redrawControl('modal');
            $this->redrawControl('flashes');
            $this->redrawControl('names');
        } else {
            $this->redirect(':edit', $values->nameId);
        }
    }
}