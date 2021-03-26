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
use Nette\Localization\ITranslator;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Facades\RelationFacade;
use Rendix2\FamilyTree\App\Filters\RelationFilter;
use Rendix2\FamilyTree\App\Forms\DeleteModalForm;
use Rendix2\FamilyTree\App\Managers\RelationManager;
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
     * @var ITranslator $translator
     */
    private $translator;

    /**
     * PersonDeleteRelationModal constructor.
     *
     * @param ITranslator $translator
     * @param RelationManager $relationManager
     * @param RelationFacade $relationFacade
     * @param RelationFilter $relationFilter
     */
    public function __construct(
        PersonUpdateService $personUpdateService,
        ITranslator $translator,
        RelationManager $relationManager,
        RelationFacade $relationFacade,
        RelationFilter $relationFilter
    ) {
        parent::__construct();

        $this->personUpdateService = $personUpdateService;
        $this->translator = $translator;
        $this->relationManager = $relationManager;
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

        $relationModalItem = $this->relationFacade->getByPrimaryKeyCached($relationId);

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
        $presenter = $this->presenter;

        if ($presenter->isAjax()) {
            $this->relationManager->deleteByPrimaryKey($values->relationId);

            $this->personUpdateService->prepareRelations($presenter, $values->personId);

            $presenter->payload->showModal = false;

            $presenter->flashMessage('relation_deleted', BasePresenter::FLASH_SUCCESS);

            $presenter->redrawControl('flashes');
            $presenter->redrawControl('relation_males');
            $presenter->redrawControl('relation_females');
        } else {
            $presenter->redirect('Person:edit', $values->personId);
        }
    }
}
