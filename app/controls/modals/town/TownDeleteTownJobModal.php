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
use Rendix2\FamilyTree\App\Filters\TownFilter;
use Rendix2\FamilyTree\App\Filters\JobFilter;
use Rendix2\FamilyTree\App\Forms\DeleteModalForm;

/**
 * Trait TownDeleteTownTownModal
 *
 * @package Rendix2\FamilyTree\App\Presenters\Traits\Town
 */
class TownDeleteTownJobModal extends Control
{
    /**
     * @param int $townId
     * @param int $jobId
     */
    public function handleTownDeleteTownJob($townId, $jobId)
    {
        if ($this->isAjax()) {
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

            $this->template->modalName = 'townDeleteTownJob';
            $this->template->townModalItem = $townFilter($townModalItem);
            $this->template->jobModalItem = $jobFilter($jobModalItem);

            $this->payload->showModal = true;

            $this->redrawControl('modal');
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
        if (!$this->isAjax()) {
            $this->redirect('Town:edit', $values->townId);
        }

        $this->jobManager->updateByPrimaryKey($values->jobId, ['townId' => null]);

        $jobs = $this->jobSettingsManager->getByTownId($values->townId);

        $this->template->jobs = $jobs;

        $this->payload->showModal = false;

        $this->flashMessage('town_saved', self::FLASH_SUCCESS);

        $this->redrawControl('flashes');
        $this->redrawControl('jobs');

    }
}
