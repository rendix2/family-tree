<?php
/**
 *
 * Created by PhpStorm.
 * Filename: ListDeleteModal.php
 * User: Tomáš Babický
 * Date: 31.10.2020
 * Time: 16:02
 */

namespace Rendix2\FamilyTree\App\Presenters\Traits\CRUD;

use Nette\Application\UI\Form;
use Nette\Forms\Controls\SubmitButton;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Forms\DeleteModalForm;

/**
 * Trait ListDeleteModal
 *
 * @package Rendix2\FamilyTree\App\Presenters\Traits\CRUD
 */
trait ListDeleteModal
{
    /**
     * @param int $id
     */
    public function handleListDeleteItem($id)
    {
        $this['listDeleteForm']->setDefaults(['id' => $id]);

        $this->template->modalName = 'listDeleteItem';

        if ($this->isAjax()) {
            $this->payload->showModal = true;
            $this->redrawControl('modal');
        }
    }

    /**
     * @return Form
     */
    protected function createComponentListDeleteForm()
    {
        $formFactory = new DeleteModalForm($this->getTranslator());
        $form = $formFactory->create($this, 'listDeleteFormOk');

        $form->addHidden('id');

        return $form;
    }

    /**
     * @param SubmitButton $submitButton
     * @param ArrayHash $values
     */
    public function listDeleteFormOk(SubmitButton $submitButton, ArrayHash $values)
    {
        $this->manager->deleteByPrimaryKey($values->id);

        $this->flashMessage('item_deleted', self::FLASH_SUCCESS);

        $this->redrawControl('modal');
        $this->redrawControl('flashes');
        $this->redrawControl('list');
    }
}
