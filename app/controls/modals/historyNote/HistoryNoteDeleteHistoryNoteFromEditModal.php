<?php
/**
 *
 * Created by PhpStorm.
 * Filename: HistoryNoteDeleteHistoryNoteFromEditModal.php
 * User: Tomáš Babický
 * Date: 21.02.2021
 * Time: 1:48
 */

namespace Rendix2\FamilyTree\App\Controls\Modals\HistoryNote;

use Dibi\ForeignKeyConstraintViolationException;
use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Nette\Forms\Controls\SubmitButton;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Controls\Forms\DeleteModalForm;
use Rendix2\FamilyTree\App\Controls\Forms\Settings\DeleteModalFormSettings;
use Rendix2\FamilyTree\App\Filters\HistoryNoteFilter;
use Rendix2\FamilyTree\App\Model\Facades\HistoryNoteFacade;
use Rendix2\FamilyTree\App\Model\Managers\HistoryNoteManager;
use Rendix2\FamilyTree\App\Presenters\BasePresenter;
use Tracy\Debugger;
use Tracy\ILogger;

/**
 * Class HistoryNoteDeleteHistoryNoteFromEditModal
 *
 * @package Rendix2\FamilyTree\App\Controls\Modals\HistoryNote
 */
class HistoryNoteDeleteHistoryNoteFromEditModal extends Control
{
    /**
     * @var DeleteModalForm $deleteModalForm
     */
    private $deleteModalForm;

    /**
     * @var HistoryNoteFacade $historyNoteFacade
     */
    private $historyNoteFacade;

    /**
     * @var HistoryNoteFilter $historyNoteFilter
     */
    private $historyNoteFilter;

    /**
     * @var HistoryNoteManager $historyNoteManager
     */
    private $historyNoteManager;

    /**
     * HistoryNoteDeleteHistoryNoteFromEditModal constructor.
     *
     * @param HistoryNoteFacade $historyNoteFacade
     * @param HistoryNoteFilter $historyNoteFilter
     * @param DeleteModalForm $deleteModalForm
     * @param HistoryNoteManager $historyNoteManager
     */
    public function __construct(
        HistoryNoteFacade $historyNoteFacade,
        HistoryNoteFilter $historyNoteFilter,
        DeleteModalForm $deleteModalForm,
        HistoryNoteManager $historyNoteManager
    ) {
        parent::__construct();

        $this->historyNoteFacade = $historyNoteFacade;
        $this->historyNoteFilter = $historyNoteFilter;
        $this->deleteModalForm = $deleteModalForm;
        $this->historyNoteManager = $historyNoteManager;
    }

    public function render()
    {
        $this['historyNoteDeleteHistoryNoteFromEditForm']->render();
    }

    /**
     * @param int $historyNoteId
     */
    public function handleHistoryNoteDeleteHistoryNoteFromEdit($historyNoteId)
    {
        $presenter = $this->presenter;

        if (!$presenter->isAjax()) {
            $presenter->redirect('HistoryNote:edit', $presenter->getParameter('id'));
        }

        $this['historyNoteDeleteHistoryNoteFromEditForm']->setDefaults(['historyNoteId' => $historyNoteId]);

        $historyNoteModalItem = $this->historyNoteFacade->select()->getManager()->getByPrimaryKey($historyNoteId);

        $historyNoteFilter = $this->historyNoteFilter;

        $presenter->template->modalName = 'historyNoteDeleteHistoryNoteFromEdit';
        $presenter->template->historyNoteModalItem = $historyNoteFilter($historyNoteModalItem);

        $presenter->payload->showModal = true;

        $presenter->redrawControl('modal');
    }

    /**
     * @return Form
     */
    protected function createComponentHistoryNoteDeleteHistoryNoteFromEditForm()
    {
        $deleteModalFormSettings = new DeleteModalFormSettings();
        $deleteModalFormSettings->callBack = [$this, 'historyNoteDeleteHistoryNoteFromEditFormYesOnClick'];
        $deleteModalFormSettings->httpRedirect = true;

        $form = $this->deleteModalForm->create($deleteModalFormSettings);

        $form->addHidden('historyNoteId');

        return $form;
    }

    /**
     * @param SubmitButton $submitButton
     * @param ArrayHash $values
     */
    public function historyNoteDeleteHistoryNoteFromEditFormYesOnClick(SubmitButton $submitButton, ArrayHash $values)
    {
        $presenter = $this->presenter;

        if (!$presenter->isAjax()) {
            $presenter->redirect('HistoryNote:edit', $presenter->getParameter('id'));
        }

        try {
            $this->historyNoteManager->delete()->deleteByPrimaryKey($values->historyNoteId);

            $presenter->flashMessage('history_note_deleted', BasePresenter::FLASH_SUCCESS);

            $presenter->redirect('HistoryNote:default');
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