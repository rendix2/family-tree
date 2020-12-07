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
use Rendix2\FamilyTree\App\Facades\WeddingFacade;
use Rendix2\FamilyTree\App\Filters\AddressFilter;
use Rendix2\FamilyTree\App\Filters\DurationFilter;
use Rendix2\FamilyTree\App\Filters\PersonFilter;
use Rendix2\FamilyTree\App\Filters\TownFilter;
use Rendix2\FamilyTree\App\Forms\FormJsonDataParser;
use Rendix2\FamilyTree\App\Forms\Settings\WeddingSettings;
use Rendix2\FamilyTree\App\Forms\WeddingForm;
use Rendix2\FamilyTree\App\Managers\AddressManager;
use Rendix2\FamilyTree\App\Managers\CountryManager;
use Rendix2\FamilyTree\App\Managers\PersonManager;
use Rendix2\FamilyTree\App\Managers\TownManager;
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

    use WeddingDeleteWeddingFromEditModal;
    use WeddingDeleteWeddingFromListModal;

    use WeddingAddTownModal;
    use WeddingAddAddressModal;

    /**
     * @var AddressManager $addressManager
     */
    private $addressManager;

    /**
     * @var AddressFacade $addressFacade
     */
    private $addressFacade;

    /**
     * @var CountryManager $countryManager
     */
    private $countryManager;

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
     * WeddingPresenter constructor.
     *
     * @param AddressManager $addressManager
     * @param AddressFacade $addressFacade
     * @param CountryManager $countryManager
     * @param PersonManager $personManager
     * @param TownManager $townManager
     * @param WeddingFacade $weddingFacade
     * @param WeddingManager $manager
     */
    public function __construct(
        AddressManager $addressManager,
        AddressFacade $addressFacade,
        CountryManager $countryManager,
        PersonManager $personManager,
        TownManager $townManager,
        WeddingFacade $weddingFacade,
        WeddingManager $manager
    ) {
        parent::__construct();

        $this->addressManager = $addressManager;
        $this->addressFacade = $addressFacade;
        $this->countryManager = $countryManager;
        $this->personManager = $personManager;
        $this->townManager = $townManager;
        $this->weddingManager = $manager;
        $this->weddingFacade = $weddingFacade;
    }

    /**
     * @return void
     */
    public function renderDefault()
    {
        $weddings = $this->weddingFacade->getAllCached();

        $this->template->weddings = $weddings;

        $this->template->addFilter('address', new AddressFilter());
        $this->template->addFilter('duration', new DurationFilter($this->getTranslator()));
        $this->template->addFilter('person', new PersonFilter($this->getTranslator(), $this->getHttpRequest()));
        $this->template->addFilter('town', new TownFilter());
    }

    /**
     * @param int|null $id weddingId
     */
    public function actionEdit($id = null)
    {
        $husbands = $this->personManager->getMalesPairsCached($this->getTranslator());
        $wives = $this->personManager->getFemalesPairsCached($this->getTranslator());
        $towns = $this->townManager->getAllPairsCached();

        $this['weddingForm-husbandId']->setItems($husbands);
        $this['weddingForm-wifeId']->setItems($wives);
        $this['weddingForm-townId']->setItems($towns);

        if ($id !== null) {
            $wedding = $this->weddingFacade->getByPrimaryKeyCached($id);

            if (!$wedding) {
                $this->error('Item not found.');
            }

            $this['weddingForm']->setDefaults((array)$wedding);

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

        $this->redrawControl('weddingFormWrapper');
        $this->redrawControl('jsFormCallback');
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

            $calcResult = $this->weddingManager->calcLengthRelation($wedding->husband, $wedding->wife, $wedding->duration, $this->getTranslator());

            $wifeWeddingAge = $calcResult['femaleRelationAge'];
            $husbandWeddingAge = $calcResult['maleRelationAge'];
            $relationLength = $calcResult['relationLength'];

            $this->template->wife = $wedding->wife;
            $this->template->wifeWeddingAge = $wifeWeddingAge;

            $this->template->husband = $wedding->husband;
            $this->template->husbandWeddingAge = $husbandWeddingAge;
        }

        $this->template->relationLength = $relationLength;

        $this->template->addFilter('person', new PersonFilter($this->getTranslator(), $this->getHttpRequest()));
    }

    /**
     * @return Form
     */
    protected function createComponentWeddingForm()
    {
        $weddingsSettings = new WeddingSettings();
        $weddingsSettings->selectTownHandle = $this->link('weddingFormSelectTown!');

        $formFactory = new WeddingForm($this->getTranslator(), $weddingsSettings);

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
}
