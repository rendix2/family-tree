<?php
/**
 *
 * Created by PhpStorm.
 * Filename: GenusPersonGenusDeleteModal.php
 * User: Tomáš Babický
 * Date: 29.11.2020
 * Time: 4:17
 */

namespace Rendix2\FamilyTree\App\Presenters\Traits\Genus;


use Dibi\ForeignKeyConstraintViolationException;
use Nette\Application\UI\Form;
use Nette\Forms\Controls\SubmitButton;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Filters\GenusFilter;
use Rendix2\FamilyTree\App\Filters\NameFilter;
use Rendix2\FamilyTree\App\Filters\PersonFilter;
use Rendix2\FamilyTree\App\Forms\DeleteModalForm;
use Tracy\Debugger;
use Tracy\ILogger;

/**
 * Trait GenusPersonGenusDeleteModal
 *
 * @package Rendix2\FamilyTree\App\Presenters\Traits\Genus
 */
trait GenusPersonGenusDeleteModal
{
    /**
     * @param int $genusId
     * @param int $personId
     */
    public function handleDeletePersonGenus($genusId, $personId)
    {
        if ($this->isAjax()) {
            $this['deletePersonGenusForm']->setDefaults(
                [
                    'genusId' => $genusId,
                    'personId' => $personId,
                ]
            );

            $personFilter = new PersonFilter($this->getTranslator(), $this->getHttpRequest());
            $genusFilter = new GenusFilter();

            $personModalItem = $this->personFacade->getByPrimaryKeyCached($personId);
            $genusModalItem = $this->genusManager->getByPrimaryKeyCached($genusId);

            $this->template->modalName = 'deletePersonGenus';
            $this->template->personModalItem = $personFilter($personModalItem);
            $this->template->genusModalItem = $genusFilter($genusModalItem);

            $this->payload->showModal = true;

            $this->redrawControl('modal');
        }
    }

    /**
     * @return Form
     */
    protected function createComponentDeletePersonGenusForm()
    {
        $formFactory = new DeleteModalForm($this->getTranslator());

        $form = $formFactory->create($this, 'deletePersonGenusFormOk');
        $form->addHidden('genusId');
        $form->addHidden('personId');

        return $form;
    }

    /**
     * @param SubmitButton $submitButton
     * @param ArrayHash $values
     */
    public function deletePersonGenusFormOk(SubmitButton $submitButton, ArrayHash $values)
    {
        if ($this->isAjax()) {
            try {
                $this->personManager->updateByPrimaryKey($values->personId, ['genusId' => null]);

                $genusPersons = $this->personFacade->getByGenusIdCached($values->personId);

                $this->template->genusPersons = $genusPersons;

                $this->payload->showModal = false;

                $this->flashMessage('person_genus_deleted', self::FLASH_SUCCESS);

                $this->redrawControl('genus_persons');
                $this->redrawControl('flashes');
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
            $this->redirect('Genus:edit', $values->nameId);
        }
    }
}