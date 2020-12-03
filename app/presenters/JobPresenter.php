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
use Rendix2\FamilyTree\App\Filters\DurationFilter;
use Rendix2\FamilyTree\App\Filters\JobFilter;
use Rendix2\FamilyTree\App\Filters\PersonFilter;
use Rendix2\FamilyTree\App\Forms\JobForm;
use Rendix2\FamilyTree\App\Managers\AddressManager;
use Rendix2\FamilyTree\App\Managers\CountryManager;
use Rendix2\FamilyTree\App\Managers\JobManager;
use Rendix2\FamilyTree\App\Managers\Person2JobManager;
use Rendix2\FamilyTree\App\Managers\PersonManager;
use Rendix2\FamilyTree\App\Managers\TownManager;
use Rendix2\FamilyTree\App\Model\Facades\AddressFacade;
use Rendix2\FamilyTree\App\Model\Facades\JobFacade;
use Rendix2\FamilyTree\App\Presenters\Traits\Job\JobAddAddressModal;
use Rendix2\FamilyTree\App\Presenters\Traits\Job\JobAddPersonJobModal;
use Rendix2\FamilyTree\App\Presenters\Traits\Job\JobAddTownModal;
use Rendix2\FamilyTree\App\Presenters\Traits\Job\JobDeletePersonJobModal;
use Rendix2\FamilyTree\App\Presenters\Traits\Job\JobEditDeleteModal;
use Rendix2\FamilyTree\App\Presenters\Traits\Job\JobListDeleteModal;

/**
 * Class JobPresenter
 *
 * @package Rendix2\FamilyTree\App\Presenters
 */
class JobPresenter extends BasePresenter
{
    use JobAddAddressModal;

    use JobAddTownModal;

    use JobListDeleteModal;
    use JobEditDeleteModal;

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
     * @var JobManager $jobManager
     */
    private $jobManager;

    /**
     * @var PersonManager $personManager
     */
    private $personManager;

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
     * @var TownManager $townManager
     */
    private $townManager;

    /**
     * JobPresenter constructor.
     * @param AddressFacade $addressFacade
     * @param AddressManager $addressManager
     * @param CountryManager $countryManager
     * @param JobManager $jobManager
     * @param JobFacade $jobFacade
     * @param PersonManager $personManager
     * @param Person2JobFacade $person2JobFacade
     * @param Person2JobManager $person2JobManager
     * @param PersonFacade $personFacade
     * @param TownManager $townManager
     */
    public function __construct(
        AddressFacade $addressFacade,
        AddressManager $addressManager,
        CountryManager $countryManager,
        JobManager $jobManager,
        JobFacade $jobFacade,
        PersonManager $personManager,
        Person2JobFacade $person2JobFacade,
        Person2JobManager $person2JobManager,
        PersonFacade $personFacade,
        TownManager $townManager
    ) {
        parent::__construct();

        $this->addressFacade = $addressFacade;
        $this->addressManager = $addressManager;
        $this->countryManager = $countryManager;
        $this->jobFacade = $jobFacade;
        $this->jobManager = $jobManager;
        $this->personFacade = $personFacade;
        $this->personManager = $personManager;
        $this->person2JobFacade = $person2JobFacade;
        $this->person2JobManager = $person2JobManager;
        $this->townManager = $townManager;
    }

    /**
     * @return void
     */
    public function renderDefault()
    {
        $jobs = $this->jobFacade->getAllCached();

        $this->template->jobs = $jobs;

        $this->template->addFilter('job', new JobFilter());
    }

    /**
     * @param int|null $id jobId
     */
    public function actionEdit($id = null)
    {
        $towns = $this->townManager->getAllPairsCached();
        $addresses = $this->addressFacade->getPairsCached();

        $this['form-townId']->setItems($towns);
        $this['form-addressId']->setItems($addresses);

        if ($id !== null) {
            $job = $this->jobFacade->getByPrimaryKeyCached($id);

            if (!$job) {
                $this->error('Item not found.');
            }

            if ($job->town) {
                $this['form-townId']->setDefaultValue($job->town->id);
            }

            if ($job->address) {
                $this['form-addressId']->setDefaultValue($job->address->id);
            }

            $this['form']->setDefaults((array)$job);
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

        $this->template->addFilter('job', new JobFilter());
        $this->template->addFilter('person', new PersonFilter($this->getTranslator(), $this->getHttpRequest()));
        $this->template->addFilter('duration', new DurationFilter($this->getTranslator()));
    }

    /**
     * @return Form
     */
    public function createComponentForm()
    {
        $formFactory = new JobForm($this->getTranslator());

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
            $this->jobManager->updateByPrimaryKey($id, $values);

            $this->flashMessage('job_saved', self::FLASH_SUCCESS);
        } else {
            $id = $this->jobManager->add($values);

            $this->flashMessage('job_added', self::FLASH_SUCCESS);
        }

        $this->redirect('Job:edit', $id);
    }
}
