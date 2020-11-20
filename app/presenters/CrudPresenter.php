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

use Dibi\Row;
use Nette\Application\UI\Form;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Presenters\Traits\CRUD\EditDeleteModal;
use Rendix2\FamilyTree\App\Presenters\Traits\CRUD\ListDeleteModal;

/**
 * Trait CrudPresenter
 *
 * @package Rendix2\FamilyTree\App\Presenters
 */
trait CrudPresenter
{
    use EditDeleteModal;
    use ListDeleteModal;

    /**
     * @var Row $item
     */
    private $item;

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

            $this['form']->setDefaults((array)$item);
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

        $this->redirect(':edit', $id);
    }
}
