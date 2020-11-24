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
    public function handleDeleteRelationItem($personId, $relationId)
    {
        if ($this->isAjax()) {

            $this['deletePersonRelationForm']->setDefaults(
                [
                    'relationId' => $relationId,
                    'personId' => $personId
                ]
            );

            $personFilter = new PersonFilter($this->getTranslator(), $this->getHttpRequest());
            $relationFilter = new RelationFilter($personFilter);

            $relationModalItem = $this->relationFacade->getByPrimaryKey($relationId);

            $this->template->modalName = 'deleteRelationItem';
            $this->template->relationModalItem = $relationFilter($relationModalItem);

            $this->payload->showModal = true;

            $this->redrawControl('modal');
        }
    }

    /**
     * @return Form
     */
    protected function createComponentDeletePersonRelationForm()
    {
        $formFactory = new DeleteModalForm($this->getTranslator());
        $form = $formFactory->create($this, 'deletePersonRelationFormOk');

        $form->addHidden('relationId');
        $form->addHidden('personId');

        return $form;
    }

    /**
     * @param SubmitButton $submitButton
     * @param ArrayHash $values
     */
    public function deletePersonRelationFormOk(SubmitButton $submitButton, ArrayHash $values)
    {
        if ($this->isAjax()) {
            $this->relationManager->deleteByPrimaryKey($values->relationId);

            $this->template->modalName = 'deleteRelationItem';

            $this->payload->showModal = false;

            $this->prepareRelations($values->personId);

            $this->flashMessage('item_deleted', self::FLASH_SUCCESS);

            $this->redrawControl('flashes');
            $this->redrawControl('relation_males');
            $this->redrawControl('relation_females');
        } else {
            $this->redirect('Person:edit', $values->personId);
        }
    }
}
