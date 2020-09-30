<?php
/**
 *
 * Created by PhpStorm.
 * Filename: CrudPresenter.php
 * User: Tomáš Babický
 * Date: 29.08.2020
 * Time: 1:34
 */

namespace Rendix2\FamilyTree\App\Presenters;

use Nette\Application\UI\Form;
use Nette\Forms\Controls\SubmitButton;
use Nette\Utils\ArrayHash;

/**
 * Trait CrudPresenter
 *
 * @package Rendix2\FamilyTree\App\Presenters
 */
trait CrudPresenter
{
    private $item;

    /**
     * @param int $id
     */
    public function actionDelete($id)
    {
        $this->manager->deleteByPrimaryKey($id);
        $this->flashMessage('item_deleted', self::FLASH_SUCCESS);
        $this->redirect(':default');
    }

    /**
     * @param int|null $id
     */
    public function actionEdit($id = null)
    {
        if ($id !== null) {
            $this->item = $item = $this->manager->getByPrimaryKey($id);

            if (!$item) {
                $this->error('Item not found.');
            }

            $this['form']->setDefaults($item);
        }
    }

    /**
     * @param Form $form
     * @param ArrayHash $values
     */
    public function saveForm(Form $form, ArrayHash $values)
    {
        $id = $this->getParameter('id');

        if ($id) {
            $this->manager->updateByPrimaryKey($id, $values);
            $this->flashMessage('item_updated', self::FLASH_SUCCESS);
        } else {
            $id = $this->manager->add($values);
            $this->flashMessage('item_added', self::FLASH_SUCCESS);
        }

        $this->redirect(':default');
    }


    /**
     * @param $name
     * @return Form
     */
    protected function createComponentDeleteForm($name)
    {
        $form = new Form($this, $name);

        $form->setTranslator($this->getTranslator());

        $form->addProtection();

        $form->addHidden('id');
        $form->addSubmit('yes','modal_delete')
            ->setAttribute('class', 'btn btn-danger')
            ->onClick[] = [$this, 'deleteFormOk'];
        $form->addSubmit('no','modal_storno')
            ->setAttribute('class', 'btn btn-primary')
            ->setAttribute('data-dismiss', 'modal')
            ->setAttribute('aria-label', 'Close');

        return $form;
    }

    /**
     * @param SubmitButton $submitButton
     * @param ArrayHash $values
     */
    public function deleteFormOk(SubmitButton $submitButton, ArrayHash $values)
    {
        $this->manager->deleteByPrimaryKey($values->id);
        $this->flashMessage('item_deleted', self::FLASH_SUCCESS);
        $this->redirect(':default');
    }

    /**
     * @param int $id
     */
    public function handleDeleteItem($id)
    {
        $this['deleteForm']->setDefaults(['id' => $id]);

        if ($this->isAjax()) {
            $this->payload->isModal = true;
            $this->redrawControl('modal');
        }
    }
}
