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
use Dibi\Row;
use Exception;
use Nette\Application\UI\Form;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Facades\Person2AddressFacade;
use Rendix2\FamilyTree\App\Facades\Person2JobFacade;
use Rendix2\FamilyTree\App\Facades\PersonFacade;
use Rendix2\FamilyTree\App\Facades\RelationFacade;
use Rendix2\FamilyTree\App\Facades\WeddingFacade;
use Rendix2\FamilyTree\App\Filters\AddressFilter;
use Rendix2\FamilyTree\App\Filters\DurationFilter;
use Rendix2\FamilyTree\App\Filters\GenusFilter;
use Rendix2\FamilyTree\App\Filters\JobFilter;
use Rendix2\FamilyTree\App\Filters\NameFilter;
use Rendix2\FamilyTree\App\Filters\PersonFilter;
use Rendix2\FamilyTree\App\Filters\SourceFilter;
use Rendix2\FamilyTree\App\Filters\TownFilter;
use Rendix2\FamilyTree\App\Forms\PersonForm;
use Rendix2\FamilyTree\App\Managers\AddressManager;
use Rendix2\FamilyTree\App\Managers\CountryManager;
use Rendix2\FamilyTree\App\Managers\GenusManager;
use Rendix2\FamilyTree\App\Managers\JobManager;
use Rendix2\FamilyTree\App\Managers\NameManager;
use Rendix2\FamilyTree\App\Managers\NoteHistoryManager;
use Rendix2\FamilyTree\App\Managers\Person2AddressManager;
use Rendix2\FamilyTree\App\Managers\Person2JobManager;
use Rendix2\FamilyTree\App\Managers\PersonManager;
use Rendix2\FamilyTree\App\Managers\RelationManager;
use Rendix2\FamilyTree\App\Managers\SourceManager;
use Rendix2\FamilyTree\App\Managers\SourceTypeManager;
use Rendix2\FamilyTree\App\Managers\TownManager;
use Rendix2\FamilyTree\App\Managers\WeddingManager;
use Rendix2\FamilyTree\App\Model\Entities\PersonEntity;
use Rendix2\FamilyTree\App\Model\Facades\AddressFacade;
use Rendix2\FamilyTree\App\Model\Facades\HistoryNoteFacade;
use Rendix2\FamilyTree\App\Model\Facades\NameFacade;
use Rendix2\FamilyTree\App\Model\Facades\SourceFacade;
use Rendix2\FamilyTree\App\Presenters\Traits\Country\AddCountryModal;
use Rendix2\FamilyTree\App\Presenters\Traits\Job\AddJobModal;
use Rendix2\FamilyTree\App\Presenters\Traits\Person\PersonAddAddressModal;
use Rendix2\FamilyTree\App\Presenters\Traits\Person\PersonAddGenusModal;
use Rendix2\FamilyTree\App\Presenters\Traits\Person\PersonAddHusbandModal;
use Rendix2\FamilyTree\App\Presenters\Traits\Person\PersonAddParentPartnerFemaleModal;
use Rendix2\FamilyTree\App\Presenters\Traits\Person\PersonAddParentPartnerMaleModal;
use Rendix2\FamilyTree\App\Presenters\Traits\Person\PersonAddPartnerMaleModal;
use Rendix2\FamilyTree\App\Presenters\Traits\Person\PersonAddPartnerFemaleModal;
use Rendix2\FamilyTree\App\Presenters\Traits\Person\PersonAddPersonAddressModal;
use Rendix2\FamilyTree\App\Presenters\Traits\Person\PersonAddPersonJobModal;
use Rendix2\FamilyTree\App\Presenters\Traits\Person\PersonAddPersonNameModal;
use Rendix2\FamilyTree\App\Presenters\Traits\Person\PersonAddPersonSourceModal;
use Rendix2\FamilyTree\App\Presenters\Traits\Person\PersonAddTownModal;
use Rendix2\FamilyTree\App\Presenters\Traits\Person\PersonAddWifeModal;
use Rendix2\FamilyTree\App\Presenters\Traits\PersonJob\AddPersonJobModal;
use Rendix2\FamilyTree\App\Presenters\Traits\SourceType\AddSourceTypeModal;
use Rendix2\FamilyTree\App\Presenters\Traits\Wedding\AddWeddingModal;
use Rendix2\FamilyTree\App\Presenters\Traits\Person\PersonDeleteEditModal;
use Rendix2\FamilyTree\App\Presenters\Traits\Person\PersonDeleteListModal;
use Rendix2\FamilyTree\App\Presenters\Traits\Person\PersonAddBrotherModal;
use Rendix2\FamilyTree\App\Presenters\Traits\Person\PersonAddDaughterModal;
use Rendix2\FamilyTree\App\Presenters\Traits\Person\PersonAddSisterModal;
use Rendix2\FamilyTree\App\Presenters\Traits\Person\PersonAddSonModal;
use Rendix2\FamilyTree\App\Presenters\Traits\Person\PersonDeletePersonAddressModal;
use Rendix2\FamilyTree\App\Presenters\Traits\Person\PersonDeleteBrotherModal;
use Rendix2\FamilyTree\App\Presenters\Traits\Person\PersonDeleteDaughterModal;
use Rendix2\FamilyTree\App\Presenters\Traits\Person\PersonDeleteGenusModal;
use Rendix2\FamilyTree\App\Presenters\Traits\Person\PersonDeleteHistoryNoteModal;
use Rendix2\FamilyTree\App\Presenters\Traits\Person\PersonDeletePersonJobModal;
use Rendix2\FamilyTree\App\Presenters\Traits\Person\PersonDeleteNameModal;
use Rendix2\FamilyTree\App\Presenters\Traits\Person\PersonDeleteRelationModal;
use Rendix2\FamilyTree\App\Presenters\Traits\Person\PersonDeleteRelationParentModal;
use Rendix2\FamilyTree\App\Presenters\Traits\Person\PersonDeleteSisterModal;
use Rendix2\FamilyTree\App\Presenters\Traits\Person\PersonDeleteSonModal;
use Rendix2\FamilyTree\App\Presenters\Traits\Person\PersonDeleteSourceModal;
use Rendix2\FamilyTree\App\Presenters\Traits\Person\PersonDeleteWeddingModal;
use Rendix2\FamilyTree\App\Presenters\Traits\Person\PersonDeleteWeddingParentModal;
use Rendix2\FamilyTree\App\Presenters\Traits\Person\PersonPrepareMethods;

/**
 * Class PersonPresenter
 *
 * @package Rendix2\FamilyTree\App\Presenters
 */
class PersonPresenter extends BasePresenter
{
    use PersonDeleteEditModal;
    use PersonDeleteListModal;

    use AddCountryModal;
    use PersonAddTownModal;
    use PersonAddAddressModal;

    use PersonAddGenusModal;
    use PersonDeleteGenusModal;

    use PersonAddBrotherModal;
    use PersonDeleteBrotherModal;

    use PersonAddSisterModal;
    use PersonDeleteSisterModal;

    use PersonAddSonModal;
    use PersonDeleteSonModal;

    use PersonAddDaughterModal;
    use PersonDeleteDaughterModal;

    use PersonPrepareMethods;

    use PersonAddPersonNameModal;
    use PersonDeleteNameModal;

    use PersonAddPersonAddressModal;
    use PersonDeletePersonAddressModal;

    use AddJobModal;
    use PersonAddPersonJobModal;
    use PersonDeletePersonJobModal;

    use AddWeddingModal;
    use PersonAddHusbandModal;
    use PersonAddWifeModal;
    use PersonDeleteWeddingModal;
    use PersonDeleteWeddingParentModal;

    use PersonAddPartnerMaleModal;
    use PersonAddPartnerFemaleModal;

    use PersonAddParentPartnerMaleModal;
    use PersonAddParentPartnerFemaleModal;

    use PersonDeleteRelationModal;
    use PersonDeleteRelationParentModal;

    use PersonAddPersonSourceModal;
    use PersonDeleteSourceModal;

    use AddSourceTypeModal;

    use PersonDeleteHistoryNoteModal;

    use AddPersonJobModal;

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
     * @var GenusManager $genusManager
     */
    private $genusManager;

    /**
     * @var JobManager $jobManager
     */
    private $jobManager;

    /**
     * @var PersonManager $personManager
     */
    private $personManager;

    /**
     * @var PersonFacade $personFacade
     */
    private $personFacade;

    /**
     * @var Person2AddressFacade $person2AddressFacade
     */
    private $person2AddressFacade;

    /**
     * @var Person2AddressManager $person2AddressManager
     */
    private $person2AddressManager;

    /**
     * @var Person2JobFacade $person2JobFacade
     */
    private $person2JobFacade;

    /**
     * @var Person2JobManager $person2JobManager
     */
    private $person2JobManager;

    /**
     * @var NameFacade $nameFacade
     */
    private $nameFacade;

    /**
     * @var NameManager $nameManager
     */
    private $nameManager;

    /**
     * @var NoteHistoryManager $historyNoteManager
     */
    private $historyNoteManager;

    /**
     * @var HistoryNoteFacade $historyNoteFacade
     */
    private $historyNoteFacade;

    /**
     * @var TownManager $townManager
     */
    private $townManager;

    /**
     * @var RelationFacade $relationFacade
     */
    private $relationFacade;

    /**
     * @var RelationManager $relationManager
     */
    private $relationManager;

    /**
     * @var SourceFacade $sourceFacade
     */
    private $sourceFacade;

    /**
     * @var SourceManager $sourceManager
     */
    private $sourceManager;

    /**
     * @var SourceTypeManager $sourceTypeManager
     */
    private $sourceTypeManager;

    /**
     * @var WeddingFacade $weddingFacade
     */
    private $weddingFacade;

    /**
     * @var WeddingManager $weddingManager
     */
    private $weddingManager;

    /**
     * @var Row $person
     */
    private $person;

    /**
     * PersonPresenter constructor.
     *
     * @param AddressManager $addressManager
     * @param AddressFacade $addressFacade
     * @param GenusManager $genusManager
     * @param HistoryNoteFacade $historyNoteFacade
     * @param JobManager $jobManager
     * @param NameFacade $nameFacade
     * @param NameManager $namesManager
     * @param NoteHistoryManager $historyNoteManager
     * @param PersonFacade $personFacade
     * @param Person2AddressFacade $person2AddressFacade
     * @param Person2AddressManager $person2AddressManager
     * @param Person2JobFacade $person2JobFacade
     * @param Person2JobManager $person2JobManager
     * @param PersonManager $personManager
     * @param TownManager $townManager
     * @param RelationFacade $relationFacade
     * @param RelationManager $relationManager
     * @param SourceFacade $sourceFacade
     * @param SourceManager $sourceManager
     * @param SourceTypeManager $sourceTypeManager
     * @param WeddingFacade $weddingFacade
     * @param WeddingManager $weddingManager
     */
    public function __construct(
        AddressManager $addressManager,
        AddressFacade $addressFacade,
        CountryManager $countryManager,
        GenusManager $genusManager,
        HistoryNoteFacade $historyNoteFacade,
        JobManager $jobManager,
        NameFacade $nameFacade,
        NameManager $namesManager,
        NoteHistoryManager $historyNoteManager,
        PersonFacade $personFacade,
        Person2AddressFacade $person2AddressFacade,
        Person2AddressManager $person2AddressManager,
        Person2JobFacade $person2JobFacade,
        Person2JobManager $person2JobManager,
        PersonManager $personManager,
        TownManager $townManager,
        RelationFacade $relationFacade,
        RelationManager $relationManager,
        SourceFacade $sourceFacade,
        SourceManager $sourceManager,
        SourceTypeManager $sourceTypeManager,
        WeddingFacade $weddingFacade,
        WeddingManager $weddingManager
    ) {
        parent::__construct();

        $this->addressManager = $addressManager;
        $this->addressFacade = $addressFacade;
        $this->countryManager = $countryManager;
        $this->historyNoteFacade = $historyNoteFacade;
        $this->genusManager = $genusManager;
        $this->jobManager = $jobManager;
        $this->personFacade = $personFacade;
        $this->personManager = $personManager;
        $this->person2AddressFacade = $person2AddressFacade;
        $this->person2AddressManager = $person2AddressManager;
        $this->person2JobFacade = $person2JobFacade;
        $this->person2JobManager = $person2JobManager;
        $this->townManager = $townManager;
        $this->nameFacade = $nameFacade;
        $this->nameManager = $namesManager;
        $this->historyNoteManager = $historyNoteManager;
        $this->relationFacade = $relationFacade;
        $this->relationManager = $relationManager;
        $this->sourceFacade = $sourceFacade;
        $this->sourceManager = $sourceManager;
        $this->sourceTypeManager = $sourceTypeManager;
        $this->weddingFacade = $weddingFacade;
        $this->weddingManager = $weddingManager;
    }

    /**
     * @param int|null $id
     */
    public function actionEdit($id = null)
    {
        $males = $this->personManager->getMalesPairsCached($this->getTranslator());
        $females = $this->personManager->getFemalesPairsCached($this->getTranslator());
        $genuses = $this->genusManager->getPairsCached('surname');
        $towns = $this->townManager->getAllPairsCached();
        $addresses = $this->addressFacade->getPairsCached();

        // parents
        $this['form-fatherId']->setItems($males);
        $this['form-motherId']->setItems($females);

        // genus
        $this['form-genusId']->setItems($genuses);

        // towns
        $this['form-birthTownId']->setItems($towns);
        $this['form-deathTownId']->setItems($towns);
        $this['form-gravedTownId']->setItems($towns);

        // addresses
        $this['form-birthAddressId']->setItems($addresses);
        $this['form-deathAddressId']->setItems($addresses);
        $this['form-gravedAddressId']->setItems($addresses);

        if ($id !== null) {
           $person = $this->personFacade->getByPrimaryKeyCached($id);

            if (!$person) {
                $this->error('Item not found.');
            }

            if ($person->father) {
                $this['form-fatherId']->setDefaultValue($person->father->id);
            }

            if ($person->mother) {
                $this['form-motherId']->setDefaultValue($person->mother->id);
            }

            if ($person->genus) {
                $this['form-genusId']->setDefaultValue($person->genus->id);
            }

            if ($person->birthTown) {
                $this['form-birthTownId']->setDefaultValue($person->birthTown->id);
            }

            if ($person->deathTown) {
                $this['form-deathTownId']->setDefaultValue($person->deathTown->id);
            }

            if ($person->gravedTown) {
                $this['form-gravedTownId']->setDefaultValue($person->gravedTown->id);
            }

            if ($person->birthAddress) {
                $this['form-birthAddressId']->setDefaultValue($person->birthAddress->id);
            }

            if ($person->deathAddress) {
                $this['form-deathAddressId']->setDefaultValue($person->deathAddress->id);
            }

            if ($person->gravedAddress) {
                $this['form-gravedAddressId']->setDefaultValue($person->gravedAddress->id);
            }

            $this['form']->setDefaults((array)$person);
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
        } else {
            $person = $this->personFacade->getByPrimaryKeyCached($id);

            $father = $person->father;
            $mother = $person->mother;

            $addresses = $this->person2AddressFacade->getByLeftCached($id);

            $names = $this->nameFacade->getByPersonCached($id);

            $jobs = $this->person2JobFacade->getByLeftCached($id);

            $historyNotes = $this->historyNoteFacade->getByPersonCached($person->id);

            if (!isset($this->template->genusPersons) && $person->genus) {
                $genusPersons = $this->personFacade->getByGenusIdCached($person->genus->id);

                $this->template->genusPersons = $genusPersons;
            } else if (!$this->isAjax()) {
                $this->template->genusPersons = [];
            }

            $sons = $this->personManager->getSonsByPersonCached($person);
            $daughters = $this->personManager->getDaughtersByPersonCached($person);

            $age = $this->personManager->calculateAgeByPerson($person);

            $sources = $this->sourceFacade->getByPersonIdCached($id);
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

        $this->prepareWeddings($id);
        $this->prepareRelations($id);

        $this->prepareParentsRelations($father, $mother);
        $this->prepareParentsWeddings($father, $mother);

        $this->prepareBrothersAndSisters($id, $father, $mother);

        $this->template->addFilter('address', new AddressFilter());
        $this->template->addFilter('job', new JobFilter());
        $this->template->addFilter('genus', new GenusFilter());
        $this->template->addFilter('person', new PersonFilter($this->getTranslator(), $this->getHttpRequest()));
        $this->template->addFilter('source', new SourceFilter());
        $this->template->addFilter('name', new NameFilter());
        $this->template->addFilter('town', new TownFilter());
        $this->template->addFilter('duration', new DurationFilter($this->getTranslator()));
    }

    /**
     * @param int $id
     */
    public function actionView($id)
    {
        $person = $this->personFacade->getByPrimaryKeyCached($id);

        if (!$person) {
            $this->error('Item not found.');
        }

        $males = $this->personManager->getMalesPairsCached($this->getTranslator());
        $females = $this->personManager->getFemalesPairsCached($this->getTranslator());
        $genuses = $this->genusManager->getPairsCached('surname');
        $towns = $this->townManager->getAllPairsCached();
        $addresses = $this->addressFacade->getPairsCached();

        // parents
        $this['form-fatherId']->setItems($males);
        $this['form-motherId']->setItems($females);

        // genus
        $this['form-genusId']->setItems($genuses);

        // towns
        $this['form-birthTownId']->setItems($towns);
        $this['form-deathTownId']->setItems($towns);
        $this['form-gravedTownId']->setItems($towns);

        // addresses
        $this['form-birthAddressId']->setItems($addresses);
        $this['form-deathAddressId']->setItems($addresses);
        $this['form-gravedAddressId']->setItems($addresses);

        foreach ($this['form']->getComponents() as $component) {
            $component->setDisabled();
        }

        if ($person->father) {
            $this['form-fatherId']->setDefaultValue($person->father->id);
        }

        if ($person->mother) {
            $this['form-motherId']->setDefaultValue($person->mother->id);
        }

        if ($person->genus) {
            $this['form-genusId']->setDefaultValue($person->genus->id);
        }

        if ($person->birthTown) {
            $this['form-birthTownId']->setDefaultValue($person->birthTown->id);
        }

        if ($person->deathTown) {
            $this['form-deathTownId']->setDefaultValue($person->deathTown->id);
        }

        if ($person->gravedTown) {
            $this['form-gravedTownId']->setDefaultValue($person->deathTown->id);
        }

        if ($person->birthAddress) {
            $this['form-birthAddressId']->setDefaultValue($person->birthTown->id);
        }

        if ($person->deathAddress) {
            $this['form-deathAddressId']->setDefaultValue($person->deathAddress->id);
        }

        if ($person->gravedAddress) {
            $this['form-gravedAddressId']->setDefaultValue($person->deathAddress->id);
        }

        $this['form']->setDefaults((array)$person);
    }

    /**
     * @param int $id personId
     *
     * @throws Exception
     */
    public function renderView($id)
    {
        $person = $this->personFacade->getByPrimaryKeyCached($id);

        $father = $person->father;
        $mother = $person->mother;

        $addresses = $this->person2AddressFacade->getByLeftCached($id);

        $names = $this->nameFacade->getByPersonCached($id);

        $jobs = $this->person2JobFacade->getByLeftCached($id);

        $historyNotes = $this->historyNoteManager->getByPerson($person->id);

        $genusPersons = [];

        if ($person->genus) {
            $genusPersons = $this->personManager->getByGenusIdCached($person->genus->id);
        }

        $sons = $this->personManager->getSonsByPersonCached($person);
        $daughters = $this->personManager->getDaughtersByPersonCached($person);

        $age = $this->personManager->calculateAgeByPerson($person);

        $sources = $this->sourceFacade->getByPersonIdCached($id);

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

        $this->prepareWeddings($id);
        $this->prepareRelations($id);

        $this->prepareParentsRelations($father, $mother);
        $this->prepareParentsWeddings($father, $mother);

        $this->prepareBrothersAndSisters($id, $father, $mother);

        $this->template->addFilter('address', new AddressFilter());
        $this->template->addFilter('job', new JobFilter());
        $this->template->addFilter('person', new PersonFilter($this->getTranslator(), $this->getHttpRequest()));
        $this->template->addFilter('name', new NameFilter());
        $this->template->addFilter('town', new TownFilter());
        $this->template->addFilter('duration', new DurationFilter($this->getTranslator()));
    }

    /**
     * @return void
     */
    public function renderDefault()
    {
        $persons = $this->personFacade->getAllCached();

        $this->template->persons = $persons;

        $this->template->addFilter('person', new PersonFilter($this->getTranslator(), $this->getHttpRequest()));
    }

    /**
     * @return Form
     */
    public function createComponentForm()
    {
        $formFactory = new PersonForm($this->getTranslator());

        $form = $formFactory->create();
        $form->onSuccess[] = [$this, 'saveForm'];

        return $form;
    }

    /**
     * @param Form $form
     * @param ArrayHash $values
     */
    public function saveForm(Form $form, ArrayHash $values)
    {
        $id = $this->getParameter('id');

        if ($id) {
            $person = $this->personFacade->getByPrimaryKey($id);

            if ($person->note !== $values->note) {
                $historyNoteData = [
                    'personId' => $id,
                    'text' => $values->note,
                    'date' => new DateTime()
                ];

                $this->historyNoteManager->add($historyNoteData);
            }

            $this->personManager->updateByPrimaryKey($id, $values);

            $this->flashMessage('person_saved', self::FLASH_SUCCESS);
        } else {
            $id = $this->personManager->add($values);

            $this->flashMessage('person_added', self::FLASH_SUCCESS);
        }

        $this->redirect('Person:edit', $id);
    }
}
