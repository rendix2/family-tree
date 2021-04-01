<?php
/**
 *
 * Created by PhpStorm.
 * Filename: TownDeleteAddressTownModal.php
 * User: Tomáš Babický
 * Date: 22.11.2020
 * Time: 20:38
 */

namespace Rendix2\FamilyTree\App\Controls\Modals\Address;

use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Nette\Forms\Controls\SubmitButton;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Controls\Forms\DeleteModalForm;
use Rendix2\FamilyTree\App\Controls\Forms\Settings\DeleteModalFormSettings;
use Rendix2\FamilyTree\App\Filters\AddressFilter;
use Rendix2\FamilyTree\App\Filters\JobFilter;

use Rendix2\FamilyTree\App\Managers\JobManager;
use Rendix2\FamilyTree\App\Managers\JobSettingsManager;
use Rendix2\FamilyTree\App\Model\Facades\AddressFacade;
use Rendix2\FamilyTree\App\Model\Facades\JobFacade;
use Rendix2\FamilyTree\App\Presenters\BasePresenter;

/**
 * Class AddressDeleteAddressJobModal
 *
 * @package Rendix2\FamilyTree\App\Controls\Modals\Address
 */
class AddressDeleteAddressJobModal extends Control
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
     * @var AddressFilter $addressFilter
     */
    private $addressFilter;

    /**
     * @var JobFilter $jobFilter
     */
    private $jobFilter;

    /**
     * @var DeleteModalForm $deleteModalForm
     */
    private $deleteModalForm;

    /**
     * @var JobManager $jobManager
     */
    private $jobManager;

    /**
     * @var JobSettingsManager $jobSettingsManager
     */
    private $jobSettingsManager;

    /**
     * AddressDeleteAddressJobModal constructor.
     *
     * @param AddressFacade      $addressFacade
     * @param AddressFilter      $addressFilter
     * @param DeleteModalForm    $deleteModalForm
     * @param JobFacade          $jobFacade
     * @param JobFilter          $jobFilter
     * @param JobManager         $jobManager
     * @param JobSettingsManager $jobSettingsManager
     */
    public function __construct(
        AddressFacade $addressFacade,
        JobFacade $jobFacade,
        AddressFilter $addressFilter,
        JobFilter $jobFilter,
        DeleteModalForm $deleteModalForm,
        JobManager $jobManager,
        JobSettingsManager $jobSettingsManager
    ) {
        parent::__construct();

        $this->addressFacade = $addressFacade;
        $this->jobFacade = $jobFacade;

        $this->addressFilter = $addressFilter;
        $this->jobFilter = $jobFilter;

        $this->deleteModalForm = $deleteModalForm;

        $this->jobManager = $jobManager;

        $this->jobSettingsManager = $jobSettingsManager;
    }

    public function render()
    {
        $this['addressDeleteAddressJobForm']->render();
    }

    /**
     * @param int $addressId
     * @param int $jobId
     */
    public function handleAddressDeleteAddressJob($addressId, $jobId)
    {
        $presenter = $this->presenter;

        if (!$presenter->isAjax()) {
            $presenter->redirect('Address:edit', $presenter->getParameter('id'));
        }

        $this['addressDeleteAddressJobForm']->setDefaults(
            [
                'addressId' => $addressId,
                'jobId' => $jobId,
            ]
        );

        $addressFilter = $this->addressFilter;
        $jobFilter = $this->jobFilter;

        $addressModalItem = $this->addressFacade->getByPrimaryKeyCached($addressId);
        $jobModalItem = $this->jobFacade->getByPrimaryKeyCached($jobId);

        $presenter->template->modalName = 'addressDeleteAddressJob';
        $presenter->template->addressModalItem = $addressFilter($addressModalItem);
        $presenter->template->jobModalItem = $jobFilter($jobModalItem);

        $presenter->payload->showModal = true;

        $presenter->redrawControl('modal');
    }

    /**
     * @return Form
     */
    protected function createComponentAddressDeleteAddressJobForm()
    {
        $deleteModalFormSettings = new DeleteModalFormSettings();
        $deleteModalFormSettings->callBack = [$this, 'addressDeleteAddressJobFormYesOnClick'];

        $form = $this->deleteModalForm->create($deleteModalFormSettings);

        $form->addHidden('addressId');
        $form->addHidden('jobId');

        return $form;
    }

    /**
     * @param SubmitButton $submitButton
     * @param ArrayHash    $values
     */
    public function addressDeleteAddressJobFormYesOnClick(SubmitButton $submitButton, ArrayHash $values)
    {
        $presenter = $this->presenter;

        if (!$presenter->isAjax()) {
            $presenter->redirect('Address:edit', $presenter->getParameter('id'));
        }

        $this->jobManager->updateByPrimaryKey($values->jobId, ['addressId' => null]);

        $jobs = $this->jobSettingsManager->getByAddressId($values->addressId);

        $presenter->template->jobs = $jobs;

        $presenter->payload->showModal = false;

        $presenter->flashMessage('address_saved', BasePresenter::FLASH_SUCCESS);

        $presenter->redrawControl('flashes');
        $presenter->redrawControl('jobs');
    }
}
