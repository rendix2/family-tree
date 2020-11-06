<?php
/**
 *
 * Created by PhpStorm.
 * Filename: PersonAddressPresenter.php
 * User: Tomáš Babický
 * Date: 28.10.2020
 * Time: 19:14
 */

namespace Rendix2\FamilyTree\App\Presenters;

use Nette\Application\UI\Form;
use Nette\Forms\Controls\SubmitButton;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\BootstrapRenderer;
use Rendix2\FamilyTree\App\Filters\AddressFilter;
use Rendix2\FamilyTree\App\Filters\PersonFilter;
use Rendix2\FamilyTree\App\Forms\DeleteModalForm;
use Rendix2\FamilyTree\App\Forms\Person2AddressForm;
use Rendix2\FamilyTree\App\Managers\AddressManager;
use Rendix2\FamilyTree\App\Managers\Person2AddressManager;
use Rendix2\FamilyTree\App\Managers\PersonManager;
use Rendix2\FamilyTree\App\Presenters\Traits\PersonAddress\EditDeleteModal;
use Rendix2\FamilyTree\App\Presenters\Traits\PersonAddress\ListDeleteModal;

/**
 * Class PersonAddressPresenter
 * 
 * @package Rendix2\FamilyTree\App\Presenters
 */
class PersonAddressPresenter extends BasePresenter
{
    use ListDeleteModal;
    use EditDeleteModal;

    /**
     * @var PersonManager
     */
    private $personManager;

    /**
     * @var Person2AddressManager $manager
     */
    private $manager;

    /**
     * @var AddressManager
     */
    private $addressManager;

    /**
     * PersonAddressPresenter constructor.
     * @param PersonManager $personManager
     * @param Person2AddressManager $person2AddressManager
     * @param AddressManager $addressManager
     */
    public function __construct(
        PersonManager $personManager,
        Person2AddressManager $person2AddressManager,
        AddressManager $addressManager
    ) {
        parent::__construct();

        $this->personManager = $personManager;
        $this->manager = $person2AddressManager;
        $this->addressManager = $addressManager;
    }

    /**
     * @return void
     */
    public function renderDefault()
    {
        $this->template->relations = $this->manager->getAllJoinedCountryJoinedTownJoined();

        $this->template->addFilter('person', new PersonFilter($this->getTranslator()));
        $this->template->addFilter('address', new AddressFilter());
    }

    /**
     * @param int $personId
     * @param int $addressId
     */
    public function actionEdit($personId, $addressId)
    {
        $persons = $this->personManager->getAllPairs($this->getTranslator());
        $addresses = $this->addressManager->getAllPairs();

        $this['form-personId']->setItems($persons);
        $this['form-addressId']->setItems($addresses);

        if ($personId && $addressId) {
            $relation = $this->manager->getByLeftIdAndRightId($personId, $addressId);

            if (!$relation) {
                $this->error('Item not found.');
            }

            $this['form']->setDefaults($relation);
        } elseif ($personId && !$addressId) {
            $person = $this->personManager->getByPrimaryKey($personId);

            if (!$person) {
                $this->error('Item not found.');
            }

            $this['form-personId']->setValue($personId);
        } elseif (!$personId && $addressId) {
            $address = $this->addressManager->getByPrimaryKey($addressId);

            if (!$address) {
                $this->error('Item not found.');
            }

            $this['form-addressId']->setValue($addressId);
        }
    }

    /**
     * @return Form
     */
    protected function createComponentForm()
    {
        $formFactory = new Person2AddressForm($this->getTranslator());

        $form = $formFactory->create();

        $form->onRender[] = [BootstrapRenderer::class, 'makeBootstrap4'];
        $form->onSuccess[] = [$this, 'saveForm'];

        return $form;
    }

    /**
     * @param Form $form
     * @param ArrayHash $values
     */
    public function saveForm(Form $form, ArrayHash $values)
    {
        $personId = $this->getParameter('personId');
        $addressId = $this->getParameter('addressId');

        if ($personId !== null || $addressId !== null) {
            $this->manager->updateGeneral($personId, $addressId, (array)$values);
            $this->flashMessage('item_updated', self::FLASH_SUCCESS);
            $this->redirect('PersonAddress:edit', $values->personId, $values->addressId);
        } else {
            $this->manager->addGeneral((array) $values);
            $this->flashMessage('item_added', self::FLASH_SUCCESS);
            $this->redirect('PersonAddress:edit', $personId, $addressId);
        }
    }
}
