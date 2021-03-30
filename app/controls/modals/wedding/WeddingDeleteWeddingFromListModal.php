<?php
/**
 *
 * Created by PhpStorm.
 * Filename: WeddingDeleteWeddingFromListModal.php
 * User: Tomáš Babický
 * Date: 21.02.2021
 * Time: 1:26
 */

namespace Rendix2\FamilyTree\App\Controls\Modals\Wedding;

use Dibi\ForeignKeyConstraintViolationException;
use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Nette\Forms\Controls\SubmitButton;
use Nette\Localization\ITranslator;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Controls\Forms\DeleteModalForm;
use Rendix2\FamilyTree\App\Facades\WeddingFacade;
use Rendix2\FamilyTree\App\Filters\WeddingFilter;

use Rendix2\FamilyTree\App\Managers\WeddingManager;
use Rendix2\FamilyTree\App\Presenters\BasePresenter;
use Tracy\Debugger;
use Tracy\ILogger;

/**
 * Class WeddingDeleteWeddingFromListModal
 *
 * @package Rendix2\FamilyTree\App\Controls\Modals\Wedding
 */
class WeddingDeleteWeddingFromListModal extends Control
{
    /**
     * @var ITranslator $translator
     */
    private $translator;

    /**
     * @var WeddingFilter $weddingFilter
     */
    private $weddingFilter;

    /**
     * @var WeddingFacade $weddingFacade
     */
    private $weddingFacade;

    /**
     * @var WeddingManager $weddingManager
     */
    private $weddingManager;

    /**
     * WeddingDeleteWeddingFromListModal constructor.
     *
     * @param ITranslator $translator
     * @param WeddingFilter $weddingFilter
     * @param WeddingFacade $weddingFacade
     * @param WeddingManager $weddingManager
     */
    public function __construct(
        WeddingFilter $weddingFilter,
        WeddingFacade $weddingFacade,

        DeleteModalForm $deleteModalForm,

        WeddingManager $weddingManager
    ) {
        parent::__construct();

        $this->weddingFilter = $weddingFilter;
        $this->weddingFacade = $weddingFacade;
        $this->weddingManager = $weddingManager;
    }

    public function render()
    {
        $this['weddingDeleteWeddingFromListForm']->render();
    }

    /**
     * @param int $weddingId
     */
    public function handleWeddingDeleteWeddingFromList($weddingId)
    {
        $presenter = $this->presenter;

        if (!$presenter->isAjax()) {
            $presenter->redirect('Wedding:default');
        }

        $this['weddingDeleteWeddingFromListForm']->setDefaults(['weddingId' => $weddingId]);

        $weddingModalItem = $this->weddingFacade->getByPrimaryKeyCached($weddingId);

        $weddingFilter = $this->weddingFilter;

        $presenter->template->modalName = 'weddingDeleteWeddingFromList';
        $presenter->template->weddingModalItem = $weddingFilter($weddingModalItem);

        $presenter->payload->showModal = true;

        $presenter->redrawControl('modal');
    }

    /**
     * @return Form
     */
    protected function createComponentWeddingDeleteWeddingFromListForm()
    {
        $formFactory = new DeleteModalForm($this->translator);

        $form = $formFactory->create([$this, 'weddingDeleteWeddingFromListFormYesOnClick']);
        $form->addHidden('weddingId');

        return $form;
    }

    /**
     * @param SubmitButton $submitButton
     * @param ArrayHash $values
     */
    public function weddingDeleteWeddingFromListFormYesOnClick(SubmitButton $submitButton, ArrayHash $values)
    {
        $presenter = $this->presenter;

        if (!$presenter->isAjax()) {
            $presenter->redirect('Wedding:default');
        }

        try {
            $this->weddingManager->deleteByPrimaryKey($values->weddingId);

            $presenter->flashMessage('wedding_deleted', BasePresenter::FLASH_SUCCESS);

            $presenter->redrawControl('list');
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
