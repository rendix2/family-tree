<?php
/**
 *
 * Created by PhpStorm.
 * Filename: JobPresenter.php
 * User: Tomáš Babický
 * Date: 29.08.2020
 * Time: 22:29
 */

namespace Rendix2\FamilyTree\App\Presenters;

use Nette\Application\UI\Form;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Facades\Person2JobFacade;
use Rendix2\FamilyTree\App\Facades\PersonFacade;
use Rendix2\FamilyTree\App\Filters\AddressFilter;
use Rendix2\FamilyTree\App\Filters\CountryFilter;
use Rendix2\FamilyTree\App\Filters\DurationFilter;
use Rendix2\FamilyTree\App\Filters\JobFilter;
use Rendix2\FamilyTree\App\Filters\PersonFilter;
use Rendix2\FamilyTree\App\Filters\TownFilter;
use Rendix2\FamilyTree\App\Forms\FormJsonDataParser;
use Rendix2\FamilyTree\App\Forms\JobForm;
use Rendix2\FamilyTree\App\Forms\Settings\JobSettings;
use Rendix2\FamilyTree\App\Managers\AddressManager;
use Rendix2\FamilyTree\App\Managers\CountryManager;
use Rendix2\FamilyTree\App\Managers\JobManager;
use Rendix2\FamilyTree\App\Managers\JobSettingsManager;
use Rendix2\FamilyTree\App\Managers\Person2JobManager;
use Rendix2\FamilyTree\App\Managers\PersonManager;
use Rendix2\FamilyTree\App\Managers\PersonSettingsManager;
use Rendix2\FamilyTree\App\Managers\TownManager;
use Rendix2\FamilyTree\App\Managers\TownSettingsManager;
use Rendix2\FamilyTree\App\Model\Facades\AddressFacade;
use Rendix2\FamilyTree\App\Model\Facades\JobFacade;
use Rendix2\FamilyTree\App\Model\Facades\JobSettingsFacade;
use Rendix2\FamilyTree\App\Presenters\Traits\Job\JobAddAddressModal;
use Rendix2\FamilyTree\App\Presenters\Traits\Job\JobAddPersonJobModal;
use Rendix2\FamilyTree\App\Presenters\Traits\Job\JobAddTownModal;
use Rendix2\FamilyTree\App\Presenters\Traits\Job\JobDeletePersonJobModal;
use Rendix2\FamilyTree\App\Presenters\Traits\Job\JobDeleteJobFromEditModal;
use Rendix2\FamilyTree\App\Presenters\Traits\Job\JobDeleteJobFromListModal;

/**
 * Class JobPresenter
 *
 * @package Rendix2\FamilyTree\App\Presenters
 */
class JobPresenter extends BasePresenter
{
    use JobAddAddressModal;

    use JobAddTownModal;

    use JobDeleteJobFromListModal;
    use JobDeleteJobFromEditModal;

    use JobAddPersonJobModal;
    use JobDeletePersonJobModal;

    /**
     * @var AddressFacade $addressFacade
     */
    private $addressFacade;

    /**
     * @var AddressManager $addressManager
     */
    private $addressManager;

    /**
     * @var CountryManager $countryManager
     */
    private $countryManager;

    /**
     * @var JobFacade $jobFacade
     */
    private $jobFacade;

    /**
     * @var JobSettingsFacade $jobSettingsFacade
     */
    private $jobSettingsFacade;

    /**
     * @var JobManager $jobManager
     */
    private $jobManager;

    /**
     * @var JobSettingsManager $jobSettingsManager
     */
    private $jobSettingsManager;

    /**
     * @var Person2JobFacade $person2JobFacade
     */
    private $person2JobFacade;

    /**
     * @var Person2JobManager $person2JobManager
     */
    private $person2JobManager;

    /**
     * @var PersonFacade $personFacade
     */
    private $personFacade;

    /**
     * @var PersonManager $personManager
     */
    private $personManager;

    /**
     * @var PersonSettingsManager $personSettingsManager
     */
    private $personSettingsManager;

    /**
     * @var TownManager $townManager
     */
    private $townManager;

    /**
     * @var TownSettingsManager $townSettingsManager
     */
    private $townSettingsManager;

    /**
     * JobPresenter constructor.
     * @param AddressFacade $addressFacade
     * @param AddressManager $addressManager
     * @param CountryManager $countryManager
     * @param JobFacade $jobFacade
     * @param JobSettingsFacade $jobSettingsFacade
     * @param JobManager $jobManager
     * @param JobSettingsManager $jobSettingsManager
     * @param PersonManager $personManager
     * @param Person2JobFacade $person2JobFacade
     * @param Person2JobManager $person2JobManager
     * @param PersonFacade $personFacade
     * @param TownManager $townManager
     * @param TownSettingsManager $townSettingsManager
     */
    public function __construct(
        AddressFacade $addressFacade,
        AddressManager $addressManager,
        CountryManager $countryManager,
        JobFacade $jobFacade,
        JobSettingsFacade $jobSettingsFacade,
        JobManager $jobManager,
        JobSettingsManager $jobSettingsManager,
        PersonManager $personManager,
        Person2JobFacade $person2JobFacade,
        Person2JobManager $person2JobManager,
        PersonFacade $personFacade,
        TownManager $townManager,
        TownSettingsManager $townSettingsManager
    ) {
        parent::__construct();

        $this->addressFacade = $addressFacade;
        $this->addressManager = $addressManager;

        $this->countryManager = $countryManager;

        $this->jobFacade = $jobFacade;
        $this->jobSettingsFacade = $jobSettingsFacade;
        $this->jobManager = $jobManager;
        $this->jobSettingsManager = $jobSettingsManager;

        $this->personFacade = $personFacade;
        $this->personManager = $personManager;

        $this->person2JobFacade = $person2JobFacade;
        $this->person2JobManager = $person2JobManager;

        $this->townManager = $townManager;
        $this->townSettingsManager = $townSettingsManager;
    }

    /**
     * @return void
     */
    public function renderDefault()
    {
        $jobs = $this->jobSettingsFacade->getAllCached();

        $this->template->jobs = $jobs;

        $this->template->addFilter('address', new AddressFilter());
        $this->template->addFilter('country', new CountryFilter());
        $this->template->addFilter('job', new JobFilter($this->getHttpRequest()));
        $this->template->addFilter('town', new TownFilter());
    }

    /**
     * @param int $townId
     * @param string $formData
     *
     * @return void
     */
    public function handleJobFormSelectAddress($townId, $formData)
    {
        if (!$this->isAjax()) {
            $this->redirect('Job:edit', $this->getParameter('id'));
        }

        $formDataParsed = FormJsonDataParser::parse($formData);
        unset($formDataParsed['townId'], $formDataParsed['addressId']);

        $towns = $this->townSettingsManager->getPairsCached('name');

        if ($townId) {
            $this['jobForm-townId']->setItems($towns)
                ->setDefaultValue($townId);

            $addresses = $this->addressFacade->getByTownPairs($townId);

            $this['jobForm-addressId']->setItems($addresses);
        } else {
            $this['jobForm-townId']->setItems($towns)
                ->setDefaultValue(null);

            $this['jobForm-addressId']->setItems([]);
        }

        $this['jobForm']->setDefaults($formDataParsed);

        $this->payload->snippets = [
            $this['jobForm-addressId']->getHtmlId() => (string) $this['jobForm-addressId']->getControl(),
        ];

        $this->redrawControl('jsFormCallback');
    }


    /**
     * @param int|null $id jobId
     */
    public function actionEdit($id = null)
    {
        $towns = $this->townSettingsManager->getAllPairsCached();
        $addresses = $this->addressFacade->getPairsCached();

        $this['jobForm-townId']->setItems($towns);
        $this['jobForm-addressId']->setItems($addresses);

        if ($id !== null) {
            $job = $this->jobFacade->getByPrimaryKeyCached($id);

            if (!$job) {
                $this->error('Item not found.');
            }

            if ($job->town) {
                $this['jobForm-townId']->setDefaultValue($job->town->id);
            }

            if ($job->address) {
                $this['jobForm-addressId']->setDefaultValue($job->address->id);
            }

            $this['jobForm']->setDefaults((array) $job);
        }
    }

    /**
     * @param int|null $id jobId
     */
    public function renderEdit($id)
    {
        if ($id === null) {
            $persons = [];
            $job = null;
        } else {
            $persons = $this->person2JobFacade->getByRightCached($id);
            $job = $this->jobFacade->getByPrimaryKeyCached($id);
        }

        $this->template->persons = $persons;
        $this->template->job = $job;

        $this->template->addFilter('duration', new DurationFilter($this->getTranslator()));
        $this->template->addFilter('job', new JobFilter($this->getHttpRequest()));
        $this->template->addFilter('person', new PersonFilter($this->getTranslator(), $this->getHttpRequest()));
    }

    /**
     * @return Form
     */
    public function createComponentJobForm()
    {
        $jobSettings = new JobSettings();
        $jobSettings->selectTownHandle = $this->link('jobFormSelectAddress!');

        $formFactory = new JobForm($this->getTranslator(), $jobSettings);

        $form = $formFactory->create();
        $form->onSuccess[] = [$this, 'jobFormSuccess'];

        return $form;
    }

    /**
     * @param Form $form
     * @param ArrayHash $values
     */
    public function jobFormSuccess(Form $form, ArrayHash $values)
    {
        $id = $this->getParameter('id');

        if ($id) {
            $this->jobManager->updateByPrimaryKey($id, $values);

            $this->flashMessage('job_saved', self::FLASH_SUCCESS);
        } else {
            $id = $this->jobManager->add($values);

            $this->flashMessage('job_added', self::FLASH_SUCCESS);
        }

        $this->redirect('Job:edit', $id);
    }
}
