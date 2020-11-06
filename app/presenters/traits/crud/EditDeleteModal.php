<?php
/**
 *
 * Created by PhpStorm.
 * Filename: EditDeleteModal.php
 * User: Tomáš Babický
 * Date: 31.10.2020
 * Time: 16:00
 */

namespace Rendix2\FamilyTree\App\Presenters\Traits\CRUD;

use Nette\Application\UI\Form;
use Nette\Forms\Controls\SubmitButton;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Forms\DeleteModalForm;

trait EditDeleteModal
{
    /**
     * @param int $personId
     */
    public function handleEditDeleteItem($personId)
    {
        $this['editDeleteForm']->setDefaults(['id' => $personId]);

        $this->template->modalName = 'editDeleteItem';

        if ($this->isAjax()) {
            $this->payload->showModal = true;
            $this->redrawControl('modal');
        }
    }

    /**
     * @return Form
     */
    protected function createComponentEditDeleteForm()
    {
        $formFactory = new DeleteModalForm($this->getTranslator());
        $form = $formFactory->create($this, 'editDeleteFormOk');

        $form->addHidden('id');

        return $form;
    }

    /**
     * @param SubmitButton $submitButton
     * @param ArrayHash $values
     */
    public function editDeleteFormOk(SubmitButton $submitButton, ArrayHash $values)
    {
        $this->manager->deleteByPrimaryKey($values->id);
        $this->flashMessage('item_deleted', self::FLASH_SUCCESS);
        $this->redirect(':default');
    }
}
