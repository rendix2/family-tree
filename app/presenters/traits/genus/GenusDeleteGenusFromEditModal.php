<?php
/**
 *
 * Created by PhpStorm.
 * Filename: AddressDeleteAddressEditModal.php
 * User: Tomáš Babický
 * Date: 16.11.2020
 * Time: 21:12
 */

namespace Rendix2\FamilyTree\App\Presenters\Traits\Genus;

use Dibi\ForeignKeyConstraintViolationException;
use Nette\Application\UI\Form;
use Nette\Forms\Controls\SubmitButton;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Filters\GenusFilter;
use Rendix2\FamilyTree\App\Forms\DeleteModalForm;
use Tracy\Debugger;
use Tracy\ILogger;

/**
 * Trait GenusDeleteGenusFromEditModal
 *
 * @package Rendix2\FamilyTree\App\Presenters\Traits\Genus
 */
trait GenusDeleteGenusFromEditModal
{
    /**
     * @param int $genusId
     */
    public function handleGenusDeleteGenusFromEdit($genusId)
    {
        if ($this->isAjax()) {
            $this['genusDeleteGenusFromEditForm']->setDefaults(['genusId' => $genusId]);

            $genusFilter = new GenusFilter();

            $genusModalItem = $this->genusManager->getByPrimaryKeyCached($genusId);

            $this->template->modalName = 'genusDeleteGenusFromEdit';
            $this->template->genusModalItem = $genusFilter($genusModalItem);

            $this->payload->showModal = true;

            $this->redrawControl('modal');
        }
    }

    /**
     * @return Form
     */
    protected function createComponentGenusDeleteGenusFromEditForm()
    {
        $formFactory = new DeleteModalForm($this->getTranslator());

        $form = $formFactory->create([$this, 'genusDeleteGenusFromEditFormYesOnClick'], true);
        $form->addHidden('genusId');

        return $form;
    }

    /**
     * @param SubmitButton $submitButton
     * @param ArrayHash $values
     */
    public function genusDeleteGenusFromEditFormYesOnClick(SubmitButton $submitButton, ArrayHash $values)
    {
        try {
            $this->genusManager->deleteByPrimaryKey($values->genusId);

            $this->flashMessage('genus_deleted', self::FLASH_SUCCESS);

            $this->redirect('Genus:default');
        } catch (ForeignKeyConstraintViolationException $e) {
            if ($e->getCode() === 1451) {
                $this->flashMessage('Item has some unset relations', self::FLASH_DANGER);

                $this->redrawControl('flashes');
            } else {
                Debugger::log($e, ILogger::EXCEPTION);
            }
        }
    }
}
