<?php
/**
 *
 * Created by PhpStorm.
 * Filename: RelationDeleteRelationFromListModal.php
 * User: Tomáš Babický
 * Date: 21.02.2021
 * Time: 1:43
 */

namespace Rendix2\FamilyTree\App\Controls\Modals\Relation;

use Dibi\ForeignKeyConstraintViolationException;
use Nette\Application\UI\Form;
use Nette\Forms\Controls\SubmitButton;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Forms\DeleteModalForm;
use Tracy\Debugger;
use Tracy\ILogger;

/**
 * Class RelationDeleteRelationFromListModal
 *
 * @package Rendix2\FamilyTree\App\Controls\Modals\Relation
 */
class RelationDeleteRelationFromListModal extends \Nette\Application\UI\Control
{
    /**
     * @param int $relationId
     */
    public function handleRelationDeleteRelationFromList($relationId)
    {
        if ($this->isAjax()) {
            $this['relationDeleteRelationFromListForm']->setDefaults(['relationId' => $relationId]);

            $relationModalItem = $this->relationFacade->getByPrimaryKeyCached($relationId);

            $relationFilter = $this->relationFilter;

            $this->template->modalName = 'relationDeleteRelationFromList';
            $this->template->relationModalItem = $relationFilter($relationModalItem);

            $this->payload->showModal = true;

            $this->redrawControl('modal');
        }
    }

    /**
     * @return Form
     */
    protected function createComponentRelationDeleteRelationFromListForm()
    {
        $formFactory = new DeleteModalForm($this->translator);
        $form = $formFactory->create([$this, 'relationDeleteRelationFromListFormYesOnClick']);

        $form->addHidden('relationId');

        return $form;
    }

    /**
     * @param SubmitButton $submitButton
     * @param ArrayHash $values
     */
    public function relationDeleteRelationFromListFormYesOnClick(SubmitButton $submitButton, ArrayHash $values)
    {
        try {
            $this->relationManager->deleteByPrimaryKey($values->relationId);

            $this->flashMessage('relation_deleted', self::FLASH_SUCCESS);

            $this->redrawControl('list');
        } catch (ForeignKeyConstraintViolationException $e) {
            if ($e->getCode() === 1451) {
                $this->flashMessage('Item has some unset relations', self::FLASH_DANGER);
            } else {
                Debugger::log($e, ILogger::EXCEPTION);
            }
        } finally {
            $this->redrawControl('flashes');
        }
    }
}