<?php
/**
 *
 * Created by PhpStorm.
 * Filename: PersonPartnerMaleDeleteModal.php
 * User: Tomáš Babický
 * Date: 26.10.2020
 * Time: 17:29
 */

namespace Rendix2\FamilyTree\App\Presenters\Traits\Person;

use Nette\Application\UI\Form;
use Nette\Forms\Controls\SubmitButton;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Filters\PersonFilter;
use Rendix2\FamilyTree\App\Filters\RelationFilter;
use Rendix2\FamilyTree\App\Forms\DeleteModalForm;

/**
 * Trait PersonDeleteRelationModal
 *
 * @package Rendix2\FamilyTree\App\Presenters\Traits\Person
 */
trait PersonDeleteRelationModal
{
    /**
     * @param int $personId
     * @param int $relationId
     */
    public function handlePersonDeleteRelation($personId, $relationId)
    {
        if (!$this->isAjax()) {
            $this->redirect('Person:edit', $this->getParameter('id'));
        }

        if ($this->isAjax()) {

            $this['personDeleteRelationForm']->setDefaults(
                [
                    'relationId' => $relationId,
                    'personId' => $personId
                ]
            );

            $relationFilter = $this->relationFilter;

            $relationModalItem = $this->relationFacade->getByPrimaryKey($relationId);

            $this->template->modalName = 'personDeleteRelation';
            $this->template->relationModalItem = $relationFilter($relationModalItem);

            $this->payload->showModal = true;

            $this->redrawControl('modal');
        }
    }

    /**
     * @return Form
     */
    protected function createComponentPersonDeleteRelationForm()
    {
        $formFactory = new DeleteModalForm($this->translator);
        $form = $formFactory->create([$this, 'personDeleteRelationFormYesOnClick']);

        $form->addHidden('relationId');
        $form->addHidden('personId');

        return $form;
    }

    /**
     * @param SubmitButton $submitButton
     * @param ArrayHash $values
     */
    public function personDeleteRelationFormYesOnClick(SubmitButton $submitButton, ArrayHash $values)
    {
        if ($this->isAjax()) {
            $this->relationManager->deleteByPrimaryKey($values->relationId);

            $this->prepareRelations($values->personId);

            $this->payload->showModal = false;

            $this->flashMessage('relation_deleted', self::FLASH_SUCCESS);

            $this->redrawControl('flashes');
            $this->redrawControl('relation_males');
            $this->redrawControl('relation_females');
        } else {
            $this->redirect('Person:edit', $values->personId);
        }
    }
}
