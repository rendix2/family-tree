<?php
/**
 *
 * Created by PhpStorm.
 * Filename: AddressDeleteAddressEditModal.php
 * User: Tomáš Babický
 * Date: 16.11.2020
 * Time: 21:12
 */

namespace Rendix2\FamilyTree\App\Presenters\Traits\Relation;

use Dibi\ForeignKeyConstraintViolationException;
use Nette\Application\UI\Form;
use Nette\Forms\Controls\SubmitButton;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Filters\PersonFilter;
use Rendix2\FamilyTree\App\Filters\RelationFilter;
use Rendix2\FamilyTree\App\Forms\DeleteModalForm;
use Tracy\Debugger;
use Tracy\ILogger;

/**
 * Trait RelationDeleteRelationFromEditModal
 *
 * @package Rendix2\FamilyTree\App\Presenters\Traits\Relation
 */
trait RelationDeleteRelationFromEditModal
{
    /**
     * @param int $relationId
     */
    public function handleRelationDeleteRelationFromEdit($relationId)
    {
        if ($this->isAjax()) {
            $this['relationDeleteRelationFromEditForm']->setDefaults(['relationId' => $relationId]);

            $relationModalItem = $this->relationFacade->getByPrimaryKeyCached($relationId);

            $relationFilter = $this->relationFilter;

            $this->template->modalName = 'relationDeleteRelationFromEdit';
            $this->template->relationModalItem = $relationFilter($relationModalItem);

            $this->payload->showModal = true;

            $this->redrawControl('modal');
        }
    }

    /**
     * @return Form
     */
    protected function createComponentRelationDeleteRelationFromEditForm()
    {
        $formFactory = new DeleteModalForm($this->translator);

        $form = $formFactory->create([$this, 'relationDeleteRelationFromEditFormYesOnClick'], true);
        $form->addHidden('relationId');

        return $form;
    }

    /**
     * @param SubmitButton $submitButton
     * @param ArrayHash $values
     */
    public function relationDeleteRelationFromEditFormYesOnClick(SubmitButton $submitButton, ArrayHash $values)
    {
        try {
            $this->relationManager->deleteByPrimaryKey($values->relationId);

            $this->flashMessage('relation_deleted', self::FLASH_SUCCESS);

            $this->redirect('Relation:default');
        } catch (ForeignKeyConstraintViolationException $e) {
            if ($e->getCode() === 1451) {
                $this->flashMessage('Item has some unset relations', self::FLASH_DANGER);

                $this->redrawControl('flashes');
            } else {
                Debugger::log($e, ILogger::EXCEPTION);
            }
        }
    }
}
