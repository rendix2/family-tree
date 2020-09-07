<?php
/**
 *
 * Created by PhpStorm.
 * Filename: WeddingPresenter.php
 * User: Tomáš Babický
 * Date: 29.08.2020
 * Time: 1:34
 */

namespace Rendix2\FamilyTree\App\Presenters;

use Nette\Application\UI\Form;
use Rendix2\FamilyTree\App\BootstrapRenderer;
use Rendix2\FamilyTree\App\Managers\PeopleManager;
use Rendix2\FamilyTree\App\Managers\WeddingManager;

/**
 * Class WeddingPresenter
 *
 * @package Rendix2\FamilyTree\App\Presenters
 */
class WeddingPresenter extends BasePresenter
{
    use CrudPresenter {
        actionEdit as traitActionEdit;
    }

    /**
     * @var WeddingManager $manager
     */
    private $manager;

    /**
     * @var PeopleManager $peopleManager
     */
    private $peopleManager;

    /**
     * WeddingPresenter constructor.
     *
     * @param WeddingManager $manager
     * @param PeopleManager $personManager
     */
    public function __construct(WeddingManager $manager, PeopleManager $personManager)
    {
        parent::__construct();

        $this->manager = $manager;
        $this->peopleManager = $personManager;
    }

    /**
     * @return void
     */
    public function renderDefault()
    {
        $weddings = $this->manager->getFluentJoinedBothPeople()->fetchAll();

        $this->template->weddings = $weddings;
    }

    /**
     * @param int|null $id
     */
    public function actionEdit($id = null)
    {
        $peoples = $this->peopleManager->getAllPairs();

        $this['form-husbandId']->setItems($peoples);
        $this['form-wifeId']->setItems($peoples);

        $this->traitActionEdit($id);
    }

    /**
     * @return Form
     */
    protected function createComponentForm()
    {
        $form = new Form();

        $form->setTranslator($this->getTranslator());

        $form->addProtection();
        $form->addSelect('husbandId', $this->getTranslator()->translate('wedding_husband'))->setTranslator(null);
        $form->addSelect('wifeId', $this->getTranslator()->translate('wedding_wife'))->setTranslator(null);

        $form->addTbDatePicker('dateSince', 'wedding_date_since')
            ->setNullable()
            ->setHtmlAttribute('class', 'form-control datepicker')
            ->setHtmlAttribute('data-toggle', 'datepicker')
            ->setHtmlAttribute('data-target', '#date');

        $form->addTbDatePicker('dateTo', 'wedding_date_to')
            ->setNullable()
            ->setHtmlAttribute('class', 'form-control datepicker')
            ->setHtmlAttribute('data-toggle', 'datepicker')
            ->setHtmlAttribute('data-target', '#date');

        $form->addSubmit('send', 'save');

        $form->onSuccess[] = [$this, 'saveForm'];
        $form->onRender[] = [BootstrapRenderer::class, 'makeBootstrap4'];

        return $form;
    }
}
