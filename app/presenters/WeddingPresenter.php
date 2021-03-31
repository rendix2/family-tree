<?php
/**
 *
 * Created by PhpStorm.
 * Filename: WeddingPresenter.php
 * User: Tomáš Babický
 * Date: 29.08.2020
 * Time: 1:34
 */

namespace Rendix2\FamilyTree\App\Presenters;

use Nette\Application\UI\Form;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Controls\Forms\Helpers\FormJsonDataParser;
use Rendix2\FamilyTree\App\Controls\Forms\Settings\WeddingSettings;
use Rendix2\FamilyTree\App\Controls\Forms\WeddingForm;
use Rendix2\FamilyTree\App\Controls\Modals\Wedding\Container\WeddingModalContainer;
use Rendix2\FamilyTree\App\Controls\Modals\Wedding\WeddingAddAddressModal;
use Rendix2\FamilyTree\App\Controls\Modals\Wedding\WeddingAddCountryModal;
use Rendix2\FamilyTree\App\Controls\Modals\Wedding\WeddingAddTownModal;
use Rendix2\FamilyTree\App\Controls\Modals\Wedding\WeddingDeleteWeddingFromEditModal;
use Rendix2\FamilyTree\App\Controls\Modals\Wedding\WeddingDeleteWeddingFromListModal;
use Rendix2\FamilyTree\App\Facades\WeddingFacade;


use Rendix2\FamilyTree\App\Managers\PersonSettingsManager;
use Rendix2\FamilyTree\App\Managers\TownSettingsManager;
use Rendix2\FamilyTree\App\Managers\WeddingManager;
use Rendix2\FamilyTree\App\Model\Facades\AddressFacade;
use Rendix2\FamilyTree\App\Services\RelationLengthService;

/**
 * Class WeddingPresenter
 *
 * @package Rendix2\FamilyTree\App\Presenters
 */
class WeddingPresenter extends BasePresenter
{
    /**
     * @var AddressFacade $addressFacade
     */
    private $addressFacade;

    /**
     * @var PersonSettingsManager $personSettingsManager
     */
    private $personSettingsManager;

    /**
     * @var RelationLengthService $relationLengthService
     */
    private $relationLengthService;

    /**
     * @var TownSettingsManager $townSettingsManager
     */
    private $townSettingsManager;

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
     * @var WeddingModalContainer $weddingModalContainer
     */
    private $weddingModalContainer;

    /**
     * WeddingPresenter constructor.
     *
     * @param AddressFacade         $addressFacade
     * @param RelationLengthService $relationLengthService
     * @param PersonSettingsManager $personSettingsManager
     * @param TownSettingsManager   $townSettingsManager
     * @param WeddingFacade         $weddingFacade
     * @param WeddingForm           $weddingForm
     * @param WeddingManager        $weddingManager
     * @param WeddingModalContainer $weddingModalContainer
     */
    public function __construct(
        AddressFacade $addressFacade,
        RelationLengthService $relationLengthService,
        PersonSettingsManager $personSettingsManager,
        TownSettingsManager $townSettingsManager,
        WeddingFacade $weddingFacade,
        WeddingForm $weddingForm,
        WeddingManager $weddingManager,
        WeddingModalContainer $weddingModalContainer
    ) {
        parent::__construct();

        $this->weddingModalContainer = $weddingModalContainer;

        $this->addressFacade = $addressFacade;
        $this->weddingFacade = $weddingFacade;

        $this->weddingForm = $weddingForm;

        $this->weddingManager = $weddingManager;

        $this->personSettingsManager = $personSettingsManager;
        $this->townSettingsManager = $townSettingsManager;

        $this->relationLengthService = $relationLengthService;
    }

    /**
     * @return void
     */
    public function renderDefault()
    {
        $weddings = $this->weddingFacade->getAllCached();

        $this->template->weddings = $weddings;
    }

    /**
     * @param int $townId
     * @param string $formData
     */
    public function handleWeddingFormSelectTown($townId, $formData)
    {
        if (!$this->isAjax()) {
            $this->redirect('Wedding:edit', $this->getParameter('id'));
        }

        $formDataParsed = FormJsonDataParser::parse($formData);
        unset($formDataParsed['addressId']);

        if ($townId) {
            $addresses = $this->addressFacade->getByTownPairs($townId);

            $this['weddingForm-addressId']->setItems($addresses);
            $this['weddingForm-townId']->setDefaultValue($townId);
        } else {
            $this['weddingForm-addressId']->setItems([]);
            $this['weddingForm-townId']->setDefaultValue(null);
        }

        $this['weddingForm']->setDefaults($formDataParsed);

        $this->payload->snippets = [
            $this['weddingForm-addressId']->getHtmlId() => (string) $this['weddingForm-addressId']->getControl(),
        ];

        $this->redrawControl('jsFormCallback');
    }

    /**
     * @param int|null $id weddingId
     */
    public function actionEdit($id = null)
    {
        $husbands = $this->personSettingsManager->getMalesPairsCached();
        $wives = $this->personSettingsManager->getFemalesPairsCached();
        $towns = $this->townSettingsManager->getAllPairsCached();

        $this['weddingForm-husbandId']->setItems($husbands);
        $this['weddingForm-wifeId']->setItems($wives);
        $this['weddingForm-townId']->setItems($towns);

        if ($id !== null) {
            $wedding = $this->weddingFacade->getByPrimaryKeyCached($id);

            if (!$wedding) {
                $this->error('Item not found.');
            }

            $this['weddingForm']->setDefaults((array) $wedding);

            $this['weddingForm-husbandId']->setDefaultValue($wedding->husband->id);
            $this['weddingForm-wifeId']->setDefaultValue($wedding->wife->id);

            if ($wedding->town) {
                $this['weddingForm-townId']->setDefaultValue($wedding->town->id);

                if ($wedding->address) {
                    $addresses = $this->addressFacade->getByTownPairs($wedding->town->id);

                    $this['weddingForm-addressId']->setItems($addresses)
                        ->setDefaultValue($wedding->address->id);
                }
            }

            $this['weddingForm-dateSince']->setDefaultValue($wedding->duration->dateSince);
            $this['weddingForm-dateTo']->setDefaultValue($wedding->duration->dateTo);
            $this['weddingForm-untilNow']->setDefaultValue($wedding->duration->untilNow);
        }
    }

    /**
     * @param int|null $id weddingId
     */
    public function renderEdit($id = null)
    {
        if ($id === null) {
            $wedding = null;

            $wife = null;
            $wifeWeddingAge = null;
            $husband = null;
            $husbandWeddingAge = null;
            $relationLength = null;

            $this->template->wife = null;
            $this->template->wifeWeddingAge = null;

            $this->template->husband = null;
            $this->template->husbandWeddingAge = null;
        } else {
            $wedding = $this->weddingFacade->getByPrimaryKeyCached($id);

            if (!$wedding) {
                $this->error('Item not found.');
            }

            $calcResult = $this->relationLengthService->getRelationLength(
                $wedding->husband,
                $wedding->wife,
                $wedding->duration
            );

            $wifeWeddingAge = $calcResult['femaleRelationAge'];
            $husbandWeddingAge = $calcResult['maleRelationAge'];
            $relationLength = $calcResult['relationLength'];

            $this->template->wife = $wedding->wife;
            $this->template->wifeWeddingAge = $wifeWeddingAge;

            $this->template->husband = $wedding->husband;
            $this->template->husbandWeddingAge = $husbandWeddingAge;
        }

        $this->template->relationLength = $relationLength;
        $this->template->wedding = $wedding;
    }

    /**
     * @return Form
     */
    protected function createComponentWeddingForm()
    {
        $weddingsSettings = new WeddingSettings();
        $weddingsSettings->selectTownHandle = $this->link('weddingFormSelectTown!');

        $form = $this->weddingForm->create($weddingsSettings);

        $form->onSuccess[] = [$this, 'weddingFormSuccess'];
        $form->onValidate[] = [$this, 'weddingFormValidate'];

        return $form;
    }

    /**
     * @param Form $form
     * @param ArrayHash $values
     */
    public function weddingFormValidate(Form $form, ArrayHash $values)
    {
        $addresses = $this->addressFacade->getByTownPairs($values->townId);

        $this['weddingForm-addressId']->setItems($addresses)
            ->setDefaultValue($form->getHttpData()['addressId'])
            ->validate();

        $this['weddingForm']->setDefaults($form->getHttpData());
    }

    /**
     * @param Form $form
     * @param ArrayHash $values
     */
    public function weddingFormSuccess(Form $form, ArrayHash $values)
    {
        $id = $this->getParameter('id');

        if ($id) {
            $this->weddingManager->updateByPrimaryKey($id, $values);

            $this->flashMessage('wedding_saved', self::FLASH_SUCCESS);
        } else {
            $id = $this->weddingManager->add($values);

            $this->flashMessage('wedding_added', self::FLASH_SUCCESS);
        }

        $this->redirect('Wedding:edit', $id);
    }

    /**
     * @return WeddingAddAddressModal
     */
    public function createComponentWeddingAddAddressModal()
    {
        return $this->weddingModalContainer->getWeddingAddAddressModalFactory()->create();
    }

    /**
     * @return WeddingAddCountryModal
     */
    public function createComponentWeddingAddCountryModal()
    {
        return $this->weddingModalContainer->getWeddingAddCountryModalFactory()->create();
    }

    /**
     * @return WeddingAddTownModal
     */
    public function createComponentWeddingAddTownModal()
    {
        return $this->weddingModalContainer->getWeddingAddTownModalFactory()->create();
    }

    /**
     * @return WeddingDeleteWeddingFromEditModal
     */
    public function createComponentWeddingDeleteWeddingFromEditModal()
    {
        return $this->weddingModalContainer->getWeddingDeleteWeddingFromEditModalFactory()->create();
    }

    /**
     * @return WeddingDeleteWeddingFromListModal
     */
    public function createComponentWeddingDeleteWeddingFromListModal()
    {
        return $this->weddingModalContainer->getWeddingDeleteWeddingFromListModalFactory()->create();
    }
}
