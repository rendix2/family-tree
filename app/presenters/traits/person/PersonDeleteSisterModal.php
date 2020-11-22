<?php
/**
 *
 * Created by PhpStorm.
 * Filename: PersonDeleteSisterModal.php
 * User: Tomáš Babický
 * Date: 05.11.2020
 * Time: 15:56
 */

namespace Rendix2\FamilyTree\App\Presenters\Traits\Person;

use Nette\Application\UI\Form;
use Nette\Forms\Controls\SubmitButton;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Filters\PersonFilter;
use Rendix2\FamilyTree\App\Forms\DeleteModalForm;

/**
 * Trait PersonDeleteSisterModal
 *
 * @package Rendix2\FamilyTree\App\Presenters\Traits\Person
 */
trait PersonDeleteSisterModal
{
    /**
     * @param int $personId
     * @param int $sisterId
     */
    public function handleDeleteSisterItem($personId, $sisterId)
    {
        if ($this->isAjax()) {
            $this['deletePersonSisterForm']->setDefaults(
                [
                    'personId' => $personId,
                    'sisterId' => $sisterId
                ]
            );

            $sister = $this->personManager->getByPrimaryKey($sisterId);

            $this->template->modalName = 'deleteSisterItem';
            $this->template->sisterModalItem = $sister;

            $this->payload->showModal = true;

            $this->redrawControl('modal');
        }
    }

    /**
     * @return Form
     */
    protected function createComponentDeletePersonSisterForm()
    {
        $formFactory = new DeleteModalForm($this->getTranslator());
        $form = $formFactory->create($this, 'deletePersonSisterFormOk');

        $form->addHidden('personId');
        $form->addHidden('sisterId');

        return $form;
    }

    /**
     * @param SubmitButton $submitButton
     * @param ArrayHash $values
     */
    public function deletePersonSisterFormOk(SubmitButton $submitButton, ArrayHash $values)
    {
        if ($this->isAjax()) {
            $this->personManager->updateByPrimaryKey($values->sisterId,
                [
                    'fatherId' => null,
                    'motherId' => null
                ]
            );

            $sister = $this->personFacade->getByPrimaryKeyCached($values->sisterId);

            $this->prepareBrothersAndSisters($values->sisterId, $sister->father, $sister->mother);

            $this->payload->showModal = false;

            $this->flashMessage('item_deleted', self::FLASH_SUCCESS);

            $this->redrawControl('modal');
            $this->redrawControl('sisters');
        } else {
            $this->redirect('Person:edit', $values->personId);
        }
    }
}
