<?php
/**
 *
 * Created by PhpStorm.
 * Filename: PersonPresenter.php
 * User: Tomáš Babický
 * Date: 29.08.2020
 * Time: 1:56
 */

namespace Rendix2\FamilyTree\App\Presenters;

use Dibi\DateTime;
use Exception;
use Nette\Application\UI\Form;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Controls\Forms\Helpers\FormJsonDataParser;
use Rendix2\FamilyTree\App\Controls\Forms\PersonForm;
use Rendix2\FamilyTree\App\Controls\Forms\Settings\PersonSettings;
use Rendix2\FamilyTree\App\Controls\Modals\Person\Container\PersonModalContainer;
use Rendix2\FamilyTree\App\Controls\Modals\Person\PersonAddAddressModal;
use Rendix2\FamilyTree\App\Controls\Modals\Person\PersonAddBrotherModal;
use Rendix2\FamilyTree\App\Controls\Modals\Person\PersonAddCountryModal;
use Rendix2\FamilyTree\App\Controls\Modals\Person\PersonAddDaughterModal;
use Rendix2\FamilyTree\App\Controls\Modals\Person\PersonAddFileModal;
use Rendix2\FamilyTree\App\Controls\Modals\Person\PersonAddGenusModal;
use Rendix2\FamilyTree\App\Controls\Modals\Person\PersonAddHusbandModal;
use Rendix2\FamilyTree\App\Controls\Modals\Person\PersonAddJobModal;
use Rendix2\FamilyTree\App\Controls\Modals\Person\PersonAddParentPartnerFemaleModal;
use Rendix2\FamilyTree\App\Controls\Modals\Person\PersonAddParentPartnerMaleModal;
use Rendix2\FamilyTree\App\Controls\Modals\Person\PersonAddPartnerFemaleModal;
use Rendix2\FamilyTree\App\Controls\Modals\Person\PersonAddPartnerMaleModal;
use Rendix2\FamilyTree\App\Controls\Modals\Person\PersonAddPersonAddressModal;
use Rendix2\FamilyTree\App\Controls\Modals\Person\PersonAddPersonJobModal;
use Rendix2\FamilyTree\App\Controls\Modals\Person\PersonAddPersonNameModal;
use Rendix2\FamilyTree\App\Controls\Modals\Person\PersonAddPersonSourceModal;
use Rendix2\FamilyTree\App\Controls\Modals\Person\PersonAddSisterModal;
use Rendix2\FamilyTree\App\Controls\Modals\Person\PersonAddSonModal;
use Rendix2\FamilyTree\App\Controls\Modals\Person\PersonAddSourceTypeModal;
use Rendix2\FamilyTree\App\Controls\Modals\Person\PersonAddTownModal;
use Rendix2\FamilyTree\App\Controls\Modals\Person\PersonAddWifeModal;
use Rendix2\FamilyTree\App\Controls\Modals\Person\PersonDeleteBrotherModal;
use Rendix2\FamilyTree\App\Controls\Modals\Person\PersonDeleteDaughterModal;
use Rendix2\FamilyTree\App\Controls\Modals\Person\PersonDeleteFileModal;
use Rendix2\FamilyTree\App\Controls\Modals\Person\PersonDeleteGenusModal;
use Rendix2\FamilyTree\App\Controls\Modals\Person\PersonDeleteHistoryNoteModal;
use Rendix2\FamilyTree\App\Controls\Modals\Person\PersonDeletePersonAddressModal;
use Rendix2\FamilyTree\App\Controls\Modals\Person\PersonDeletePersonFromEditModal;
use Rendix2\FamilyTree\App\Controls\Modals\Person\PersonDeletePersonFromListModal;
use Rendix2\FamilyTree\App\Controls\Modals\Person\PersonDeletePersonJobModal;
use Rendix2\FamilyTree\App\Controls\Modals\Person\PersonDeletePersonNameModal;
use Rendix2\FamilyTree\App\Controls\Modals\Person\PersonDeleteRelationModal;
use Rendix2\FamilyTree\App\Controls\Modals\Person\PersonDeleteRelationParentModal;
use Rendix2\FamilyTree\App\Controls\Modals\Person\PersonDeleteSisterModal;
use Rendix2\FamilyTree\App\Controls\Modals\Person\PersonDeleteSonModal;
use Rendix2\FamilyTree\App\Controls\Modals\Person\PersonDeleteSourceModal;
use Rendix2\FamilyTree\App\Controls\Modals\Person\PersonDeleteWeddingModal;
use Rendix2\FamilyTree\App\Controls\Modals\Person\PersonDeleteWeddingParentModal;
use Rendix2\FamilyTree\App\Controls\Modals\Person\PersonShowImageModal;
use Rendix2\FamilyTree\App\Model\Entities\PersonEntity;
use Rendix2\FamilyTree\App\Model\Facades\AddressFacade;
use Rendix2\FamilyTree\App\Model\Facades\HistoryNoteFacade;
use Rendix2\FamilyTree\App\Model\Facades\NameFacade;
use Rendix2\FamilyTree\App\Model\Facades\Person2AddressFacade;
use Rendix2\FamilyTree\App\Model\Facades\Person2JobFacade;
use Rendix2\FamilyTree\App\Model\Facades\PersonFacade;
use Rendix2\FamilyTree\App\Model\Facades\SourceFacade;
use Rendix2\FamilyTree\App\Model\FileDir;
use Rendix2\FamilyTree\App\Model\Managers\FileManager;
use Rendix2\FamilyTree\App\Model\Managers\GenusManager;
use Rendix2\FamilyTree\App\Model\Managers\HistoryNoteManager;
use Rendix2\FamilyTree\App\Model\Managers\PersonManager;
use Rendix2\FamilyTree\App\Model\Managers\TownManager;
use Rendix2\FamilyTree\App\Services\PersonAgeService;
use Rendix2\FamilyTree\App\Services\PersonUpdateService;

/**
 * Class PersonPresenter
 *
 * @package Rendix2\FamilyTree\App\Presenters
 */
class PersonPresenter extends BasePresenter
{
    /**
     * @var AddressFacade $addressFacade
     */
    private $addressFacade;

    /**
     * @var string $fileDir
     */
    private $fileDir;

    /**
     * @var FileManager $fileManager
     */
    private $fileManager;

    /**
     * @var GenusManager $genusManager
     */
    private $genusManager;

    /**
     * @var HistoryNoteFacade $historyNoteFacade
     */
    private $historyNoteFacade;

    /**
     * @var HistoryNoteManager $historyNoteManager
     */
    private $historyNoteManager;

    /**
     * @var PersonAgeService $personAgeService
     */
    private $personAgeService;

    /**
     * @var PersonFacade $personFacade
     */
    private $personFacade;

    /**
     * @var PersonForm $personForm
     */
    private $personForm;
    /**
     * @var PersonManager $personManager
     */
    private $personManager;

    /**
     * @var PersonModalContainer $personModalContainer
     */
    private $personModalContainer;

    /**
     * @var Person2AddressFacade $person2AddressFacade
     */
    private $person2AddressFacade;

    /**
     * @var Person2JobFacade $person2JobFacade
     */
    private $person2JobFacade;

    /**
     * @var NameFacade $nameFacade
     */
    private $nameFacade;

    /**
     * @var SourceFacade $sourceFacade
     */
    private $sourceFacade;

    /**
     * @var TownManager $townManager
     */
    private $townManager;

    /**
     * @var PersonUpdateService $personUpdateService
     */
    private $personUpdateService;

    /**
     * PersonPresenter constructor.
     *
     * @param AddressFacade        $addressFacade
     * @param FileDir              $fileDir
     * @param FileManager          $fileManager
     * @param GenusManager         $genusManager
     * @param HistoryNoteFacade    $historyNoteFacade
     * @param NameFacade           $nameFacade
     * @param HistoryNoteManager   $historyNoteManager
     * @param Person2AddressFacade $person2AddressFacade
     * @param Person2JobFacade     $person2JobFacade
     * @param PersonFacade         $personFacade
     * @param PersonForm           $personForm
     * @param PersonModalContainer $personModalContainer
     * @param PersonAgeService     $personAgeService
     * @param PersonUpdateService  $personUpdateService
     * @param TownManager          $townManager
     * @param SourceFacade         $sourceFacade
     * @param PersonManager        $personManager
     */
    public function __construct(
        AddressFacade $addressFacade,
        FileDir $fileDir,
        FileManager $fileManager,
        GenusManager $genusManager,
        HistoryNoteFacade $historyNoteFacade,
        NameFacade $nameFacade,
        HistoryNoteManager $historyNoteManager,
        Person2AddressFacade $person2AddressFacade,
        Person2JobFacade $person2JobFacade,
        PersonFacade $personFacade,
        PersonForm $personForm,
        PersonModalContainer $personModalContainer,
        PersonAgeService $personAgeService,
        PersonUpdateService $personUpdateService,
        TownManager $townManager,
        SourceFacade $sourceFacade,
        PersonManager $personManager
    ) {
        parent::__construct();

        $this->personModalContainer = $personModalContainer;

        $this->fileDir = $fileDir->getFileDir();

        $this->addressFacade = $addressFacade;
        $this->historyNoteFacade = $historyNoteFacade;
        $this->personFacade = $personFacade;
        $this->person2AddressFacade = $person2AddressFacade;
        $this->person2JobFacade = $person2JobFacade;
        $this->nameFacade = $nameFacade;
        $this->sourceFacade = $sourceFacade;

        $this->personForm = $personForm;

        $this->personManager = $personManager;

        $this->fileManager = $fileManager;
        $this->genusManager = $genusManager;
        $this->historyNoteManager = $historyNoteManager;

        $this->personAgeService = $personAgeService;
        $this->personUpdateService = $personUpdateService;

        $this->townManager = $townManager;
    }

    /**
     * @param int $birthTownId
     * @param string $formData
     */
    public function handlePersonFormSelectBirthTown($birthTownId, $formData)
    {
        if (!$this->isAjax()) {
            $this->redirect('Person:edit', $this->getParameter('id'));
        }

        $formDataParsed = FormJsonDataParser::parse($formData);
        unset($formDataParsed['birthAddressId'], $formDataParsed['deathAddressId'], $formDataParsed['gravedAddressId']);

        if ($birthTownId) {
            $addresses = $this->addressFacade->select()->getCachedManager()->getByTownPairs($birthTownId);

            $this['personForm-birthTownId']->setDefaultValue($birthTownId);
            $this['personForm-birthAddressId']->setItems($addresses);
        } else {
            $this['personForm-birthTownId']->setDefaultValue(null);
            $this['personForm-birthAddressId']->setItems([]);
        }

        $this['personForm']->setDefaults($formDataParsed);

        $this->payload->showModal = false;
        $this->payload->snippets = [
            $this['personForm-birthAddressId']->getHtmlId() => (string) $this['personForm-birthAddressId']->getControl(),
        ];

        $this->redrawControl('jsFormCallback');
    }

    /**
     * @param int $deathTownId
     * @param string $formData
     */
    public function handlePersonFormSelectDeathTown($deathTownId, $formData)
    {
        if (!$this->isAjax()) {
            $this->redirect('Person:edit', $this->getParameter('id'));
        }

        $formDataParsed = FormJsonDataParser::parse($formData);
        unset($formDataParsed['birthAddressId'],$formDataParsed['deathAddressId'], $formDataParsed['deathAddressId']);

        if ($deathTownId) {
            $addresses = $this->addressFacade->select()->getManager()->getByTownPairs($deathTownId);

            $this['personForm-deathTownId']->setDefaultValue($deathTownId);
            $this['personForm-deathAddressId']->setItems($addresses);
        } else {
            $this['personForm-deathTownId']->setDefaultValue(null);
            $this['personForm-deathAddressId']->setItems([]);
        }

        $this['personForm']->setDefaults($formDataParsed);

        $this->payload->showModal = false;
        $this->payload->snippets = [
            $this['personForm-deathAddressId']->getHtmlId() => (string) $this['personForm-deathAddressId']->getControl(),
        ];

        $this->redrawControl('jsFormCallback');
    }

    /**
     * @param int $gravedTownId
     * @param string $formData
     */
    public function handlePersonFormSelectGravedTown($gravedTownId, $formData)
    {
        if (!$this->isAjax()) {
            $this->redirect('Person:edit', $this->getParameter('id'));
        }

        $formDataParsed = FormJsonDataParser::parse($formData);
        unset($formDataParsed['birthAddressId'], $formDataParsed['deathAddressId'], $formDataParsed['gravedAddressId']);

        if ($gravedTownId) {
            $addresses = $this->addressFacade->select()->getManager()->getByTownPairs($gravedTownId);

            $this['personForm-gravedTownId']->setDefaultValue($gravedTownId);
            $this['personForm-gravedAddressId']->setItems($addresses);
        } else {
            $this['personForm-gravedTownId']->setDefaultValue(null);
            $this['personForm-gravedAddressId']->setItems([]);
        }

        $this['personForm']->setDefaults($formDataParsed);

        $this->payload->showModal = false;
        $this->payload->snippets = [
            $this['personForm-gravedAddressId']->getHtmlId() => (string) $this['personForm-gravedAddressId']->getControl(),
        ];

        $this->redrawControl('jsFormCallback');
    }

    /**
     * @param int|null $id
     */
    public function actionEdit($id = null)
    {
        $males = $this->personManager->select()->getSettingsCachedManager()->getMalesPairs();
        $females = $this->personManager->select()->getSettingsCachedManager()->getFemalesPairs();
        $genuses = $this->genusManager->select()->getCachedManager()->getPairs('surname');
        $towns = $this->townManager->select()->getSettingsCachedManager()->getAllPairs();

        // parents
        $this['personForm-fatherId']->setItems($males);
        $this['personForm-motherId']->setItems($females);

        // genus
        $this['personForm-genusId']->setItems($genuses);

        // towns
        $this['personForm-birthTownId']->setItems($towns);
        $this['personForm-deathTownId']->setItems($towns);
        $this['personForm-gravedTownId']->setItems($towns);

        if ($id !== null) {
           $person = $this->personFacade->select()->getCachedManager()->getByPrimaryKey($id);

            if (!$person) {
                $this->error('Item not found.');
            }

            if ($person->father) {
                $this['personForm-fatherId']->setDefaultValue($person->father->id);
            }

            if ($person->mother) {
                $this['personForm-motherId']->setDefaultValue($person->mother->id);
            }

            if ($person->genus) {
                $this['personForm-genusId']->setDefaultValue($person->genus->id);
            }

            if ($person->birthTown) {
                $this['personForm-birthTownId']->setDefaultValue($person->birthTown->id);

                $birthAddresses = $this->addressFacade->select()->getCachedManager()->getByTownPairs($person->birthTown->id);

                $this['personForm-birthAddressId']->setItems($birthAddresses);

                if ($person->birthAddress) {
                    $this['personForm-birthAddressId']->setDefaultValue($person->birthAddress->id);
                }
            }

            if ($person->deathTown) {
                $this['personForm-deathTownId']->setDefaultValue($person->deathTown->id);

                $deathAddresses = $this->addressFacade->select()->getCachedManager()->getByTownPairs($person->deathTown->id);

                $this['personForm-deathAddressId']->setItems($deathAddresses);

                if ($person->deathAddress) {
                    $this['personForm-deathAddressId']->setDefaultValue($person->deathAddress->id);
                }
            }

            if ($person->gravedTown) {
                $this['personForm-gravedTownId']->setDefaultValue($person->gravedTown->id);

                $gravedAddresses = $this->addressFacade->select()->getCachedManager()->getByTownPairs($person->gravedTown->id);

                $this['personForm-gravedAddressId']->setItems($gravedAddresses);

                if ($person->gravedAddress) {
                    $this['personForm-gravedAddressId']->setDefaultValue($person->gravedAddress->id);
                }
            }

            $this['personForm']->setDefaults((array) $person);
        }
    }

    /**
     * @param int|null $id personId
     *
     * @throws Exception
     */
    public function renderEdit($id = null)
    {
        if ($id === null) {
            $person = new PersonEntity([]);
            $father = null;
            $mother = null;

            $addresses = [];
            $names = [];

            $sons = [];
            $daughters = [];

            $jobs = [];

            $historyNotes = [];

            $age = null;

            $this->template->genusPersons = [];

            $sources = [];

            $files = [];
        } else {
            $person = $this->personFacade->select()->getCachedManager()->getByPrimaryKey($id);

            $father = $person->father;
            $mother = $person->mother;

            $addresses = $this->person2AddressFacade->select()->getCachedManager()->getByLeftKey($id);

            $names = $this->nameFacade->select()->getCachedManager()->getByPersonId($id);

            $jobs = $this->person2JobFacade->select()->getCachedManager()->getByLeftKey($id);

            $historyNotes = $this->historyNoteFacade->select()->getCachedManager()->getByPersonId($person->id);

            if (!isset($this->template->genusPersons) && $person->genus) {
                $genusPersons = $this->personFacade->select()->getSettingsCachedManager()->getByGenusId($person->genus->id);

                $this->template->genusPersons = $genusPersons;
            } else if (!$this->isAjax()) {
                $this->template->genusPersons = [];
            }

            $sons = $this->personManager->select()->getSettingsCachedManager()->getSonsByPerson($person);
            $daughters = $this->personManager->select()->getSettingsCachedManager()->getDaughtersByPerson($person);

            $age = $this->personAgeService->calculateAgeByPerson($person);

            $sources = $this->sourceFacade->select()->getCachedManager()->getByPersonId($id);

            $files = $this->fileManager->select()->getCachedManager()->getByPersonId($id);
        }

        $this->template->addresses = $addresses;

        $this->template->names = $names;

        $this->template->father = $father;
        $this->template->mother = $mother;

        $this->template->sons = $sons;
        $this->template->daughters = $daughters;

        $this->template->jobs = $jobs;

        $this->template->historyNotes = $historyNotes;

        $this->template->age = $age;

        $this->template->person = $person;

        // $this->template->genusPersons = $genusPersons;

        $this->template->sources = $sources;

        $this->template->files = array_chunk($files, 5);
        $this->template->filesDir = $this->fileDir;
        $this->template->sep = DIRECTORY_SEPARATOR;

        $this->personUpdateService->prepareWeddings($this, $id);
        $this->personUpdateService->prepareRelations($this, $id);

        $this->personUpdateService->prepareParentsRelations(
            $this,
            $father,
            $mother
        );

        $this->personUpdateService->prepareParentsWeddings(
            $this,
            $father,
            $mother
        );

        $this->personUpdateService->prepareBrothersAndSisters(
            $this,
            $id,
            $father,
            $mother
        );
    }

    /**
     * @param int $id
     */
    public function actionView($id)
    {
        $person = $this->personFacade->select()->getCachedManager()->getByPrimaryKey($id);

        if (!$person) {
            $this->error('Item not found.');
        }

        $males = $this->personManager->select()->getSettingsCachedManager()->getMalesPairs();
        $females = $this->personManager->select()->getSettingsCachedManager()->getFemalesPairs();
        $genuses = $this->genusManager->select()->getCachedManager()->getPairs('surname');
        $towns = $this->townManager->select()->getSettingsCachedManager()->getAllPairs();
        $addresses = $this->addressFacade->select()->getCachedManager()->getAllPairs();

        // parents
        $this['personForm-fatherId']->setItems($males);
        $this['personForm-motherId']->setItems($females);

        // genus
        $this['personForm-genusId']->setItems($genuses);

        // towns
        $this['personForm-birthTownId']->setItems($towns);
        $this['personForm-deathTownId']->setItems($towns);
        $this['personForm-gravedTownId']->setItems($towns);

        // addresses
        $this['personForm-birthAddressId']->setItems($addresses);
        $this['personForm-deathAddressId']->setItems($addresses);
        $this['personForm-gravedAddressId']->setItems($addresses);

        foreach ($this['personForm']->getComponents() as $component) {
            $component->setDisabled();
        }

        if ($person->father) {
            $this['personForm-fatherId']->setDefaultValue($person->father->id);
        }

        if ($person->mother) {
            $this['personForm-motherId']->setDefaultValue($person->mother->id);
        }

        if ($person->genus) {
            $this['personForm-genusId']->setDefaultValue($person->genus->id);
        }

        if ($person->birthTown) {
            $this['personForm-birthTownId']->setDefaultValue($person->birthTown->id);

            $birthAddresses = $this->addressFacade->select()->getCachedManager()->getByTownPairs($person->birthTown->id);

            $this['personForm-birthAddressId']->setItems($birthAddresses);

            if ($person->birthAddress) {
                $this['personForm-birthAddressId']->setDefaultValue($person->birthAddress->id);
            }
        }

        if ($person->deathTown) {
            $this['personForm-deathTownId']->setDefaultValue($person->deathTown->id);

            $deathAddresses = $this->addressFacade->select()->getCachedManager()->getByTownPairs($person->deathTown->id);

            $this['personForm-deathAddressId']->setItems($deathAddresses);

            if ($person->deathAddress) {
                $this['personForm-deathAddressId']->setDefaultValue($person->deathAddress->id);
            }
        }

        if ($person->gravedTown) {
            $this['personForm-gravedTownId']->setDefaultValue($person->gravedTown->id);

            $gravedAddresses = $this->addressFacade->select()->getCachedManager()->getByTownPairs($person->gravedTown->id);

            $this['personForm-gravedAddressId']->setItems($gravedAddresses);

            if ($person->gravedAddress) {
                $this['personForm-gravedAddressId']->setDefaultValue($person->gravedAddress->id);
            }
        }

        $this['personForm']->setDefaults((array) $person);
    }

    /**
     * @param int $id personId
     *
     * @throws Exception
     */
    public function renderView($id)
    {
        $person = $this->personFacade->select()->getCachedManager()->getByPrimaryKey($id);

        $father = $person->father;
        $mother = $person->mother;

        $addresses = $this->person2AddressFacade->select()->getCachedManager()->getByLeftKey($id);

        $names = $this->nameFacade->select()->getCachedManager()->getByPersonId($id);

        $jobs = $this->person2JobFacade->select()->getCachedManager()->getByLeftKey($id);

        $historyNotes = $this->historyNoteManager->select()->getCachedManager()->getByPersonId($person->id);

        $genusPersons = [];

        if ($person->genus) {
            $genusPersons = $this->personManager->select()->getSettingsCachedManager()->getByGenusId($person->genus->id);
        }

        $sons = $this->personManager->select()->getSettingsCachedManager()->getSonsByPerson($person);
        $daughters = $this->personManager->select()->getSettingsCachedManager()->getDaughtersByPerson($person);

        $age = $this->personAgeService->calculateAgeByPerson($person);

        $files = $this->fileManager->select()->getCachedManager()->getByPersonId($id);

        $sources = $this->sourceFacade->select()->getCachedManager()->getByPersonId($id);

        $this->template->addresses = $addresses;

        $this->template->names = $names;

        $this->template->father = $father;
        $this->template->mother = $mother;

        $this->template->sons = $sons;
        $this->template->daughters = $daughters;

        $this->template->jobs = $jobs;

        $this->template->historyNotes = $historyNotes;

        $this->template->age = $age;

        $this->template->person = $person;

        $this->template->genusPersons = $genusPersons;

        $this->template->sources = $sources;

        $this->template->files = array_chunk($files, 5);
        $this->template->filesDir = $this->fileDir;
        $this->template->sep = DIRECTORY_SEPARATOR;

        $this->personUpdateService->prepareWeddings($this, $id);
        $this->personUpdateService->prepareRelations($this, $id);

        $this->personUpdateService->prepareParentsRelations($this, $father, $mother);
        $this->personUpdateService->prepareParentsWeddings($this, $father, $mother);

        $this->personUpdateService->prepareBrothersAndSisters($this, $id, $father, $mother);
    }

    /**
     * @return void
     */
    public function renderDefault()
    {
        $persons = $this->personFacade->select()->getSettingsCachedManager()->getAll();

        $this->template->persons = $persons;
    }

    /**
     * @return Form
     */
    public function createComponentPersonForm()
    {
        $personSettings = new PersonSettings();
        $personSettings->selectBirthTownHandle = $this->link('personFormSelectBirthTown!');
        $personSettings->selectDeathTownHandle = $this->link('personFormSelectDeathTown!');
        $personSettings->selectGravedTownHandle = $this->link('personFormSelectGravedTown!');

        $formFactory = $this->personForm;

        $form = $formFactory->create($personSettings);
        $form->onValidate[] = [$this, 'personFormValidate'];
        $form->onSuccess[] = [$this, 'personFormSuccess'];

        return $form;
    }


    /**
     * @param Form $form
     * @param ArrayHash $values
     */
    public function personFormValidate(Form $form, ArrayHash $values)
    {
        $personFormData = $form->getHttpData();

        $birthAddresses = $this->addressFacade->select()->getManager()->getByTownPairs($values->birthTownId);

        $this['personForm-birthAddressId']->setItems($birthAddresses)
            ->setDefaultValue($personFormData['birthAddressId']);

        $deathAddresses = $this->addressFacade->select()->getManager()->getByTownPairs($values->deathTownId);

        $this['personForm-deathAddressId']->setItems($deathAddresses)
            ->setDefaultValue($personFormData['deathAddressId']);

        $gravedAddresses = $this->addressFacade->select()->getManager()->getByTownPairs($values->gravedTownId);

        $this['personForm-gravedAddressId']->setItems($gravedAddresses)
            ->setDefaultValue($personFormData['gravedAddressId']);
    }

    /**
     * @param Form $form
     * @param ArrayHash $values
     */
    public function personFormSuccess(Form $form, ArrayHash $values)
    {
        $id = $this->getParameter('id');

        if ($id) {
            $person = $this->personFacade->select()->getCachedManager()->getByPrimaryKey($id);

            if ($person->note !== $values->note) {
                $historyNoteData = [
                    'personId' => $id,
                    'text' => $values->note,
                    'date' => new DateTime()
                ];

                $this->historyNoteManager->insert()->insert((array) $historyNoteData);
            }

            $this->personManager->update()->updateByPrimaryKey($id, (array) $values);

            $this->flashMessage('person_saved', self::FLASH_SUCCESS);
        } else {
            $id = $this->personManager->insert()->insert((array) $values);

            $this->flashMessage('person_added', self::FLASH_SUCCESS);
        }

        $this->redirect('Person:edit', $id);
    }

    /**
     * @return PersonAddAddressModal
     */
    protected function createComponentPersonAddAddressModal()
    {
        return $this->personModalContainer->getPersonAddAddressModalFactory()->create();
    }

    /**
     * @return PersonAddTownModal
     */
    protected function createComponentPersonAddTownModal()
    {
        return $this->personModalContainer->getPersonAddTownModalFactory()->create();
    }

    /**
     * @return PersonAddGenusModal
     */
    protected function createComponentPersonAddGenusModal()
    {
        return $this->personModalContainer->getPersonAddGenusModalFactory()->create();
    }

    /**
     * @return PersonDeleteGenusModal
     */
    protected function createComponentPersonDeleteGenusModal()
    {
        return $this->personModalContainer->getPersonDeleteGenusModalFactory()->create();
    }

    /**
     * @return PersonAddBrotherModal
     */
    protected function createComponentPersonAddBrotherModal()
    {
        return $this->personModalContainer->getPersonAddBrotherModalFactory()->create();
    }

    /**
     * @return PersonDeleteBrotherModal
     */
    protected function createComponentPersonDeleteBrotherModal()
    {
        return $this->personModalContainer->getPersonDeleteBrotherModalFactory()->create();
    }

    /**
     * @return PersonAddSisterModal
     */
    protected function createComponentPersonAddSisterModal()
    {
        return $this->personModalContainer->getPersonAddSisterModalFactory()->create();
    }

    /**
     * @return PersonAddSourceTypeModal
     */
    public function createComponentPersonAddSourceTypeModal()
    {
        return $this->personModalContainer->getPersonAddSourceTypeModalFactory()->create();
    }

    /**
     * @return PersonAddJobModal
     */
    public function createComponentPersonAddJobModal()
    {
        return $this->personModalContainer->getPersonAddJobModalFactory()->create();
    }

    /**
     * @return PersonDeleteSisterModal
     */
    protected function createComponentPersonDeleteSisterModal()
    {
        return $this->personModalContainer->getPersonDeleteSisterModalFactory()->create();
    }

    /**
     * @return PersonAddSonModal
     */
    protected function createComponentPersonAddSonModal()
    {
        return $this->personModalContainer->getPersonAddSonModalFactory()->create();
    }

    /**
     * @return PersonDeleteSonModal
     */
    protected function createComponentPersonDeleteSonModal()
    {
        return $this->personModalContainer->getPersonDeleteSonModalFactory()->create();
    }
    /**
     * @return PersonAddDaughterModal
     */
    protected function createComponentPersonAddDaughterModal()
    {
        return $this->personModalContainer->getPersonAddDaughterModalFactory()->create();
    }

    /**
     * @return PersonDeleteDaughterModal
     */
    protected function createComponentPersonDeleteDaughterModal()
    {
        return $this->personModalContainer->getPersonDeleteDaughterModalFactory()->create();
    }

    /**
     * @return PersonAddPersonNameModal
     */
    protected function createComponentPersonAddPersonNameModal()
    {
        return $this->personModalContainer->getPersonAddPersonNameModalFactory()->create();
    }

    /**
     * @return PersonDeletePersonNameModal
     */
    protected function createComponentPersonDeletePersonNameModal()
    {
        return $this->personModalContainer->getPersonDeletePersonNameModalFactory()->create();
    }
    /**
     * @return PersonAddPersonAddressModal
     */
    protected function createComponentPersonAddPersonAddressModal()
    {
        return $this->personModalContainer->getPersonAddPersonAddressModalFactory()->create();
    }

    /**
     * @return PersonDeletePersonAddressModal
     */
    protected function createComponentPersonDeletePersonAddressModal()
    {
        return $this->personModalContainer->getPersonDeletePersonAddressModalFactory()->create();
    }

    /**
     * @return PersonAddPersonJobModal
     */
    protected function createComponentPersonAddPersonJobModal()
    {
        return $this->personModalContainer->getPersonAddPersonJobModalFactory()->create();
    }

    /**
     * @return PersonDeletePersonFromEditModal
     */
    protected function createComponentPersonDeletePersonFromEditModal()
    {
        return $this->personModalContainer->getPersonDeletePersonFromEditModalFactory()->create();
    }

    /**
     * @return PersonDeletePersonFromListModal
     */
    protected function createComponentPersonDeletePersonFromListModal()
    {
        return $this->personModalContainer->getPersonDeletePersonFromListModalFactory()->create();
    }

    /**
     * @return PersonDeletePersonJobModal
     */
    protected function createComponentPersonDeletePersonJobModal()
    {
        return $this->personModalContainer->getPersonDeletePersonJobModalFactory()->create();
    }

    /**
     * @return PersonAddHusbandModal
     */
    protected function createComponentPersonAddHusbandModal()
    {
        return $this->personModalContainer->getPersonAddHusbandModalFactory()->create();
    }

    /**
     * @return PersonAddWifeModal
     */
    protected function createComponentPersonAddWifeModal()
    {
        return $this->personModalContainer->getPersonAddWifeModalFactory()->create();
    }

    /**
     * @return PersonDeleteWeddingModal
     */
    protected function createComponentPersonDeleteWeddingModal()
    {
        return $this->personModalContainer->getPersonDeleteWeddingModalFactory()->create();
    }

    /**
     * @return PersonDeleteWeddingParentModal
     */
    protected function createComponentPersonDeleteWeddingParentModal()
    {
        return $this->personModalContainer->getPersonDeleteWeddingParentModalFactory()->create();
    }

    /**
     * @return PersonAddPartnerMaleModal
     */
    protected function createComponentPersonAddPartnerMaleModal()
    {
        return $this->personModalContainer->getPersonAddPartnerMaleModalFactory()->create();
    }

    /**
     * @return PersonAddPartnerFemaleModal
     */
    protected function createComponentPersonAddPartnerFemaleModal()
    {
        return $this->personModalContainer->getPersonAddPartnerFemaleModalFactory()->create();
    }

    /**
     * @return PersonAddParentPartnerMaleModal
     */
    protected function createComponentPersonAddParentPartnerMaleModal()
    {
        return $this->personModalContainer->getPersonAddParentPartnerMaleModalFactory()->create();
    }

    /**
     * @return PersonAddParentPartnerFemaleModal
     */
    protected function createComponentPersonAddParentPartnerFemaleModal()
    {
        return $this->personModalContainer->getPersonAddParentPartnerFemaleModalFactory()->create();
    }

    /**
     * @return PersonDeleteRelationModal
     */
    protected function createComponentPersonDeleteRelationModal()
    {
        return $this->personModalContainer->getPersonDeleteRelationModalFactory()->create();
    }

    /**
     * @return PersonDeleteRelationParentModal
     */
    protected function createComponentPersonDeleteRelationParentModal()
    {
        return $this->personModalContainer->getPersonDeleteRelationParentModalFactory()->create();
    }

    /**
     * @return PersonAddPersonSourceModal
     */
    protected function createComponentPersonAddPersonSourceModal()
    {
        return $this->personModalContainer->getPersonAddPersonSourceModalFactory()->create();
    }

    /**
     * @return PersonDeleteSourceModal
     */
    protected function createComponentPersonDeleteSourceModal()
    {
        return $this->personModalContainer->getPersonDeleteSourceModalFactory()->create();
    }

    /**
     * @return PersonDeleteHistoryNoteModal
     */
    protected function createComponentPersonDeleteHistoryNoteModal()
    {
        return $this->personModalContainer->getPersonDeleteHistoryNoteModalFactory()->create();
    }

    /**
     * @return PersonAddFileModal
     */
    protected function createComponentPersonAddFileModal()
    {
        return $this->personModalContainer->getPersonAddFileModalFactory()->create();
    }

    /**
     * @return PersonShowImageModal
     */
    protected function createComponentPersonShowImageModal()
    {
        return $this->personModalContainer->getPersonShowImageModalFactory()->create();
    }

    /**
     * @return PersonDeleteFileModal
     */
    protected function createComponentPersonDeleteFileModal()
    {
        return $this->personModalContainer->getPersonDeleteFileModalFactory()->create();
    }

    /**
     * @return PersonAddCountryModal
     */
    protected function createComponentPersonAddCountryModal()
    {
        return $this->personModalContainer->getPersonAddCountryModalFactory()->create();
    }
}
