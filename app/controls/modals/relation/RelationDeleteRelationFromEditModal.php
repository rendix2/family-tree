<?php
/**
 *
 * Created by PhpStorm.
 * Filename: RelationDeleteRelationFromEditModalFactory.php
 * User: Tomáš Babický
 * Date: 21.02.2021
 * Time: 1:43
 */

namespace Rendix2\FamilyTree\App\Controls\Modals\Relation;

use Dibi\ForeignKeyConstraintViolationException;
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
use Tracy\Debugger;
use Tracy\ILogger;

/**
 * Class RelationDeleteRelationFromEditModalFactory
 *
 * @package Rendix2\FamilyTree\App\Controls\Modals\Relation
 */
class RelationDeleteRelationFromEditModal extends Control
{
    /**
     * @var RelationFacade $relationFacade
     */
    private $relationFacade;

    /**
     * @var RelationFilter $relationFilter
     */
    private $relationFilter;

    /**
     * @var DeleteModalForm $deleteModalForm
     */
    private $deleteModalForm;

    /**
     * @var RelationManager $relationManager
     */
    private $relationManager;

    /**
     * RelationDeleteRelationFromEditModal constructor.
     *
     * @param RelationFacade $relationFacade
     * @param RelationFilter $relationFilter
     * @param DeleteModalForm $deleteModalForm
     * @param RelationManager $relationManager
     */
    public function __construct(
        RelationFacade $relationFacade,

        RelationFilter $relationFilter,

        DeleteModalForm $deleteModalForm,

        RelationManager $relationManager
    ) {
        parent::__construct();

        $this->relationFacade = $relationFacade;

        $this->relationFilter = $relationFilter;

        $this->deleteModalForm = $deleteModalForm;

        $this->relationManager = $relationManager;
    }

    public function render()
    {
        $this['relationDeleteRelationFromEditForm']->render();
    }

    /**
     * @param int $relationId
     */
    public function handleRelationDeleteRelationFromEdit($relationId)
    {
        $presenter = $this->presenter;

        if (!$presenter->isAjax()) {
            $presenter->redirect('Relation:edit', $presenter->getParameter('id'));
        }

        $this['relationDeleteRelationFromEditForm']->setDefaults(['relationId' => $relationId]);

        $relationModalItem = $this->relationFacade->select()->getCachedManager()->getByPrimaryKey($relationId);
        $relationFilter = $this->relationFilter;

        $presenter->template->modalName = 'relationDeleteRelationFromEdit';
        $presenter->template->relationModalItem = $relationFilter($relationModalItem);

        $presenter->payload->showModal = true;

        $presenter->redrawControl('modal');
    }

    /**
     * @return Form
     */
    protected function createComponentRelationDeleteRelationFromEditForm()
    {
        $deleteModalFormSettings = new DeleteModalFormSettings();
        $deleteModalFormSettings->callBack = [$this, 'relationDeleteRelationFromEditFormYesOnClick'];
        $deleteModalFormSettings->httpRedirect = true;

        $form = $this->deleteModalForm->create($deleteModalFormSettings);

        $form->addHidden('relationId');

        return $form;
    }

    /**
     * @param SubmitButton $submitButton
     * @param ArrayHash $values
     */
    public function relationDeleteRelationFromEditFormYesOnClick(SubmitButton $submitButton, ArrayHash $values)
    {
        $presenter = $this->presenter;

        if (!$presenter->isAjax()) {
            $presenter->redirect('Relation:edit', $presenter->getParameter('id'));
        }

        try {
            $this->relationManager->delete()->deleteByPrimaryKey($values->relationId);

            $presenter->flashMessage('relation_deleted', BasePresenter::FLASH_SUCCESS);

            $presenter->redirect('Relation:default');
        } catch (ForeignKeyConstraintViolationException $e) {
            if ($e->getCode() === 1451) {
                $presenter->flashMessage('Item has some unset relations', BasePresenter::FLASH_DANGER);

                $presenter->redrawControl('flashes');
            } else {
                Debugger::log($e, ILogger::EXCEPTION);
            }
        }
    }
}
