<?php
/**
 *
 * Created by PhpStorm.
 * Filename: SourceDeleteSourceFromEditModal.php
 * User: Tomáš Babický
 * Date: 21.02.2021
 * Time: 1:39
 */

namespace Rendix2\FamilyTree\App\Controls\Modals\Source;

use Dibi\ForeignKeyConstraintViolationException;
use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Nette\Forms\Controls\SubmitButton;
use Nette\Localization\ITranslator;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Controls\Forms\DeleteModalForm;
use Rendix2\FamilyTree\App\Filters\SourceFilter;

use Rendix2\FamilyTree\App\Managers\SourceManager;
use Rendix2\FamilyTree\App\Model\Facades\SourceFacade;
use Rendix2\FamilyTree\App\Presenters\BasePresenter;
use Tracy\Debugger;
use Tracy\ILogger;

/**
 * Class SourceDeleteSourceFromEditModal
 *
 * @package Rendix2\FamilyTree\App\Controls\Modals\Source
 */
class SourceDeleteSourceFromEditModal extends Control
{
    /**
     * @var SourceFacade $sourceFacade
     */
    private $sourceFacade;

    /**
     * @var SourceFilter $sourceFilter
     */
    private $sourceFilter;

    /**
     * @var SourceManager $sourceManager
     */
    private $sourceManager;

    /**
     * @var ITranslator $translator
     */
    private $translator;

    /**
     * SourceDeleteSourceFromEditModal constructor.
     *
     * @param SourceFacade $sourceFacade
     * @param SourceFilter $sourceFilter
     * @param SourceManager $sourceManager
     * @param ITranslator $translator
     */
    public function __construct(
        SourceFacade $sourceFacade,
        SourceFilter $sourceFilter,

        DeleteModalForm $deleteModalForm,

        SourceManager $sourceManager,
    ) {
        parent::__construct();

        $this->sourceFacade = $sourceFacade;
        $this->sourceFilter = $sourceFilter;
        $this->sourceManager = $sourceManager;
    }

    public function render()
    {
        $this['sourceDeleteSourceFromEditForm']->render();
    }

    /**
     * @param int $sourceId
     */
    public function handleSourceDeleteSourceFromEdit($sourceId)
    {
        $presenter = $this->presenter;

        if (!$presenter->isAjax()) {
            $presenter->redirect('Source:edit', $presenter->getParameter('id'));
        }

        $this['sourceDeleteSourceFromEditForm']->setDefaults(['sourceId' => $sourceId]);

        $sourceFilter = $this->sourceFilter;

        $sourceModalItem = $this->sourceFacade->getByPrimaryKeyCached($sourceId);

        $presenter->template->modalName = 'sourceDeleteSourceFromEdit';
        $presenter->template->sourceModalItem = $sourceFilter($sourceModalItem);

        $presenter->payload->showModal = true;

        $presenter->redrawControl('modal');
    }

    /**
     * @return Form
     */
    protected function createComponentSourceDeleteSourceFromEditForm()
    {
        $formFactory = new DeleteModalForm($this->translator);

        $form = $formFactory->create([$this, 'sourceDeleteSourceFromEditFormYesOnClick'], true);
        $form->addHidden('sourceId');

        return $form;
    }

    /**
     * @param SubmitButton $submitButton
     * @param ArrayHash $values
     */
    public function sourceDeleteSourceFromEditFormYesOnClick(SubmitButton $submitButton, ArrayHash $values)
    {
        $presenter = $this->presenter;

        if (!$presenter->isAjax()) {
            $presenter->redirect('Source:edit', $presenter->getParameter('id'));
        }

        try {
            $this->sourceManager->deleteByPrimaryKey($values->sourceId);

            $presenter->flashMessage('source_deleted', BasePresenter::FLASH_SUCCESS);

            $presenter->redirect(':default');
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
