<?php
/**
 *
 * Created by PhpStorm.
 * Filename: TownAddModalWedding.php
 * User: Tomáš Babický
 * Date: 02.12.2020
 * Time: 1:58
 */

namespace Rendix2\FamilyTree\App\Controls\Modals\Town;

use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Controls\Forms\Settings\WeddingSettings;
use Rendix2\FamilyTree\App\Controls\Forms\WeddingForm;
use Rendix2\FamilyTree\App\Model\Facades\WeddingFacade;
use Rendix2\FamilyTree\App\Model\Managers\PersonManager;
use Rendix2\FamilyTree\App\Model\Managers\TownManager;
use Rendix2\FamilyTree\App\Model\Managers\WeddingManager;
use Rendix2\FamilyTree\App\Model\Facades\AddressFacade;
use Rendix2\FamilyTree\App\Presenters\BasePresenter;

/**
 * Class TownAddWeddingModal
 *
 * @package Rendix2\FamilyTree\App\Controls\Modals\Town
 */
class TownAddWeddingModal extends Control
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
     * @var WeddingManager $weddingManager
     */
    private $weddingManager;

    /**
     * @var WeddingForm $weddingForm
     */
    private $weddingForm;

    /**
     * TownAddWeddingModal constructor.
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
        $this['townAddWeddingForm']->render();
    }

    /**
     * @param int $townId
     *
     * @return void
     */
    public function handleTownAddWedding($townId)
    {
        $presenter = $this->presenter;

        if (!$presenter->isAjax()) {
            $presenter->redirect('Town:edit', $presenter->getParameter('id'));
        }

        $males = $this->personManager->select()->getManager()->getMalesPairs();
        $females = $this->personManager->select()->getManager()->getFemalesPairs();
        $towns = $this->townManager->select()->getManager()->getAllPairs();
        $addresses = $this->addressFacade->select()->getManager()->getByTownPairs($townId);

        $this['townAddWeddingForm-husbandId']->setItems($males);
        $this['townAddWeddingForm-wifeId']->setItems($females);
        $this['townAddWeddingForm-_townId']->setDefaultValue($townId);
        $this['townAddWeddingForm-townId']->setItems($towns)
            ->setDisabled()
            ->setDefaultValue($townId);
        $this['townAddWeddingForm-addressId']->setItems($addresses);

        $presenter->template->modalName = 'townAddWedding';

        $presenter->payload->showModal = true;

        $presenter->redrawControl('modal');
    }

    /**
     * @return Form
     */
    protected function createComponentTownAddWeddingForm()
    {
        $weddingSettings = new WeddingSettings();

        $form = $this->weddingForm->create($weddingSettings);

        $form->addHidden('_townId');

        $form->onAnchor[] = [$this, 'townAddWeddingFormAnchor'];
        $form->onValidate[] = [$this, 'townAddWeddingFormValidate'];
        $form->onSuccess[] = [$this, 'townAddWeddingFormSuccess'];

        $form->elementPrototype->setAttribute('class', 'ajax');

        return $form;
    }

    /**
     * @return void
     */
    public function townAddWeddingFormAnchor()
    {
        $presenter = $this->presenter;

        $presenter->redrawControl('modal');
    }

    /**
     * @param Form $form
     */
    public function townAddWeddingFormValidate(Form $form)
    {
        $persons = $this->personManager->select()->getManager()->getMalesPairs();

        $husbandControl = $form->getComponent('husbandId');
        $husbandControl->setItems($persons)
            ->validate();

        $persons = $this->personManager->select()->getManager()->getFemalesPairs();

        $wifeControl = $form->getComponent('wifeId');
        $wifeControl->setItems($persons)
            ->validate();

        $towns = $this->townManager->select()->getManager()->getAllPairs();

        $townHiddenControl = $form->getComponent('_townId');
        $townControl = $form->getComponent('townId');
        $townControl->setItems($towns)
            ->setValue($townHiddenControl->getValue())
            ->validate();

        $addresses = $this->addressFacade->select()->getManager()->getByTownPairs($townHiddenControl->getValue());

        $addressControl = $form->getComponent('addressId');
        $addressControl->setItems($addresses)
            ->validate();

        $form->removeComponent($townHiddenControl);
    }

    /**
     * @param Form $form
     * @param ArrayHash $values
     */
    public function townAddWeddingFormSuccess(Form $form, ArrayHash $values)
    {
        $presenter = $this->presenter;

        if (!$presenter->isAjax()) {
            $presenter->redirect('Town:edit', $presenter->getParameter('id'));
        }

        $this->weddingManager->insert()->insert((array) $values);

        $weddings = $this->weddingFacade->select()->getCachedManager()->getByTownId($values->townId);

        $presenter->template->weddings = $weddings;

        $presenter->flashMessage('wedding_added', BasePresenter::FLASH_SUCCESS);

        $presenter->payload->showModal = false;

        $presenter->redrawControl('flashes');
        $presenter->redrawControl('weddings');
    }
}
