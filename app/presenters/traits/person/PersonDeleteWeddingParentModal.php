<?php
/**
 *
 * Created by PhpStorm.
 * Filename: PersonDeleteWeddingParentModal.php
 * User: Tomáš Babický
 * Date: 27.10.2020
 * Time: 2:18
 */

namespace Rendix2\FamilyTree\App\Presenters\Traits\Person;

use Nette\Application\UI\Form;
use Nette\Forms\Controls\SubmitButton;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Filters\PersonFilter;
use Rendix2\FamilyTree\App\Filters\WeddingFilter;
use Rendix2\FamilyTree\App\Forms\DeleteModalForm;

/**
 * Trait PersonDeleteWeddingParentModal
 *
 * @package Rendix2\FamilyTree\App\Presenters\Traits\Person
 */
trait PersonDeleteWeddingParentModal
{
    /**
     * @param int $personId
     * @param int $weddingId
     */
    public function handlePersonDeleteParentsWedding($personId, $weddingId)
    {
        if (!$this->isAjax()) {
            $this->redirect('Person:edit', $this->getParameter('id'));
        }

        if ($this->isAjax()) {
            $this['personDeleteParentsWeddingForm']->setDefaults(
                [
                    'weddingId' => $weddingId,
                    'personId' => $personId
                ]
            );

            $personFilter = new PersonFilter($this->translator, $this->getHttpRequest());
            $weddingFilter = new WeddingFilter($personFilter);

            $weddingModalItem = $this->weddingFacade->getByPrimaryKeyCached($weddingId);

            $this->template->modalName = 'personDeleteParentsWedding';
            $this->template->weddingModalItem = $weddingFilter($weddingModalItem);

            $this->payload->showModal = true;

            $this->redrawControl('modal');
        }
    }

    /**
     * @return Form
     */
    protected function createComponentPersonDeleteParentsWeddingForm()
    {
        $formFactory = new DeleteModalForm($this->translator);

        $form = $formFactory->create([$this, 'personDeleteParentsWeddingFormYesOnClick']);
        $form->addHidden('weddingId');
        $form->addHidden('personId');

        return $form;
    }

    /**
     * @param SubmitButton $submitButton
     * @param ArrayHash $values
     */
    public function personDeleteParentsWeddingFormYesOnClick(SubmitButton $submitButton, ArrayHash $values)
    {
        if ($this->isAjax()) {
            $this->weddingManager->deleteByPrimaryKey($values->weddingId);

            $person = $this->personFacade->getByPrimaryKeyCached($values->personId);

            $this->prepareParentsWeddings($person->father, $person->mother);

            $this->payload->showModal = false;

            $this->flashMessage('wedding_deleted', self::FLASH_SUCCESS);

            $this->redrawControl('flashes');
            $this->redrawControl('father_wives');
            $this->redrawControl('mother_husbands');
        } else {
            $this->redirect('Person:edit', $values->personId);
        }
    }
}
