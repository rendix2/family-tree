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
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\BootstrapRenderer;
use Rendix2\FamilyTree\App\Facades\Person2AddressFacade;
use Rendix2\FamilyTree\App\Filters\AddressFilter;
use Rendix2\FamilyTree\App\Filters\PersonFilter;
use Rendix2\FamilyTree\App\Forms\Person2AddressForm;
use Rendix2\FamilyTree\App\Managers\AddressManager;
use Rendix2\FamilyTree\App\Managers\Person2AddressManager;
use Rendix2\FamilyTree\App\Managers\PersonManager;
use Rendix2\FamilyTree\App\Model\Facades\AddressFacade;
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
     * @var AddressFacade $addressFacade
     */
    private $addressFacade;

    /**
     * @var Person2AddressFacade $person2AddressFacade
     */
    private $person2AddressFacade;

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
     * @param AddressFacade $addressFacade
     * @param Person2AddressFacade $person2AddressFacade
     * @param PersonManager $personManager
     * @param Person2AddressManager $person2AddressManager
     * @param AddressManager $addressManager
     */
    public function __construct(
        AddressFacade $addressFacade,
        Person2AddressFacade $person2AddressFacade,
        PersonManager $personManager,
        Person2AddressManager $person2AddressManager,
        AddressManager $addressManager
    ) {
        parent::__construct();

        $this->addressFacade = $addressFacade;
        $this->personManager = $personManager;
        $this->manager = $person2AddressManager;
        $this->addressManager = $addressManager;
        $this->person2AddressFacade = $person2AddressFacade;
    }

    /**
     * @return void
     */
    public function renderDefault()
    {
        $this->template->relations = $this->person2AddressFacade->getAllCached();

        $this->template->addFilter('person', new PersonFilter($this->getTranslator(), $this->getHttpRequest()));
        $this->template->addFilter('address', new AddressFilter());
    }

    /**
     * @param int $personId
     * @param int $addressId
     */
    public function actionEdit($personId, $addressId)
    {
        $persons = $this->personManager->getAllPairsCached($this->getTranslator());
        $addresses = $this->addressFacade->getPairsCached();

        $this['form-personId']->setItems($persons);
        $this['form-addressId']->setItems($addresses);

        if ($personId && $addressId) {
            $relation = $this->person2AddressFacade->getByLeftAndRightCached($personId, $addressId);

            if (!$relation) {
                $this->error('Item not found.');
            }

            $this['form-personId']->setDefaultValue($relation->person->id);
            $this['form-addressId']->setDefaultValue($relation->address->id);

            $this['form-dateSince']->setDefaultValue($relation->duration->dateSince);
            $this['form-dateTo']->setDefaultValue($relation->duration->dateTo);
            $this['form-untilNow']->setDefaultValue($relation->duration->untilNow);

            $this['form']->setDefaults((array)$relation);
        } elseif ($personId && !$addressId) {
            $person = $this->personManager->getByPrimaryKey($personId);

            if (!$person) {
                $this->error('Item not found.');
            }

            $this['form-personId']->setDefaultValue($personId);
        } elseif (!$personId && $addressId) {
            $address = $this->addressManager->getByPrimaryKey($addressId);

            if (!$address) {
                $this->error('Item not found.');
            }

            $this['form-addressId']->setDefaultValue($addressId);
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
