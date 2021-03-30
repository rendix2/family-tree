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
use Rendix2\FamilyTree\App\Controls\Forms\DeleteModalForm;
use Rendix2\FamilyTree\App\Controls\Forms\Settings\DeleteModalFormSettings;
use Rendix2\FamilyTree\App\Facades\PersonFacade;
use Rendix2\FamilyTree\App\Facades\RelationFacade;
use Rendix2\FamilyTree\App\Filters\RelationFilter;

use Rendix2\FamilyTree\App\Managers\RelationManager;
use Rendix2\FamilyTree\App\Presenters\BasePresenter;
use Rendix2\FamilyTree\App\Services\PersonUpdateService;

/**
 * Class PersonDeleteRelationParentModal
 *
 * @package Rendix2\FamilyTree\App\Controls\Modals\Person
 */
class PersonDeleteRelationParentModal extends Control
{
    /**
     * @var DeleteModalForm $deleteModalForm
     */
    private $deleteModalForm;

    /**
     * @var ITranslator $translator
     */
    private $translator;

    /**
     * @var PersonFacade $personFacade
     */
    private $personFacade;

    /**
     * @var PersonUpdateService $personUpdateService
     */
    private $personUpdateService;

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
     * @param PersonUpdateService $personUpdateService
     * @param RelationManager $relationManager
     * @param RelationFacade $relationFacade
     * @param RelationFilter $relationFilter
     */
    public function __construct(
        PersonFacade $personFacade,
        PersonUpdateService $personUpdateService,

        DeleteModalForm $deleteModalForm,

        RelationManager $relationManager,
        RelationFacade $relationFacade,
        RelationFilter $relationFilter
    ) {
        parent::__construct();

        $this->deleteModalForm = $deleteModalForm;

        $this->translator = $translator;
        $this->personFacade = $personFacade;
        $this->personUpdateService = $personUpdateService;
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

        if (!$presenter->isAjax()) {
            $presenter->redirect('Person:edit', $presenter->getParameter('id'));
        }

        $this['personDeleteParentsRelationForm']->setDefaults(
            [
                'relationId' => $relationId,
                'personId' => $personId
            ]
        );

        $relationFilter = $this->relationFilter;

        $relationModalItem = $this->relationFacade->getByPrimaryKeyCached($relationId);

        $presenter->template->modalName = 'personDeleteParentsRelation';
        $presenter->template->relationModalItem = $relationFilter($relationModalItem);

        $presenter->payload->showModal = true;

        $presenter->redrawControl('modal');
    }

    /**
     * @return Form
     */
    protected function createComponentPersonDeleteParentsRelationForm()
    {
        $deleteModalFormSettings = new DeleteModalFormSettings();
        $deleteModalFormSettings->callBack = [$this, 'personDeleteParentsRelationFormYesOnClick'];

        $form = $this->deleteModalForm->create($deleteModalFormSettings);

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

        if (!$presenter->isAjax()) {
            $presenter->redirect('Person:edit', $presenter->getParameter('id'));
        }

        $this->relationManager->deleteByPrimaryKey($values->relationId);

        $person = $this->personFacade->getByPrimaryKeyCached($values->personId);

        $this->personUpdateService->prepareParentsRelations($presenter, $person->father, $person->mother);

        $presenter->payload->showModal = false;

        $presenter->flashMessage('relation_deleted', BasePresenter::FLASH_SUCCESS);

        $presenter->redrawControl('flashes');
        $presenter->redrawControl('father_relations');
        $presenter->redrawControl('mother_relations');
    }
}