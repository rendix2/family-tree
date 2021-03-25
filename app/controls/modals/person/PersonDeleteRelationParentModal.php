<?php
/**
 *
 * Created by PhpStorm.
 * Filename: PersonDeleteRelationParentModal.php
 * User: Tomáš Babický
 * Date: 20.02.2021
 * Time: 13:15
 */

namespace Rendix2\FamilyTree\App\Controls\Modals\Person;

use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Nette\Forms\Controls\SubmitButton;
use Nette\Localization\ITranslator;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Facades\PersonFacade;
use Rendix2\FamilyTree\App\Facades\RelationFacade;
use Rendix2\FamilyTree\App\Filters\RelationFilter;
use Rendix2\FamilyTree\App\Forms\DeleteModalForm;
use Rendix2\FamilyTree\App\Managers\RelationManager;
use Rendix2\FamilyTree\App\Presenters\BasePresenter;
use Rendix2\FamilyTree\App\Presenters\Traits\Person\PersonPrepareMethods;

/**
 * Class PersonDeleteRelationParentModal
 *
 * @package Rendix2\FamilyTree\App\Controls\Modals\Person
 */
class PersonDeleteRelationParentModal extends Control
{
    use PersonPrepareMethods;

    /**
     * @var ITranslator $translator
     */
    private $translator;

    /**
     * @var PersonFacade $personFacade
     */
    private $personFacade;

    /**
     * @var RelationManager $relationManager
     */
    private $relationManager;

    /**
     * @var RelationFacade $relationFacade
     */
    private $relationFacade;

    /**
     * @var RelationFilter $relationFilter
     */
    private $relationFilter;

    /**
     * PersonDeleteRelationParentModal constructor.
     *
     * @param ITranslator $translator
     * @param PersonFacade $personFacade
     * @param RelationManager $relationManager
     * @param RelationFacade $relationFacade
     * @param RelationFilter $relationFilter
     */
    public function __construct(
        ITranslator $translator,
        PersonFacade $personFacade,
        RelationManager $relationManager,
        RelationFacade $relationFacade,
        RelationFilter $relationFilter
    ) {
        parent::__construct();

        $this->translator = $translator;
        $this->personFacade = $personFacade;
        $this->relationManager = $relationManager;
        $this->relationFacade = $relationFacade;
        $this->relationFilter = $relationFilter;
    }

    /**
     * @return void
     */
    public function render()
    {
        $this['personDeleteParentsRelationForm']->render();
    }

    /**
     * @param int $personId
     * @param int $relationId
     */
    public function handlePersonDeleteParentsRelation($personId, $relationId)
    {
        $presenter = $this->presenter;

        if (!$this->presenter->isAjax()) {
            $this->presenter->redirect('Person:edit', $this->getParameter('id'));
        }

        $this['personDeleteParentsRelationForm']->setDefaults(
            [
                'relationId' => $relationId,
                'personId' => $personId
            ]
        );

        $relationFilter = $this->relationFilter;

        $relationModalItem = $this->relationFacade->getByPrimaryKeyCached($relationId);

        $this->presenter->template->modalName = 'personDeleteParentsRelation';
        $this->presenter->template->relationModalItem = $relationFilter($relationModalItem);

        $this->presenter->payload->showModal = true;

        $this->presenter->redrawControl('modal');
    }

    /**
     * @return Form
     */
    protected function createComponentPersonDeleteParentsRelationForm()
    {
        $formFactory = new DeleteModalForm($this->translator);

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
        $presenter = $this->presenter;

        if ($this->presenter->isAjax()) {
            $this->relationManager->deleteByPrimaryKey($values->relationId);

            $person = $this->personFacade->getByPrimaryKeyCached($values->personId);

            $this->prepareParentsRelations($person->father, $person->mother);

            $this->presenter->payload->showModal = false;

            $this->presenter->flashMessage('relation_deleted', BasePresenter::FLASH_SUCCESS);

            $this->presenter->redrawControl('flashes');
            $this->presenter->redrawControl('father_relations');
            $this->presenter->redrawControl('mother_relations');
        } else {
            $this->presenter->redirect('Person:edit', $values->personId);
        }
    }
}