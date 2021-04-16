<?php
/**
 *
 * Created by PhpStorm.
 * Filename: AddressDeleteWeddingModal.php
 * User: Tomáš Babický
 * Date: 28.11.2020
 * Time: 1:08
 */

namespace Rendix2\FamilyTree\App\Controls\Modals\Address;

use Dibi\ForeignKeyConstraintViolationException;
use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Nette\Forms\Controls\SubmitButton;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Controls\Forms\DeleteModalForm;
use Rendix2\FamilyTree\App\Controls\Forms\Settings\DeleteModalFormSettings;
use Rendix2\FamilyTree\App\Filters\WeddingFilter;
use Rendix2\FamilyTree\App\Model\Facades\WeddingFacade;
use Rendix2\FamilyTree\App\Model\Managers\WeddingManager;
use Rendix2\FamilyTree\App\Presenters\BasePresenter;
use Tracy\Debugger;
use Tracy\ILogger;

/**
 * Class AddressDeleteWeddingModal
 *
 * @package Rendix2\FamilyTree\App\Controls\Modals\Address
 */
class AddressDeleteWeddingModal extends Control
{
    /**
     * @var WeddingFacade $weddingFacade
     */
    private $weddingFacade;

    /**
     * @var WeddingFilter $weddingFilter
     */
    private $weddingFilter;

    /**
     * @var DeleteModalForm $deleteModalForm
     */
    private $deleteModalForm;

    /**
     * @var WeddingManager $weddingManager
     */
    private $weddingManager;

    /**
     * AddressDeleteWeddingModal constructor.
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

        $this->weddingFacade = $weddingFacade;

        $this->weddingFilter = $weddingFilter;

        $this->deleteModalForm = $deleteModalForm;

        $this->weddingManager = $weddingManager;
    }

    public function render()
    {
        $this['addressDeleteWeddingForm']->render();
    }

    /**
     * @param int $addressId
     * @param int $weddingId
     */
    public function handleAddressDeleteWedding($addressId, $weddingId)
    {
        $presenter = $this->presenter;

        if (!$presenter->isAjax()) {
            $presenter->redirect('Address:edit', $presenter->getParameter('id'));
        }

        $this['addressDeleteWeddingForm']->setDefaults(
            [
                'addressId' => $addressId,
                'weddingId' => $weddingId,
            ]
        );

        $weddingModalItem = $this->weddingFacade->select()->getCachedManager()->getByPrimaryKey($weddingId);

        $weddingFilter = $this->weddingFilter;

        $presenter->template->modalName = 'addressDeleteWedding';
        $presenter->template->weddingModalItem = $weddingFilter($weddingModalItem);

        $presenter->payload->showModal = true;

        $presenter->redrawControl('modal');
    }

    /**
     * @return Form
     */
    protected function createComponentAddressDeleteWeddingForm()
    {
        $deleteModalFormSettings = new DeleteModalFormSettings();
        $deleteModalFormSettings->callBack = [$this, 'addressDeleteWeddingFormYesOnClick'];
        $deleteModalFormSettings->httpRedirect = true;

        $form = $this->deleteModalForm->create($deleteModalFormSettings);

        $form->addHidden('addressId');
        $form->addHidden('weddingId');

        return $form;
    }

    /**
     * @param SubmitButton $submitButton
     * @param ArrayHash    $values
     */
    public function addressDeleteWeddingFormYesOnClick(SubmitButton $submitButton, ArrayHash $values)
    {
        $presenter = $this->presenter;

        if (!$presenter->isAjax()) {
            $presenter->redirect('Address:edit', $presenter->getParameter('id'));
        }

        try {
            $this->weddingManager->delete()->deleteByPrimaryKey($values->weddingId);

            $weddings = $this->weddingFacade->select()->getCachedManager()->getByAddressId($values->addressId);

            $presenter->template->weddings = $weddings;

            $presenter->flashMessage('wedding_deleted', BasePresenter::FLASH_SUCCESS);

            $presenter->redrawControl('weddings');
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
