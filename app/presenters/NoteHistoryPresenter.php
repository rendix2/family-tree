<?php
/**
 *
 * Created by PhpStorm.
 * Filename: NoteHistory.php
 * User: Tomáš Babický
 * Date: 16.09.2020
 * Time: 2:01
 */

namespace Rendix2\FamilyTree\App\Presenters;

use Dibi\DateTime;
use Nette\Application\UI\Form;
use Nette\Forms\Controls\SubmitButton;
use Nette\Localization\ITranslator;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\BootstrapRenderer;
use Rendix2\FamilyTree\App\Filters\PersonFilter;
use Rendix2\FamilyTree\App\Managers\NoteHistoryManager;
use Rendix2\FamilyTree\App\Managers\PersonManager;

class NoteHistoryPresenter extends BasePresenter
{
    use CrudPresenter;

    /**
     * @var NoteHistoryManager $manager
     */
    private $manager;

    /**
     * @var PersonManager $personManager
     */
    private $personManager;

    /**
     * @var ITranslator $translator
     */
    private $translator;

    /**
     * NoteHistoryPresenter constructor.
     *
     * @param NoteHistoryManager $noteHistoryManager
     * @param PersonManager $personManager
     */
    public function __construct(
        NoteHistoryManager $noteHistoryManager,
        PersonManager $personManager
    ) {
        parent::__construct();

        $this->manager = $noteHistoryManager;
        $this->personManager = $personManager;
    }

    /**
     * @return void
     */
    public function renderDefault()
    {
        $notesHistory = $this->manager->getAllJoinedPerson();

        $this->template->notesHistory = $notesHistory;

        $this->template->addFilter('person', new PersonFilter());
    }

    /**
     * @param int $id
     */
    public function actionApplyNote($id)
    {
        $note = $this->manager->getByPrimaryKey($id);

        if (!$note) {
            $this->error('Item not found.');
        }

        $this->personManager->updateByPrimaryKey($id, ['note' => $note->text]);
        $this->flashMessage('item_updated', self::FLASH_SUCCESS);
        $this->redirect('Person:edit', $id);
    }

    /**
     * @return Form
     */
    public function createComponentForm()
    {
        $form = new Form();

        $form->setTranslator($this->getTranslator());

        $form->addProtection();

        $form->addTextArea('text', 'note_history_text')
            ->setAttribute('class', ' form-control tinyMCE');

        $form->addSubmit('send', 'save');
        $form->addSubmit('use', 'note_history_apply')->onClick[] = [$this, 'useNote'];

        $form->onSuccess[] = [$this, 'saveForm'];
        $form->onRender[] = [BootstrapRenderer::class, 'makeBootstrap4'];

        return $form;
    }

    /**
     * @param SubmitButton $submitButton
     * @param ArrayHash $values
     */
    public function useNote(SubmitButton $submitButton, ArrayHash $values)
    {
        $id = $this->presenter->getParameter('id');

        $note = $this->manager->getByPrimaryKey($id);

        if ($note->text !== $values->text) {
            $noteHistoryData = [
                'personId' => $id,
                'text'     => $values->text,
                'date'     => new DateTime()
            ];

            $this->manager->add($noteHistoryData);
        }

        $this->personManager->updateByPrimaryKey($id, ['note' => $values->text]);
        $this->flashMessage('item_updated', self::FLASH_SUCCESS);
        $this->redirect('Person:edit', $id);
    }
}
