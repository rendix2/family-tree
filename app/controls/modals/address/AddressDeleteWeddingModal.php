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
 * Class AddressDeleteWeddingModal
 *
 * @package Rendix2\FamilyTree\App\Controls\Modals\Address
 */
class AddressDeleteWeddingModal extends Control
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
     * AddressDeleteWeddingModal constructor.
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
        $this['addressDeleteWeddingForm']->render();
    }

    /**
     * @param int $addressId
     * @param int $weddingId
     */
    public function handleAddressDeleteWedding($addressId, $weddingId)
    {
        $presenter = $this->presenter;

        if ($presenter->isAjax()) {
            $this['addressDeleteWeddingForm']->setDefaults(
                [
                    'addressId' => $addressId,
                    'weddingId' => $weddingId
                ]
            );

            $weddingModalItem = $this->weddingFacade->getByPrimaryKeyCached($weddingId);

            $weddingFilter = $this->weddingFilter;

            $presenter->template->modalName = 'addressDeleteWedding';
            $presenter->template->weddingModalItem = $weddingFilter($weddingModalItem);

            $presenter->payload->showModal = true;

            $presenter->redrawControl('modal');
        }
    }

    /**
     * @return Form
     */
    protected function createComponentAddressDeleteWeddingForm()
    {
        $formFactory = new DeleteModalForm($this->translator);

        $form = $formFactory->create([$this, 'addressDeleteWeddingFormYesOnClick'], true);
        $form->addHidden('addressId');
        $form->addHidden('weddingId');

        return $form;
    }

    /**
     * @param SubmitButton $submitButton
     * @param ArrayHash $values
     */
    public function addressDeleteWeddingFormYesOnClick(SubmitButton $submitButton, ArrayHash $values)
    {
        $presenter = $this->presenter;

        try {
            $this->weddingManager->deleteByPrimaryKey($values->weddingId);

            $weddings = $this->weddingFacade->getByAddressId($values->addressId);

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
