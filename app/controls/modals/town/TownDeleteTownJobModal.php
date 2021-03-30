<?php
/**
 *
 * Created by PhpStorm.
 * Filename: TownDeleteTownTownModal.php
 * User: Tomáš Babický
 * Date: 22.11.2020
 * Time: 20:38
 */

namespace Rendix2\FamilyTree\App\Controls\Modals\Town;

use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Nette\Forms\Controls\SubmitButton;
use Nette\Localization\ITranslator;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Controls\Forms\DeleteModalForm;
use Rendix2\FamilyTree\App\Controls\Forms\Settings\DeleteModalFormSettings;
use Rendix2\FamilyTree\App\Filters\JobFilter;
use Rendix2\FamilyTree\App\Filters\TownFilter;

use Rendix2\FamilyTree\App\Managers\JobManager;
use Rendix2\FamilyTree\App\Managers\JobSettingsManager;
use Rendix2\FamilyTree\App\Model\Facades\JobFacade;
use Rendix2\FamilyTree\App\Model\Facades\TownFacade;
use Rendix2\FamilyTree\App\Presenters\BasePresenter;

/**
 * Class TownDeleteTownJobModal
 *
 * @package Rendix2\FamilyTree\App\Controls\Modals\Town
 */
class TownDeleteTownJobModal extends Control
{
    /**
     * @var DeleteModalForm $deleteModalForm
     */
    private $deleteModalForm;

    /**
     * @var JobFacade $jobFacade
     */
    private $jobFacade;

    /**
     * @var JobFilter $jobFilter
     */
    private $jobFilter;

    /**
     * @var JobManager $jobManager
     */
    private $jobManager;

    /**
     * @var JobSettingsManager $jobSettingsManager
     */
    private $jobSettingsManager;

    /**
     * @var TownFacade $townFacade
     */
    private $townFacade;

    /**
     * @var TownFilter $townFilter
     */
    private $townFilter;

    /**
     * @var ITranslator $translator
     */
    private $translator;

    /**
     * TownDeleteTownJobModal constructor.
     *
     * @param JobFacade $jobFacade
     * @param JobFilter $jobFilter
     * @param JobManager $jobManager
     * @param JobSettingsManager $jobSettingsManager
     * @param TownFacade $townFacade
     * @param TownFilter $townFilter
     * @param ITranslator $translator
     */
    public function __construct(
        JobFacade $jobFacade,
        JobFilter $jobFilter,

        DeleteModalForm $deleteModalForm,

        JobManager $jobManager,
        JobSettingsManager $jobSettingsManager,
        TownFacade $townFacade,
        TownFilter $townFilter,
        ITranslator $translator
    ) {
        parent::__construct();

        $this->deleteModalForm = $deleteModalForm;

        $this->jobFacade = $jobFacade;
        $this->jobFilter = $jobFilter;
        $this->jobManager = $jobManager;
        $this->jobSettingsManager = $jobSettingsManager;
        $this->townFacade = $townFacade;
        $this->townFilter = $townFilter;
        $this->translator = $translator;
    }

    public function render()
    {
        $this['townDeleteTownJobForm']->render();
    }

    /**
     * @param int $townId
     * @param int $jobId
     */
    public function handleTownDeleteTownJob($townId, $jobId)
    {
        $presenter = $this->presenter;

        if (!$presenter->isAjax()) {
            $presenter->redirect('Town:edit', $presenter->getParameter('id'));
        }

        $this['townDeleteTownJobForm']->setDefaults(
            [
                'townId' => $townId,
                'jobId' => $jobId
            ]
        );

        $townFilter = $this->townFilter;
        $jobFilter = $this->jobFilter;

        $townModalItem = $this->townFacade->getByPrimaryKeyCached($townId);
        $jobModalItem = $this->jobFacade->getByPrimaryKeyCached($jobId);

        $presenter->template->modalName = 'townDeleteTownJob';
        $presenter->template->townModalItem = $townFilter($townModalItem);
        $presenter->template->jobModalItem = $jobFilter($jobModalItem);

        $presenter->payload->showModal = true;

        $presenter->redrawControl('modal');
    }

    /**
     * @return Form
     */
    protected function createComponentTownDeleteTownJobForm()
    {
        $deleteModalFormSettings = new DeleteModalFormSettings();
        $deleteModalFormSettings->callBack = [$this, 'townDeleteTownJobFormYesOnClick'];

        $form = $this->deleteModalForm->create($deleteModalFormSettings);
        $form->addHidden('townId');
        $form->addHidden('jobId');

        return $form;
    }

    /**
     * @param SubmitButton $submitButton
     * @param ArrayHash $values
     */
    public function townDeleteTownJobFormYesOnClick(SubmitButton $submitButton, ArrayHash $values)
    {
        $presenter = $this->presenter;

        if (!$presenter->isAjax()) {
            $presenter->redirect('Town:edit', $presenter->getParameter('id'));
        }

        $this->jobManager->updateByPrimaryKey($values->jobId, ['townId' => null]);

        $jobs = $this->jobSettingsManager->getByTownId($values->townId);

        $presenter->template->jobs = $jobs;

        $presenter->payload->showModal = false;

        $presenter->flashMessage('town_saved', BasePresenter::FLASH_SUCCESS);

        $presenter->redrawControl('flashes');
        $presenter->redrawControl('jobs');
    }
}
