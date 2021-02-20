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
use Rendix2\FamilyTree\App\Presenters\Traits\Person\PersonPrepareMethods;

/**
 * Class PersonDeleteRelationModal
 *
 * @package Rendix2\FamilyTree\App\Controls\Modals\Person
 */
class PersonDeleteRelationModal extends Control
{
    use PersonPrepareMethods;

    /**
     * @var ITranslator $translator
     */
    private $translator;

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
     * @param ITranslator $translator
     * @param RelationManager $relationManager
     * @param RelationFacade $relationFacade
     * @param RelationFilter $relationFilter
     */
    public function __construct(
        ITranslator $translator,
        RelationManager $relationManager,
        RelationFacade $relationFacade,
        RelationFilter $relationFilter
    ) {
        parent::__construct();

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
        if (!$this->presenter->isAjax()) {
            $this->presenter->redirect('Person:edit', $this->getParameter('id'));
        }

        $this['personDeleteRelationForm']->setDefaults(
            [
                'relationId' => $relationId,
                'personId' => $personId
            ]
        );

        $relationFilter = $this->relationFilter;

        $relationModalItem = $this->relationFacade->getByPrimaryKeyCached($relationId);

        $this->presenter->template->modalName = 'personDeleteRelation';
        $this->presenter->template->relationModalItem = $relationFilter($relationModalItem);

        $this->presenter->payload->showModal = true;

        $this->presenter->redrawControl('modal');
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
        if ($this->presenter->isAjax()) {
            $this->relationManager->deleteByPrimaryKey($values->relationId);

            $this->prepareRelations($values->personId);

            $this->presenter->payload->showModal = false;

            $this->presenter->flashMessage('relation_deleted', BasePresenter::FLASH_SUCCESS);

            $this->presenter->redrawControl('flashes');
            $this->presenter->redrawControl('relation_males');
            $this->presenter->redrawControl('relation_females');
        } else {
            $this->presenter->redirect('Person:edit', $values->personId);
        }
    }
}