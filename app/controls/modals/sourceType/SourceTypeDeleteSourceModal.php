<?php
/**
 *
 * Created by PhpStorm.
 * Filename: SourceTypeDeleteSourceModal.php
 * User: Tomáš Babický
 * Date: 21.02.2021
 * Time: 1:33
 */

namespace Rendix2\FamilyTree\App\Controls\Modals\SourceType;

use Dibi\ForeignKeyConstraintViolationException;
use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Nette\Forms\Controls\SubmitButton;
use Nette\Localization\ITranslator;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Filters\SourceFilter;
use Rendix2\FamilyTree\App\Forms\DeleteModalForm;
use Rendix2\FamilyTree\App\Managers\SourceManager;
use Rendix2\FamilyTree\App\Model\Facades\SourceFacade;
use Rendix2\FamilyTree\App\Presenters\BasePresenter;
use Tracy\Debugger;
use Tracy\ILogger;

/**
 * Class SourceTypeDeleteSourceModal
 *
 * @package Rendix2\FamilyTree\App\Controls\Modals\SourceType
 */
class SourceTypeDeleteSourceModal extends Control
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
     * SourceTypeDeleteSourceModal constructor.
     *
     * @param SourceFacade $sourceFacade
     * @param SourceFilter $sourceFilter
     * @param SourceManager $sourceManager
     * @param ITranslator $translator
     */
    public function __construct(
        SourceFacade $sourceFacade,
        SourceFilter $sourceFilter,
        SourceManager $sourceManager,
        ITranslator $translator
    ) {
        parent::__construct();

        $this->sourceFacade = $sourceFacade;
        $this->sourceFilter = $sourceFilter;
        $this->sourceManager = $sourceManager;
        $this->translator = $translator;
    }

    public function render()
    {
        $this['sourceTypeDeleteSourceForm']->render();
    }

    /**
     * @param int $sourceTypeId
     * @param int $sourceId
     */
    public function handleSourceTypeDeleteSource($sourceTypeId, $sourceId)
    {
        $presenter = $this->presenter;

        if (!$presenter->isAjax()) {
            $presenter->redirect('SourceType:edit', $presenter->getParameter('id'));
        }

        $this['sourceTypeDeleteSourceForm']->setDefaults(
            [
                'sourceTypeId' => $sourceTypeId,
                'sourceId' => $sourceId
            ]
        );

        $sourceFilter = $this->sourceFilter;

        $sourceModalItem = $this->sourceFacade->getByPrimaryKeyCached($sourceId);

        $presenter->template->modalName = 'sourceTypeDeleteSource';
        $presenter->template->sourceModalItem = $sourceFilter($sourceModalItem);

        $presenter->payload->showModal = true;

        $presenter->redrawControl('modal');
    }

    /**
     * @return Form
     */
    protected function createComponentSourceTypeDeleteSourceForm()
    {
        $formFactory = new DeleteModalForm($this->translator);

        $form = $formFactory->create([$this, 'sourceTypeDeleteSourceForYesOnClick']);
        $form->addHidden('sourceTypeId');
        $form->addHidden('sourceId');

        return $form;
    }

    /**
     * @param SubmitButton $submitButton
     * @param ArrayHash $values
     */
    public function sourceTypeDeleteSourceForYesOnClick(SubmitButton $submitButton, ArrayHash $values)
    {
        $presenter = $this->presenter;

        if (!$presenter->isAjax()) {
            $presenter->redirect('SourceType:edit', $presenter->getParameter('id'));
        }

        try {
            $this->sourceManager->deleteByPrimaryKey($values->sourceId);

            $sources = $this->sourceFacade->getBySourceTypeId($values->sourceTypeId);

            $presenter->template->sources = $sources;

            $presenter->payload->showModal = false;

            $presenter->flashMessage('source_deleted', BasePresenter::FLASH_SUCCESS);

            $presenter->redrawControl('sources');
        } catch (ForeignKeyConstraintViolationException $e) {
            if ($e->getCode() === 1451) {
                $presenter->flashMessage('Item has some unset relations', BasePresenter::FLASH_DANGER);
            } else {
                Debugger::log($e, ILogger::EXCEPTION);
            }
        } finally {
            $presenter->redrawControl('flashes');
        }
    }
}