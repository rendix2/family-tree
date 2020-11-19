<?php
/**
 *
 * Created by PhpStorm.
 * Filename: PersonAddress.php
 * User: Tomáš Babický
 * Date: 26.10.2020
 * Time: 1:36
 */

namespace Rendix2\FamilyTree\App\Presenters\Traits\Person;

use Nette\Application\UI\Form;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\BootstrapRenderer;
use Rendix2\FamilyTree\App\Filters\PersonFilter;
use Rendix2\FamilyTree\App\Forms\Person2AddressForm;
use Rendix2\FamilyTree\App\Presenters\BasePresenter;

/**
 * Trait PersonAddress
 *
 * @package Rendix2\FamilyTree\App\Presenters\Traits\Person
 */
trait PersonAddress
{
    /**
     * @param int $id personId
     */
    public function actionAddress($id)
    {
        $person = $this->manager->getByPrimaryKey($id);

        if (!$person) {
            $this->error('Item not found.');
        }

        $this->item = $person;

        $addresses = $this->addressManager->getAllPairs();

        $personFilter = new PersonFilter($this->getTranslator(), $this->getHttpRequest());

        $this['addressForm-personId']->setItems([$id => $personFilter($person)])->setDisabled()->setDefaultValue($id);
        $this['addressForm-addressId']->setItems($addresses);
    }

    /**
     * @param int $id personId
     */
    public function renderAddress($id)
    {
        $this->template->person = $this->item;

        $this->template->addFilter('person', new PersonFilter($this->getTranslator(), $this->getHttpRequest()));
    }

    /**
     * @return Form
     */
    public function createComponentAddressForm()
    {
        $control = new Person2AddressForm($this->getTranslator());
        $form = $control->create();

        $form->onRender[] = [BootstrapRenderer::class, 'makeBootstrap4'];
        $form->onSuccess[] = [$this, 'saveAddressForm'];

        return $form;
    }

    /**
     * @param Form $form
     * @param ArrayHash $values
     */
    public function saveAddressForm(Form $form, ArrayHash $values)
    {
        $personId = $this->getParameter('id');

        $values->personId = $personId;

        $this->person2AddressManager->addGeneral((array)$values);

        $this->flashMessage('item_added', BasePresenter::FLASH_SUCCESS);
        $this->redirect(':edit', $personId);
    }
}
