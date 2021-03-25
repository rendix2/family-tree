<?php
/**
 *
 * Created by PhpStorm.
 * Filename: WeddingDeleteWeddingFromEditModal.php
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
use Rendix2\FamilyTree\App\Facades\WeddingFacade;
use Rendix2\FamilyTree\App\Filters\WeddingFilter;
use Rendix2\FamilyTree\App\Forms\DeleteModalForm;
use Rendix2\FamilyTree\App\Managers\WeddingManager;
use Rendix2\FamilyTree\App\Presenters\BasePresenter;
use Tracy\Debugger;
use Tracy\ILogger;

/**
 * Class WeddingDeleteWeddingFromEditModal
 *
 * @package Rendix2\FamilyTree\App\Controls\Modals\Wedding
 */
class WeddingDeleteWeddingFromEditModal extends Control
{
    /**
     * @var ITranslator $translator
     */
    private $translator;

    /**
     * @var WeddingFacade $weddingFacade
     */
    private $weddingFacade;

    /**
     * @var WeddingFilter $weddingFilter
     */
    private $weddingFilter;

    /**
     * @var WeddingManager $weddingManager
     */
    private $weddingManager;

    /**
     * WeddingDeleteWeddingFromEditModal constructor.
     *
     * @param ITranslator $translator
     * @param WeddingFacade $weddingFacade
     * @param WeddingFilter $weddingFilter
     * @param WeddingManager $weddingManager
     */
    public function __construct(
        ITranslator $translator,
        WeddingFacade $weddingFacade,
        WeddingFilter $weddingFilter,
        WeddingManager $weddingManager
    ) {
        parent::__construct();

        $this->translator = $translator;
        $this->weddingFacade = $weddingFacade;
        $this->weddingFilter = $weddingFilter;
        $this->weddingManager = $weddingManager;
    }

    public function render()
    {
        $this['weddingDeleteWeddingFromEditForm']->render();
    }

    /**
     * @param int $weddingId
     */
    public function handleWeddingDeleteWeddingFromEdit($weddingId)
    {
        $presenter = $this->presenter;

        if ($presenter->isAjax()) {
            $this['weddingDeleteWeddingFromEditForm']->setDefaults(['weddingId' => $weddingId]);

            $weddingModalItem = $this->weddingFacade->getByPrimaryKeyCached($weddingId);

            $weddingFilter = $this->weddingFilter;

            $presenter->template->modalName = 'weddingDeleteWeddingFromEdit';
            $presenter->template->weddingModalItem = $weddingFilter($weddingModalItem);

            $presenter->payload->showModal = true;

            $presenter->redrawControl('modal');
        }
    }

    /**
     * @return Form
     */
    protected function createComponentWeddingDeleteWeddingFromEditForm()
    {
        $formFactory = new DeleteModalForm($this->translator);

        $form = $formFactory->create([$this, 'weddingDeleteWeddingFromEditFormYesOnClick'], true);
        $form->addHidden('weddingId');

        return $form;
    }

    /**
     * @param SubmitButton $submitButton
     * @param ArrayHash $values
     */
    public function weddingDeleteWeddingFromEditFormYesOnClick(SubmitButton $submitButton, ArrayHash $values)
    {
        $presenter = $this->presenter;

        try {
            $this->weddingManager->deleteByPrimaryKey($values->weddingId);

            $presenter->flashMessage('wedding_deleted', BasePresenter::FLASH_SUCCESS);

            $presenter->redirect('Wedding:default');
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
