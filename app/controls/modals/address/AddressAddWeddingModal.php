<?php
/**
 *
 * Created by PhpStorm.
 * Filename: TownAddModalWedding.php
 * User: Tomáš Babický
 * Date: 02.12.2020
 * Time: 1:58
 */

namespace Rendix2\FamilyTree\App\Controls\Modals\Address;

use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Controls\Forms\Settings\WeddingSettings;
use Rendix2\FamilyTree\App\Controls\Forms\WeddingForm;
use Rendix2\FamilyTree\App\Model\Facades\WeddingFacade;
use Rendix2\FamilyTree\App\Model\Managers\PersonManager;
use Rendix2\FamilyTree\App\Model\Managers\TownManager;
use Rendix2\FamilyTree\App\Model\Facades\AddressFacade;
use Rendix2\FamilyTree\App\Model\Managers\WeddingManager;
use Rendix2\FamilyTree\App\Presenters\BasePresenter;

/**
 * Class AddressAddWeddingModal
 *
 * @package Rendix2\FamilyTree\App\Controls\Modals\Address
 */
class AddressAddWeddingModal extends Control
{
    /**
     * @var AddressFacade $addressFacade
     */
    private $addressFacade;

    /**
     * @var PersonManager $personManager
     */
    private $personManager;

    /**
     * @var TownManager $townManager
     */
    private $townManager;

    /**
     * @var WeddingFacade $weddingFacade
     */
    private $weddingFacade;

    /**
     * @var WeddingForm $weddingForm
     */
    private $weddingForm;

    /**
     * @var WeddingManager $weddingManager
     */
    private $weddingManager;

    /**
     * AddressAddWeddingModal constructor.
     *
     * @param AddressFacade  $addressFacade
     * @param PersonManager  $personManager
     * @param TownManager    $townManager
     * @param WeddingFacade  $weddingFacade
     * @param WeddingForm    $weddingForm
     * @param WeddingManager $weddingManager
     */
    public function __construct(
        AddressFacade $addressFacade,
        PersonManager $personManager,
        TownManager $townManager,
        WeddingFacade $weddingFacade,
        WeddingForm $weddingForm,
        WeddingManager $weddingManager
    ) {
        parent::__construct();

        $this->addressFacade = $addressFacade;
        $this->personManager = $personManager;
        $this->townManager = $townManager;
        $this->weddingFacade = $weddingFacade;
        $this->weddingForm = $weddingForm;
        $this->weddingManager = $weddingManager;
    }

    public function render()
    {
        $this['addressAddWeddingForm']->render();
    }

    /**
     * @param int $townId
     * @param int $addressId
     *
     * @return void
     */
    public function handleAddressAddWedding($townId, $addressId)
    {
        $presenter = $this->presenter;

        if (!$presenter->isAjax()) {
            $presenter->redirect('Address:edit', $presenter->getParameter('id'));
        }

        $males = $this->personManager->select()->getSettingsCachedManager()->getMalesPairs();
        $females = $this->personManager->select()->getSettingsCachedManager()->getFemalesPairs();
        $towns = $this->townManager->select()->getSettingsCachedManager()->getAllPairs();
        $addresses = $this->addressFacade->select()->getCachedManager()->getAllPairs();

        $this['addressAddWeddingForm-husbandId']->setItems($males);
        $this['addressAddWeddingForm-wifeId']->setItems($females);
        $this['addressAddWeddingForm-_townId']->setDefaultValue($townId);
        $this['addressAddWeddingForm-townId']->setItems($towns)
            ->setDisabled()
            ->setDefaultValue($townId);

        $this['addressAddWeddingForm-_addressId']->setDefaultValue($addressId);
        $this['addressAddWeddingForm-addressId']->setItems($addresses)
            ->setDisabled()
            ->setDefaultValue($addressId);

        $presenter->template->modalName = 'addressAddWedding';

        $presenter->payload->showModal = true;

        $presenter->redrawControl('modal');
    }

    /**
     * @return Form
     */
    protected function createComponentAddressAddWeddingForm()
    {
        $weddingSettings = new WeddingSettings();

        $form = $this->weddingForm->create($weddingSettings);

        $form->addHidden('_addressId');
        $form->addHidden('_townId');

        $form->onAnchor[] = [$this, 'addressAddWeddingFormAnchor'];
        $form->onValidate[] = [$this, 'addressAddWeddingFormValidate'];
        $form->onSuccess[] = [$this, 'addressAddWeddingFormSuccess'];

        $form->elementPrototype->setAttribute('class', 'ajax');

        return $form;
    }

    /**
     * @return void
     */
    public function addressAddWeddingFormAnchor()
    {
        $presenter = $this->presenter;

        $presenter->redrawControl('modal');
    }

    /**
     * @param Form $form
     */
    public function addressAddWeddingFormValidate(Form $form)
    {
        $persons = $this->personManager->select()->getCachedManager()->getMalesPairs();

        $husbandControl = $form->getComponent('husbandId');
        $husbandControl->setItems($persons)
            ->validate();

        $persons = $this->personManager->select()->getCachedManager()->getFemalesPairs();

        $wifeControl = $form->getComponent('wifeId');
        $wifeControl->setItems($persons)
            ->validate();

        $towns = $this->townManager->select()->getCachedManager()->getAllPairs();

        $townHiddenControl = $form->getComponent('_townId');

        $townControl = $form->getComponent('townId');
        $townControl->setItems($towns)
            ->setValue($townHiddenControl->getValue())
            ->validate();

        $addresses = $this->addressFacade->select()->getCachedManager()->getAllPairs();

        $addressHiddenControl = $form->getComponent('_addressId');

        $addressControl = $form->getComponent('addressId');
        $addressControl->setItems($addresses)
            ->setValue($addressHiddenControl->getValue())
            ->validate();

        $form->removeComponent($addressHiddenControl);
        $form->removeComponent($townHiddenControl);
    }

    /**
     * @param Form $form
     * @param ArrayHash $values
     */
    public function addressAddWeddingFormSuccess(Form $form, ArrayHash $values)
    {
        $presenter = $this->presenter;

        if (!$presenter->isAjax()) {
            $presenter->redirect('Address:edit', $presenter->getParameter('id'));
        }

        $this->weddingManager->insert()->insert((array) $values);

        $weddings = $this->weddingFacade->select()->getCachedManager()->getByTownId($values->townId);

        $presenter->template->weddings = $weddings;

        $presenter->payload->showModal = false;

        $presenter->flashMessage('wedding_added', BasePresenter::FLASH_SUCCESS);

        $presenter->redrawControl('flashes');
        $presenter->redrawControl('weddings');
    }
}
