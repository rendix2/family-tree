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
use Nette\Localization\ITranslator;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Facades\PersonFacade;
use Rendix2\FamilyTree\App\Filters\HistoryNoteFilter;
use Rendix2\FamilyTree\App\Filters\PersonFilter;
use Rendix2\FamilyTree\App\Forms\DeleteModalForm;
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
     * @var ITranslator $translator
     */
    private $translator;

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
     * @param ITranslator $translator
     * @param NoteHistoryManager $historyNoteManager
     * @param HistoryNoteFacade $historyNoteFacade
     * @param PersonFacade $personFacade
     * @param HistoryNoteFilter $historyNoteFilter
     * @param PersonFilter $personFilter
     */
    public function __construct(
        ITranslator $translator,
        NoteHistoryManager $historyNoteManager,
        HistoryNoteFacade $historyNoteFacade,
        PersonFacade $personFacade,
        HistoryNoteFilter $historyNoteFilter,
        PersonFilter $personFilter
    ) {
        parent::__construct();

        $this->translator = $translator;
        $this->historyNoteManager = $historyNoteManager;
        $this->historyNoteFacade = $historyNoteFacade;
        $this->personFacade = $personFacade;
        $this->historyNoteFilter = $historyNoteFilter;
        $this->personFilter = $personFilter;
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

        if (!$this->presenter->isAjax()) {
            $this->presenter->redirect('Person:edit', $this->getParameter('id'));
        }

        if ($this->presenter->isAjax()) {
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

            $this->presenter->template->modalName = 'personDeleteHistoryNote';
            $this->presenter->template->personModalItem = $personFilter($personModalItem);
            $this->presenter->template->historyNoteModalItem = $historyNoteFilter($historyNoteModalItem);

            $this->presenter->payload->showModal = true;

            $this->presenter->redrawControl('modal');
        }
    }

    /**
     * @return Form
     */
    protected function createComponentPersonDeleteHistoryNoteForm()
    {
        $formFactory = new DeleteModalForm($this->translator);

        $form = $formFactory->create([$this, 'personDeleteHistoryNoteFormYesOnClick']);
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

        if ($this->presenter->isAjax()) {
            $this->historyNoteManager->deleteByPrimaryKey($values->historyNoteId);

            $historyNotes = $this->historyNoteManager->getByPerson($values->personId);

            $this->presenter->template->historyNotes = $historyNotes;

            $this->presenter->payload->showModal = false;

            $this->presenter->flashMessage('history_note_deleted', BasePresenter::FLASH_SUCCESS);

            $this->presenter->redrawControl('flashes');
            $this->presenter->redrawControl('history_notes');
        } else {
            $this->presenter->redirect('Person:edit', $values->personId);
        }
    }
}
