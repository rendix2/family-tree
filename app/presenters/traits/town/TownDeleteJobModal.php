<?php
/**
 *
 * Created by PhpStorm.
 * Filename: TownDeleteJobModal.php
 * User: Tomáš Babický
 * Date: 31.10.2020
 * Time: 1:30
 */

namespace Rendix2\FamilyTree\App\Presenters\Traits\Town;

use Dibi\ForeignKeyConstraintViolationException;
use Nette\Application\UI\Form;
use Nette\Forms\Controls\SubmitButton;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Filters\JobFilter;
use Rendix2\FamilyTree\App\Forms\DeleteModalForm;
use Tracy\Debugger;
use Tracy\ILogger;

/**
 * Trait TownDeleteJobModal
 *
 * @package Rendix2\FamilyTree\App\Presenters\Traits\Town
 */
trait TownDeleteJobModal
{
    /**
     * @param int $townId
     * @param int $jobId
     */
    public function handleDeleteJobItem($townId, $jobId)
    {
        if ($this->isAjax()) {
            $this['deleteJobForm']->setDefaults(
                [
                    'townId' => $townId,
                    'jobId' => $jobId
                ]
            );

            $jobFilter = new JobFilter();

            $jobModalItem = $this->jobFacade->getByPrimaryKeyCached($jobId);

            $this->template->modalName = 'deleteJobItem';
            $this->template->jobModalItem = $jobFilter($jobModalItem);

            $this->payload->showModal = true;

            $this->redrawControl('modal');
        }
    }

    /**
     * @return Form
     */
    protected function createComponentDeleteJobForm()
    {
        $formFactory = new DeleteModalForm($this->getTranslator());

        $form = $formFactory->create($this, 'deleteJobFormOk');
        $form->addHidden('townId');
        $form->addHidden('jobId');


        return $form;
    }

    /**
     * @param SubmitButton $submitButton
     * @param ArrayHash $values
     */
    public function deleteJobFormOk(SubmitButton $submitButton, ArrayHash $values)
    {
        if ($this->isAjax()) {
            try {
                $this->jobManager->deleteByPrimaryKey($values->jobId);

                $jobs = $this->jobManager->getByTownId($values->townId);

                $this->template->jobs = $jobs;

                $this->payload->showModal = false;

                $this->flashMessage('item_deleted', self::FLASH_SUCCESS);

                $this->redrawControl('jobs');
            } catch (ForeignKeyConstraintViolationException $e) {
                if ($e->getCode() === 1451) {
                    $this->flashMessage('Item has some unset relations', self::FLASH_DANGER);
                } else {
                    Debugger::log($e, ILogger::EXCEPTION);
                }
            } finally {
                $this->redrawControl('flashes');
            }
        } else {
            $this->redirect('Town:edit', $values->townId);
        }
    }
}
