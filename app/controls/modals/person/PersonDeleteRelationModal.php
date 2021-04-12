<?php
/**
 *
 * Created by PhpStorm.
 * Filename: PersonDeleteRelationModal.php
 * User: Tomáš Babický
 * Date: 20.02.2021
 * Time: 13:14
 */

namespace Rendix2\FamilyTree\App\Controls\Modals\Person;

use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Nette\Forms\Controls\SubmitButton;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Controls\Forms\DeleteModalForm;
use Rendix2\FamilyTree\App\Controls\Forms\Settings\DeleteModalFormSettings;
use Rendix2\FamilyTree\App\Model\Facades\RelationFacade;
use Rendix2\FamilyTree\App\Filters\RelationFilter;
use Rendix2\FamilyTree\App\Model\Managers\RelationManager;
use Rendix2\FamilyTree\App\Presenters\BasePresenter;
use Rendix2\FamilyTree\App\Services\PersonUpdateService;

/**
 * Class PersonDeleteRelationModal
 *
 * @package Rendix2\FamilyTree\App\Controls\Modals\Person
 */
class PersonDeleteRelationModal extends Control
{
    /**
     * @var DeleteModalForm $deleteModalForm
     */
    private $deleteModalForm;

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
     * PersonDeleteRelationModal constructor.
     *
     * @param PersonUpdateService $personUpdateService
     * @param RelationManager     $relationContainer
     * @param RelationFacade      $relationFacade
     * @param RelationFilter      $relationFilter
     * @param DeleteModalForm     $deleteModalForm
     */
    public function __construct(
        PersonUpdateService $personUpdateService,
        RelationManager $relationContainer,
        RelationFacade $relationFacade,
        RelationFilter $relationFilter,
        DeleteModalForm $deleteModalForm
    ) {
        parent::__construct();

        $this->deleteModalForm = $deleteModalForm;

        $this->personUpdateService = $personUpdateService;
        $this->relationManager = $relationContainer;
        $this->relationFacade = $relationFacade;
        $this->relationFilter = $relationFilter;
    }

    /**
     * @return void
     */
    public function render()
    {
        $this['personDeleteRelationForm']->render();
    }

    /**
     * @param int $personId
     * @param int $relationId
     */
    public function handlePersonDeleteRelation($personId, $relationId)
    {
        $presenter = $this->presenter;

        if (!$presenter->isAjax()) {
            $presenter->redirect('Person:edit', $presenter->getParameter('id'));
        }

        $this['personDeleteRelationForm']->setDefaults(
            [
                'relationId' => $relationId,
                'personId' => $personId
            ]
        );

        $relationFilter = $this->relationFilter;

        $relationModalItem = $this->relationFacade->select()->getCachedManager()->getByPrimaryKey($relationId);

        $presenter->template->modalName = 'personDeleteRelation';
        $presenter->template->relationModalItem = $relationFilter($relationModalItem);

        $presenter->payload->showModal = true;

        $presenter->redrawControl('modal');
    }

    /**
     * @return Form
     */
    protected function createComponentPersonDeleteRelationForm()
    {
        $deleteModalFormSettings = new DeleteModalFormSettings();
        $deleteModalFormSettings->callBack = [$this, 'personDeleteRelationFormYesOnClick'];

        $form = $this->deleteModalForm->create($deleteModalFormSettings);

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
        $presenter = $this->presenter;

        if (!$presenter->isAjax()) {
            $presenter->redirect('Person:edit', $presenter->getParameter('id'));
        }

        $this->relationManager->delete()->deleteByPrimaryKey($values->relationId);

        $this->personUpdateService->prepareRelations($presenter, $values->personId);

        $presenter->payload->showModal = false;

        $presenter->flashMessage('relation_deleted', BasePresenter::FLASH_SUCCESS);

        $presenter->redrawControl('flashes');
        $presenter->redrawControl('relation_males');
        $presenter->redrawControl('relation_females');
    }
}
