<?php
/**
 *
 * Created by PhpStorm.
 * Filename: GenusPresenter.php
 * User: Tomáš Babický
 * Date: 29.08.2020
 * Time: 22:34
 */

namespace Rendix2\FamilyTree\App\Presenters;

use Nette\Application\UI\Form;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Controls\Forms\GenusForm;
use Rendix2\FamilyTree\App\Controls\Modals\Genus\Container\GenusModalContainer;
use Rendix2\FamilyTree\App\Facades\PersonFacade;
use Rendix2\FamilyTree\App\Managers\GenusManager;
use Rendix2\FamilyTree\App\Model\Facades\NameFacade;

/**
 * Class GenusPresenter
 *
 * @package Rendix2\FamilyTree\App\Presenters
 */
class GenusPresenter extends BasePresenter
{
    /**
     * @var GenusForm $genusForm
     */
    private $genusForm;

    /**
     * @var GenusManager $genusManager
     */
    private $genusManager;

    /**
     * @var GenusModalContainer $genusModalContainer
     */
    private $genusModalContainer;

    /**
     * @var NameFacade $nameFacade
     */
    private $nameFacade;

    /**
     * @var PersonFacade $personFacade
     */
    private $personFacade;

    /**
     * GenusPresenter constructor.
     *
     * @param GenusModalContainer $genusModalContainer
     * @param NameFacade $nameFacade
     * @param PersonFacade $personFacade
     */
    public function __construct(
        GenusModalContainer $genusModalContainer,
        GenusForm $genusForm,
        NameFacade $nameFacade,
        PersonFacade $personFacade
    ) {
        parent::__construct();

        $this->genusForm = $genusForm;

        $this->genusModalContainer = $genusModalContainer;

        $this->nameFacade = $nameFacade;
        $this->personFacade = $personFacade;
    }

    /**
     * @return void
     */
    public function renderDefault()
    {
        $genuses = $this->genusManager->getAllCached();

        $this->template->genuses = $genuses;
    }

    /**
     * @param int|null $id
     */
    public function actionEdit($id = null)
    {
        if ($id !== null) {
            $genus = $this->genusManager->getByPrimaryKeyCached($id);

            if (!$genus) {
                $this->error('Item not found.');
            }

            $this['genusForm']->setDefaults((array) $genus);
        }
    }

    /**
     * @param int|null $id
     */
    public function renderEdit($id = null)
    {
        if ($id === null) {
            $genusPersons = [];
            $genusNamePersons = [];
        } else {
            $genusPersons = $this->personFacade->getByGenusIdCached($id);
            $genusNamePersons = $this->nameFacade->getByGenusIdCached($id);
        }

        $genus = $this->genusManager->getByPrimaryKeyCached($id);

        $this->template->genusPersons = $genusPersons;
        $this->template->genusNamePersons = $genusNamePersons;
        $this->template->genus = $genus;
    }

    /**
     * @return Form
     */
    public function createComponentGenusForm()
    {
        $form = $this->genusForm->create();

        $form->onSuccess[] = [$this, 'genusFormSuccess'];

        return $form;
    }

    /**
     * @param Form $form
     * @param ArrayHash $values
     */
    public function genusFormSuccess(Form $form, ArrayHash $values)
    {
        $id = $this->getParameter('id');

        if ($id) {
            $this->genusManager->updateByPrimaryKey($id, $values);

            $this->flashMessage('genus_saved', self::FLASH_SUCCESS);
        } else {
            $id = $this->genusManager->add($values);

            $this->flashMessage('genus_added', self::FLASH_SUCCESS);
        }

        $this->redirect('Genus:edit', $id);
    }

    public function createComponentGenusDeleteGenusFromListModal()
    {
        return $this->genusModalContainer->getGenusDeleteGenusFromListModalFactory()->create();
    }

    public function createComponentGenusDeleteGenusFromEditModal()
    {
        return $this->genusModalContainer->getGenusDeleteGenusFromEditModalFactory()->create();
    }

    public function createComponentGenusDeletePersonNameModal()
    {
        return $this->genusModalContainer->getGenusDeletePersonNameModalFactory()->create();
    }

    public function createComponentGenusDeletePersonGenusModal()
    {
        return $this->genusModalContainer->getGenusDeletePersonGenusModalFactory()->create();
    }

    public function createComponentGenusAddNameModal()
    {
        return $this->genusModalContainer->getGenusAddNameModalFactory()->create();
    }
}
