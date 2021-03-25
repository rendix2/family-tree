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
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Forms\DeleteModalForm;
use Rendix2\FamilyTree\App\Presenters\BasePresenter;

/**
 * Class TownDeleteTownJobModal
 *
 * @package Rendix2\FamilyTree\App\Controls\Modals\Town
 */
class TownDeleteTownJobModal extends Control
{
    /**
     * @param int $townId
     * @param int $jobId
     */
    public function handleTownDeleteTownJob($townId, $jobId)
    {
        $presenter = $this->presenter;

        if ($presenter->isAjax()) {
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
    }

    /**
     * @return Form
     */
    protected function createComponentTownDeleteTownJobForm()
    {
        $formFactory = new DeleteModalForm($this->translator);

        $form = $formFactory->create([$this, 'townDeleteTownJobFormYesOnClick']);
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
            $presenter->redirect('Town:edit', $values->townId);
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
