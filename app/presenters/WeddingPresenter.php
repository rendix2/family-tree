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
use Rendix2\FamilyTree\App\Controls\Modals\Wedding\Container\WeddingModalContainer;
use Rendix2\FamilyTree\App\Facades\WeddingFacade;
use Rendix2\FamilyTree\App\Filters\AddressFilter;
use Rendix2\FamilyTree\App\Filters\DurationFilter;
use Rendix2\FamilyTree\App\Filters\PersonFilter;
use Rendix2\FamilyTree\App\Filters\TownFilter;
use Rendix2\FamilyTree\App\Filters\WeddingFilter;
use Rendix2\FamilyTree\App\Forms\FormJsonDataParser;
use Rendix2\FamilyTree\App\Forms\Settings\WeddingSettings;
use Rendix2\FamilyTree\App\Forms\WeddingForm;
use Rendix2\FamilyTree\App\Managers\AddressManager;
use Rendix2\FamilyTree\App\Managers\CountryManager;
use Rendix2\FamilyTree\App\Managers\PersonSettingsManager;
use Rendix2\FamilyTree\App\Managers\TownManager;
use Rendix2\FamilyTree\App\Managers\TownSettingsManager;
use Rendix2\FamilyTree\App\Managers\WeddingManager;
use Rendix2\FamilyTree\App\Model\Facades\AddressFacade;
use Rendix2\FamilyTree\App\Presenters\Traits\Country\AddCountryModal;
use Rendix2\FamilyTree\App\Presenters\Traits\Wedding\WeddingAddAddressModal;
use Rendix2\FamilyTree\App\Presenters\Traits\Wedding\WeddingAddTownModal;
use Rendix2\FamilyTree\App\Presenters\Traits\Wedding\WeddingDeleteWeddingFromEditModal;
use Rendix2\FamilyTree\App\Presenters\Traits\Wedding\WeddingDeleteWeddingFromListModal;

/**
 * Class WeddingPresenter
 *
 * @package Rendix2\FamilyTree\App\Presenters
 */
class WeddingPresenter extends BasePresenter
{
    use AddCountryModal;

    /**
     * @var AddressFacade $addressFacade
     */
    private $addressFacade;

    /**
     * @var AddressFilter $addressFilter
     */
    private $addressFilter;

    /**
     * @var AddressManager $addressManager
     */
    private $addressManager;

    /**
     * @var CountryManager $countryManager
     */
    private $countryManager;

    /**
     * @var DurationFilter $durationFilter
     */
    private $durationFilter;

    /**
     * @var PersonFilter $personFilter
     */
    private $personFilter;

    /**
     * @var PersonSettingsManager $personSettingsManager
     */
    private $personSettingsManager;

    /**
     * @var TownFilter $townFilter
     */
    private $townFilter;

    /**
     * @var TownManager $townManager
     */
    private $townManager;

    /**
     * @var TownSettingsManager $townSettingsManager
     */
    private $townSettingsManager;

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
     * @var WeddingModalContainer $weddingModalContainer
     */
    private $weddingModalContainer;

    /**
     * WeddingPresenter constructor.
     *
     * @param AddressFacade $addressFacade
     * @param AddressFilter $addressFilter
     * @param AddressManager $addressManager
     * @param CountryManager $countryManager
     * @param DurationFilter $durationFilter
     * @param PersonFilter $personFilter
     * @param PersonSettingsManager $personSettingsManager
     * @param TownFilter $townFilter
     * @param TownManager $townManager
     * @param TownSettingsManager $townSettingsManager
     * @param WeddingFacade $weddingFacade
     * @param WeddingFilter $weddingFilter
     * @param WeddingManager $weddingManager
     * @param WeddingModalContainer $weddingModalContainer
     */
    public function __construct(
        AddressFacade $addressFacade,
        AddressFilter $addressFilter,
        AddressManager $addressManager,
        CountryManager $countryManager,
        DurationFilter $durationFilter,
        PersonFilter $personFilter,
        PersonSettingsManager $personSettingsManager,
        TownFilter $townFilter,
        TownManager $townManager,
        TownSettingsManager $townSettingsManager,
        WeddingFacade $weddingFacade,
        WeddingFilter $weddingFilter,
        WeddingManager $weddingManager,
        WeddingModalContainer $weddingModalContainer
    ) {
        parent::__construct();

        $this->addressFacade = $addressFacade;
        $this->weddingFacade = $weddingFacade;

        $this->addressManager = $addressManager;
        $this->countryManager = $countryManager;
        $this->townManager = $townManager;
        $this->weddingManager = $weddingManager;

        $this->addressFilter = $addressFilter;
        $this->durationFilter = $durationFilter;
        $this->personFilter = $personFilter;
        $this->townFilter = $townFilter;
        $this->weddingFilter = $weddingFilter;

        $this->personSettingsManager = $personSettingsManager;
        $this->townSettingsManager = $townSettingsManager;

        $this->weddingModalContainer = $weddingModalContainer;
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
        $husbands = $this->personSettingsManager->getMalesPairsCached($this->translator);
        $wives = $this->personSettingsManager->getFemalesPairsCached($this->translator);
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

            $calcResult = $this->weddingManager->getRelationLength($wedding->husband, $wedding->wife, $wedding->duration, $this->translator);

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

        $formFactory = new WeddingForm($this->translator, $weddingsSettings);

        $form = $formFactory->create();
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

    public function createComponentWeddingDeleteWeddingFromEditModal()
    {
        return $this->weddingModalContainer->getWeddingDeleteWeddingFromEditModalFactory()->create();
    }

    public function createComponentWeddingDeleteWeddingFromListModal()
    {
        return $this->weddingModalContainer->getWeddingDeleteWeddingFromListModalFactory()->create();
    }

    public function createComponentWeddingAddTownModal()
    {
        return $this->weddingModalContainer->getWeddingAddTownModalFactory()->create();
    }

    public function createComponentWeddingAddAddressModal()
    {
        return $this->weddingModalContainer->getWeddingAddAddressModalFactory()->create();
    }

}
