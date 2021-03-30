<?php
/**
 *
 * Created by PhpStorm.
 * Filename: AddressAddJobModal.php
 * User: Tomáš Babický
 * Date: 02.12.2020
 * Time: 1:58
 */

namespace Rendix2\FamilyTree\App\Controls\Modals\Town;

use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Nette\Localization\ITranslator;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Forms\JobForm;
use Rendix2\FamilyTree\App\Forms\Settings\JobSettings;
use Rendix2\FamilyTree\App\Managers\JobManager;
use Rendix2\FamilyTree\App\Managers\TownManager;
use Rendix2\FamilyTree\App\Managers\TownSettingsManager;
use Rendix2\FamilyTree\App\Model\Facades\AddressFacade;
use Rendix2\FamilyTree\App\Model\Facades\JobFacade;
use Rendix2\FamilyTree\App\Presenters\BasePresenter;

/**
 * Class TownAddJobModal
 *
 * @package Rendix2\FamilyTree\App\Controls\Modals\Town
 */
class TownAddJobModal extends Control
{
    /**
     * @var AddressFacade $addressFacade
     */
    private $addressFacade;

    /**
     * @var JobFacade $jobFacade
     */
    private $jobFacade;

    /**
     * @var JobManager $jobManager
     */
    private $jobManager;

    /**
     * @var TownManager $townManager
     */
    private $townManager;

    /**
     * @var TownSettingsManager $townSettingsManager
     */
    private $townSettingsManager;

    /**
     * @var ITranslator $translator
     */
    private $translator;

    /**
     * TownAddJobModal constructor.
     *
     * @param AddressFacade $addressFacade
     * @param JobFacade $jobFacade
     * @param JobManager $jobManager
     * @param TownManager $townManager
     * @param TownSettingsManager $townSettingsManager
     * @param ITranslator $translator
     */
    public function __construct(
        AddressFacade $addressFacade,
        JobFacade $jobFacade,
        JobManager $jobManager,
        TownManager $townManager,
        TownSettingsManager $townSettingsManager,
        ITranslator $translator
    ) {
        parent::__construct();

        $this->addressFacade = $addressFacade;
        $this->jobFacade = $jobFacade;
        $this->jobManager = $jobManager;
        $this->townManager = $townManager;
        $this->townSettingsManager = $townSettingsManager;
        $this->translator = $translator;
    }

    public function render()
    {
        $this['townAddJobForm']->render();
    }

    /**
     * @param int $townId
     *
     * @return void
     */
    public function handleTownAddJob($townId)
    {
        $presenter = $this->presenter;

        if (!$presenter->isAjax()) {
            $presenter->redirect('Town:edit', $presenter->getParameter('id'));
        }

        $towns = $this->townSettingsManager->getAllPairs();
        $addresses = $this->addressFacade->getPairsCached();

        $this['townAddJobForm-_townId']->setDefaultValue($townId);
        $this['townAddJobForm-townId']->setItems($towns)->setDisabled()->setDefaultValue($townId);
        $this['townAddJobForm-addressId']->setItems($addresses);

        $presenter->template->modalName = 'townAddJob';

        $presenter->payload->showModal = true;

        $presenter->redrawControl('modal');
    }

    /**
     * @return Form
     */
    protected function createComponentTownAddJobForm()
    {
        $jobSettings = new JobSettings();

        $formFactory = new JobForm($this->translator, $jobSettings);

        $form = $formFactory->create();
        $form->addHidden('_townId');
        $form->onAnchor[] = [$this, 'townAddJobFormAnchor'];
        $form->onValidate[] = [$this, 'townAddJobFormValidate'];
        $form->onSuccess[] = [$this, 'townAddJobFormSuccess'];
        $form->elementPrototype->setAttribute('class', 'ajax');

        return $form;
    }

    /**
     * @return void
     */
    public function townAddJobFormAnchor()
    {
        $presenter = $this->presenter;

        $presenter->redrawControl('modal');
    }

    /**
     * @param Form $form
     */
    public function townAddJobFormValidate(Form $form)
    {
        $towns = $this->townManager->getAllPairs();

        $townHiddenControl = $form->getComponent('_townId');

        $townControl = $form->getComponent('townId');
        $townControl->setItems($towns)
            ->setValue($townHiddenControl->getValue())
            ->validate();

        $addresses = $this->addressFacade->getPairsCached();

        $addressControl = $form->getComponent('addressId');
        $addressControl->setItems($addresses)
            ->validate();

        $form->removeComponent($townHiddenControl);
    }

    /**
     * @param Form $form
     * @param ArrayHash $values
     */
    public function townAddJobFormSuccess(Form $form, ArrayHash $values)
    {
        $presenter = $this->presenter;

        if (!$presenter->isAjax()) {
            $presenter->redirect('Town:edit', $presenter->getParameter('id'));
        }

        $this->jobManager->add($values);

        $jobs = $this->jobFacade->getByTownIdCached($values->townId);

        $presenter->template->jobs = $jobs;

        $presenter->payload->showModal = false;

        $presenter->flashMessage('job_added', BasePresenter::FLASH_SUCCESS);

        $presenter->redrawControl('flashes');
        $presenter->redrawControl('jobs');
    }
}
