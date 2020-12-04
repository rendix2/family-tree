<?php
/**
 *
 * Created by PhpStorm.
 * Filename: GenusPersonNameDeleteModal.php
 * User: Tomáš Babický
 * Date: 30.10.2020
 * Time: 0:25
 */

namespace Rendix2\FamilyTree\App\Presenters\Traits\Genus;

use Dibi\ForeignKeyConstraintViolationException;
use Nette\Application\UI\Form;
use Nette\Forms\Controls\SubmitButton;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Filters\NameFilter;
use Rendix2\FamilyTree\App\Filters\PersonFilter;
use Rendix2\FamilyTree\App\Forms\DeleteModalForm;
use Rendix2\FamilyTree\App\Model\Facades\NameFacade;
use Tracy\Debugger;
use Tracy\ILogger;

/**
 * Trait GenusPersonNameDeleteModal
 *
 * @package Rendix2\FamilyTree\App\Presenters\Traits\Genus
 */
trait GenusDeletePersonNameModal
{
    /**
     * @param int $nameId
     * @param int $personId
     */
    public function handleGenusDeletePersonName($nameId, $personId)
    {
        if ($this->isAjax()) {
            $this['genusDeletePersonNameForm']->setDefaults(
                [
                    'nameId' => $nameId,
                    'personId' => $personId,
                ]
            );

            $personFilter = new PersonFilter($this->getTranslator(), $this->getHttpRequest());
            $nameFilter = new NameFilter();

            $personModalItem = $this->personFacade->getByPrimaryKeyCached($personId);
            $nameModalItem = $this->nameFacade->getByPrimaryKeyCached($nameId);

            $this->template->modalName = 'genusDeletePersonName';
            $this->template->personModalItem = $personFilter($personModalItem);
            $this->template->nameModalItem = $nameFilter($nameModalItem);

            $this->payload->showModal = true;

            $this->redrawControl('modal');
        }
    }

    /**
     * @return Form
     */
    protected function createComponentGenusDeletePersonNameForm()
    {
        $formFactory = new DeleteModalForm($this->getTranslator());

        $form = $formFactory->create([$this, 'genusDeletePersonNameFormYesOnClick']);
        $form->addHidden('nameId');
        $form->addHidden('personId');

        return $form;
    }

    /**
     * @param SubmitButton $submitButton
     * @param ArrayHash $values
     */
    public function genusDeletePersonNameFormYesOnClick(SubmitButton $submitButton, ArrayHash $values)
    {
        if ($this->isAjax()) {
            try {
                $this->nameManager->deleteByPrimaryKey($values->nameId);

                $genusNamePersons = $this->nameFacade->getByGenusIdCached($values->personId);

                $this->template->genusNamePersons = $genusNamePersons;

                $this->payload->showModal = false;

                $this->flashMessage('name_deleted', self::FLASH_SUCCESS);

                $this->redrawControl('genus_name_persons');
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
