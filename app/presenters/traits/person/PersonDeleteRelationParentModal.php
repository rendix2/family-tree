<?php
/**
 *
 * Created by PhpStorm.
 * Filename: PersonDeleteRelationParentModal.php
 * User: Tomáš Babický
 * Date: 27.10.2020
 * Time: 2:18
 */

namespace Rendix2\FamilyTree\App\Presenters\Traits\Person;

use Nette\Application\UI\Form;
use Nette\Forms\Controls\SubmitButton;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Filters\PersonFilter;
use Rendix2\FamilyTree\App\Filters\RelationFilter;
use Rendix2\FamilyTree\App\Forms\DeleteModalForm;

/**
 * Trait PersonDeleteRelationParentModal
 *
 * @package Rendix2\FamilyTree\App\Presenters\Traits\Person
 */
trait PersonDeleteRelationParentModal
{
    /**
     * @param int $personId
     * @param int $relationId
     */
    public function handlePersonDeleteParentsRelation($personId, $relationId)
    {
        if ($this->isAjax()) {
            $this['personDeleteParentsRelationForm']->setDefaults(
                [
                    'relationId' => $relationId,
                    'personId' => $personId
                ]
            );

            $personFilter = new PersonFilter($this->getTranslator(), $this->getHttpRequest());
            $relationFilter = new RelationFilter($personFilter);

            $relationModalItem = $this->relationFacade->getByPrimaryKey($relationId);

            $this->template->modalName = 'personDeleteParentsRelation';
            $this->template->relationModalItem = $relationFilter($relationModalItem);

            $this->payload->showModal = true;

            $this->redrawControl('modal');
        }
    }

    /**
     * @return Form
     */
    protected function createComponentPersonDeleteParentsRelationForm()
    {
        $formFactory = new DeleteModalForm($this->getTranslator());

        $form = $formFactory->create([$this, 'personDeleteParentsRelationFormYesOnClick']);
        $form->addHidden('relationId');
        $form->addHidden('personId');

        return $form;
    }

    /**
     * @param SubmitButton $submitButton
     * @param ArrayHash $values
     */
    public function personDeleteParentsRelationFormYesOnClick(SubmitButton $submitButton, ArrayHash $values)
    {
        if ($this->isAjax()) {
            $this->relationManager->deleteByPrimaryKey($values->relationId);

            $person = $this->personFacade->getByPrimaryKeyCached($values->personId);

            $this->prepareParentsRelations($person->father, $person->mother);

            $this->payload->showModal = false;

            $this->flashMessage('relation_deleted', self::FLASH_SUCCESS);

            $this->redrawControl('flashes');
            $this->redrawControl('father_relations');
            $this->redrawControl('mother_relations');
        } else {
            $this->redirect('Person:edit', $values->personId);
        }
    }
}
