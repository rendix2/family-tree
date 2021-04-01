<?php
/**
 *
 * Created by PhpStorm.
 * Filename: TownDeleteWeddingModal.php
 * User: Tomáš Babický
 * Date: 31.10.2020
 * Time: 15:32
 */

namespace Rendix2\FamilyTree\App\Controls\Modals\Town;

use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Nette\Forms\Controls\SubmitButton;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Controls\Forms\DeleteModalForm;
use Rendix2\FamilyTree\App\Controls\Forms\Settings\DeleteModalFormSettings;
use Rendix2\FamilyTree\App\Facades\WeddingFacade;
use Rendix2\FamilyTree\App\Filters\WeddingFilter;

use Rendix2\FamilyTree\App\Managers\WeddingManager;
use Rendix2\FamilyTree\App\Presenters\BasePresenter;

/**
 * Class TownDeleteWeddingModal
 *
 * @package Rendix2\FamilyTree\App\Controls\Modals\Town
 */
class TownDeleteWeddingModal extends Control
{
    /**
     * @var DeleteModalForm $deleteModalForm
     */
    private $deleteModalForm;

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
     * TownDeleteWeddingModal constructor.
     *
     * @param WeddingFacade   $weddingFacade
     * @param WeddingFilter   $weddingFilter
     * @param DeleteModalForm $deleteModalForm
     * @param WeddingManager  $weddingManager
     */
    public function __construct(
        WeddingFacade $weddingFacade,
        WeddingFilter $weddingFilter,

        DeleteModalForm $deleteModalForm,

        WeddingManager $weddingManager
    ) {
        parent::__construct();

        $this->deleteModalForm = $deleteModalForm;

        $this->weddingFacade = $weddingFacade;
        $this->weddingFilter = $weddingFilter;
        $this->weddingManager = $weddingManager;
    }

    public function render()
    {
        $this['townDeleteWeddingForm']->render();
    }

    /**
     * @param int $townId
     * @param int $weddingId
     */
    public function handleTownDeleteWedding($townId, $weddingId)
    {
        $presenter = $this->presenter;

        if (!$presenter->isAjax()) {
            $presenter->redirect('Town:edit', $presenter->getParameter('id'));
        }

        $this['townDeleteWeddingForm']->setDefaults(
            [
                'townId' => $townId,
                'weddingId' => $weddingId
            ]
        );

        $weddingFilter = $this->weddingFilter;

        $weddingModalItem = $this->weddingFacade->getByPrimaryKeyCached($weddingId);

        $presenter->template->modalName = 'townDeleteWedding';
        $presenter->template->weddingModalItem = $weddingFilter($weddingModalItem);

        $presenter->payload->showModal = true;

        $presenter->redrawControl('modal');
    }

    /**
     * @return Form
     */
    protected function createComponentTownDeleteWeddingForm()
    {
        $deleteModalFormSettings = new DeleteModalFormSettings();
        $deleteModalFormSettings->callBack = [$this, 'townDeleteWeddingFormYesOnClick'];

        $form = $this->deleteModalForm->create($deleteModalFormSettings);

        $form->addHidden('townId');
        $form->addHidden('weddingId');

        return $form;
    }

    /**
     * @param SubmitButton $submitButton
     * @param ArrayHash $values
     */
    public function townDeleteWeddingFormYesOnClick(SubmitButton $submitButton, ArrayHash $values)
    {
        $presenter = $this->presenter;

        if (!$presenter->isAjax()) {
            $presenter->redirect('Town:edit', $presenter->getParameter('id'));
        }

        $this->weddingManager->deleteByPrimaryKey($values->weddingId);

        $weddings = $this->weddingManager->getByTownId($values->townId);

        $presenter->template->weddings = $weddings;

        $presenter->payload->showModal = false;

        $presenter->flashMessage('wedding_deleted', BasePresenter::FLASH_SUCCESS);

        $presenter->redrawControl('weddings');
        $presenter->redrawControl('flashes');
    }
}
