<?php
/**
 *
 * Created by PhpStorm.
 * Filename: AddressDeleteWeddingAddressModal.php
 * User: Tomáš Babický
 * Date: 28.11.2020
 * Time: 1:09
 */

namespace Rendix2\FamilyTree\App\Controls\Modals\Address;

use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Nette\Forms\Controls\SubmitButton;
use Nette\Localization\ITranslator;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Facades\WeddingFacade;
use Rendix2\FamilyTree\App\Filters\AddressFilter;
use Rendix2\FamilyTree\App\Filters\WeddingFilter;
use Rendix2\FamilyTree\App\Forms\DeleteModalForm;
use Rendix2\FamilyTree\App\Managers\WeddingManager;
use Rendix2\FamilyTree\App\Model\Facades\AddressFacade;
use Rendix2\FamilyTree\App\Presenters\BasePresenter;

/**
 * Class AddressDeleteWeddingAddressModal
 *
 * @package Rendix2\FamilyTree\App\Controls\Modals\Address
 */
class AddressDeleteWeddingAddressModal extends Control
{
    /**
     * @var AddressFacade $addressFacade
     */
    private $addressFacade;

    /**
     * @var AddressFilter $addressFilter
     */
    private $addressFilter;

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
     * AddressDeleteWeddingAddressModal constructor.
     *
     * @param AddressFacade $addressFacade
     * @param AddressFilter $addressFilter
     * @param ITranslator $translator
     * @param WeddingFacade $weddingFacade
     * @param WeddingFilter $weddingFilter
     * @param WeddingManager $weddingManager
     */
    public function __construct(
        AddressFacade $addressFacade,
        AddressFilter $addressFilter,
        ITranslator $translator,
        WeddingFacade $weddingFacade,
        WeddingFilter $weddingFilter,
        WeddingManager $weddingManager
    ) {
        parent::__construct();

        $this->addressFacade = $addressFacade;
        $this->addressFilter = $addressFilter;
        $this->translator = $translator;
        $this->weddingFacade = $weddingFacade;
        $this->weddingFilter = $weddingFilter;
        $this->weddingManager = $weddingManager;
    }

    public function render()
    {
        $this['addressDeleteWeddingAddressForm']->render();
    }

    /**
     * @param int $addressId
     * @param int $weddingId
     */
    public function handleAddressDeleteWeddingAddress($addressId, $weddingId)
    {
        $presenter = $this->presenter;

        if (!$presenter->isAjax()) {
            $presenter->redirect('Address:edit', $presenter->getParameter('id'));
        }

        $this['addressDeleteWeddingAddressForm']->setDefaults(
            [
                'addressId' => $addressId,
                'weddingId' => $weddingId
            ]
        );

        $weddingModalItem = $this->weddingFacade->getByPrimaryKeyCached($weddingId);
        $addressModalItem = $this->addressFacade->getByPrimaryKeyCached($addressId);

        $weddingFilter = $this->weddingFilter;
        $addressFilter = $this->addressFilter;

        $presenter->template->modalName = 'addressDeleteWeddingAddress';
        $presenter->template->weddingModalItem = $weddingFilter($weddingModalItem);
        $presenter->template->addressModalItem = $addressFilter($addressModalItem);

        $presenter->payload->showModal = true;

        $presenter->redrawControl('modal');
    }

    /**
     * @return Form
     */
    protected function createComponentAddressDeleteWeddingAddressForm()
    {
        $formFactory = new DeleteModalForm($this->translator);

        $form = $formFactory->create([$this, 'addressDeleteWeddingAddressFormYesOnClick']);
        $form->addHidden('addressId');
        $form->addHidden('weddingId');

        return $form;
    }

    /**
     * @param SubmitButton $submitButton
     * @param ArrayHash $values
     */
    public function addressDeleteWeddingAddressFormYesOnClick(SubmitButton $submitButton, ArrayHash $values)
    {
        $presenter = $this->presenter;

        if (!$presenter->isAjax()) {
            $presenter->redirect('Address:edit', $presenter->getParameter('id'));
        }

        $this->weddingManager->updateByPrimaryKey($values->weddingId, ['addressId' => null]);

        $weddings = $this->weddingFacade->getByAddressId($values->addressId);

        $presenter->template->weddings = $weddings;

        $presenter->payload->showModal = false;

        $presenter->flashMessage('wedding_address_deleted', BasePresenter::FLASH_SUCCESS);

        $presenter->redrawControl('flashes');
        $presenter->redrawControl('weddings');
    }
}
