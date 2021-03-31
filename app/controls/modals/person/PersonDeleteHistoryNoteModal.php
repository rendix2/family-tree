<?php
/**
 *
 * Created by PhpStorm.
 * Filename: PersonDeleteHistoryNoteModal.php
 * User: Tomáš Babický
 * Date: 20.02.2021
 * Time: 13:09
 */

namespace Rendix2\FamilyTree\App\Controls\Modals\Person;

use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Nette\Forms\Controls\SubmitButton;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Controls\Forms\DeleteModalForm;
use Rendix2\FamilyTree\App\Controls\Forms\Settings\DeleteModalFormSettings;
use Rendix2\FamilyTree\App\Facades\PersonFacade;
use Rendix2\FamilyTree\App\Filters\HistoryNoteFilter;
use Rendix2\FamilyTree\App\Filters\PersonFilter;

use Rendix2\FamilyTree\App\Managers\NoteHistoryManager;
use Rendix2\FamilyTree\App\Model\Facades\HistoryNoteFacade;
use Rendix2\FamilyTree\App\Presenters\BasePresenter;

/**
 * Class PersonDeleteHistoryNoteModal
 *
 * @package Rendix2\FamilyTree\App\Controls\Modals\Person
 */
class PersonDeleteHistoryNoteModal extends Control
{
    /**
     * @var DeleteModalForm $deleteModalForm
     */
    private $deleteModalForm;

    /**
     * @var NoteHistoryManager $historyNoteManager
     */
    private $historyNoteManager;

    /**
     * @var HistoryNoteFacade $historyNoteFacade
     */
    private $historyNoteFacade;

    /**
     * @var PersonFacade $personFacade
     */
    private $personFacade;

    /**
     * @var HistoryNoteFilter $historyNoteFilter
     */
    private $historyNoteFilter;

    /**
     * @var PersonFilter $personFilter
     */
    private $personFilter;

    /**
     * PersonDeleteHistoryNoteModal constructor.
     *
     * @param DeleteModalForm    $deleteModalForm
     * @param NoteHistoryManager $historyNoteManager
     * @param HistoryNoteFacade  $historyNoteFacade
     * @param PersonFacade       $personFacade
     * @param HistoryNoteFilter  $historyNoteFilter
     * @param PersonFilter       $personFilter
     */
    public function __construct(
        DeleteModalForm $deleteModalForm,
        NoteHistoryManager $historyNoteManager,
        HistoryNoteFacade $historyNoteFacade,
        PersonFacade $personFacade,
        HistoryNoteFilter $historyNoteFilter,
        PersonFilter $personFilter
    ) {
        parent::__construct();

        $this->historyNoteManager = $historyNoteManager;
        $this->historyNoteFacade = $historyNoteFacade;
        $this->personFacade = $personFacade;
        $this->historyNoteFilter = $historyNoteFilter;
        $this->personFilter = $personFilter;

        $this->deleteModalForm = $deleteModalForm;
    }

    /**
     * @return void
     */
    public function render()
    {
        $this['personDeleteHistoryNoteForm']->render();
    }

    /**
     * @param int $personId
     * @param int $historyNoteId
     */
    public function handlePersonDeleteHistoryNote($personId, $historyNoteId)
    {
        $presenter = $this->presenter;

        if (!$presenter->isAjax()) {
            $presenter->redirect('Person:edit', $presenter->getParameter('id'));
        }

        $this['personDeleteHistoryNoteForm']->setDefaults(
            [
                'personId' => $personId,
                'historyNoteId' => $historyNoteId
            ]
        );

        $personFilter = $this->personFilter;
        $historyNoteFilter = $this->historyNoteFilter;

        $personModalItem = $this->personFacade->getByPrimaryKeyCached($personId);
        $historyNoteModalItem = $this->historyNoteFacade->getByPrimaryKeyCached($historyNoteId);

        $presenter->template->modalName = 'personDeleteHistoryNote';
        $presenter->template->personModalItem = $personFilter($personModalItem);
        $presenter->template->historyNoteModalItem = $historyNoteFilter($historyNoteModalItem);

        $presenter->payload->showModal = true;

        $presenter->redrawControl('modal');
    }

    /**
     * @return Form
     */
    protected function createComponentPersonDeleteHistoryNoteForm()
    {
        $deleteModalFormSettings = new DeleteModalFormSettings();
        $deleteModalFormSettings->callBack = [$this, 'personDeleteHistoryNoteFormYesOnClick'];

        $form = $this->deleteModalForm->create($deleteModalFormSettings);

        $form->addHidden('personId');
        $form->addHidden('historyNoteId');

        return $form;
    }

    /**
     * @param SubmitButton $submitButton
     * @param ArrayHash $values
     */
    public function personDeleteHistoryNoteFormYesOnClick(SubmitButton $submitButton, ArrayHash $values)
    {
        $presenter = $this->presenter;

        if (!$presenter->isAjax()) {
            $presenter->redirect('Person:edit', $presenter->getParameter('id'));
        }

        $this->historyNoteManager->deleteByPrimaryKey($values->historyNoteId);

        $historyNotes = $this->historyNoteManager->getByPersonId($values->personId);

        $presenter->template->historyNotes = $historyNotes;

        $presenter->payload->showModal = false;

        $presenter->flashMessage('history_note_deleted', BasePresenter::FLASH_SUCCESS);

        $presenter->redrawControl('flashes');
        $presenter->redrawControl('history_notes');
    }
}
