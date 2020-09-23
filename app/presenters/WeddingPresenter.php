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
use Rendix2\FamilyTree\App\Managers\PersonManager;
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
     * @var PersonManager $personManager
     */
    private $personManager;

    /**
     * WeddingPresenter constructor.
     *
     * @param WeddingManager $manager
     * @param PersonManager $personManager
     */
    public function __construct(WeddingManager $manager, PersonManager $personManager)
    {
        parent::__construct();

        $this->manager = $manager;
        $this->personManager = $personManager;
    }

    /**
     * @return void
     */
    public function renderDefault()
    {
        $weddings = $this->manager->getFluentJoinedBothPersons()->fetchAll();

        $this->template->weddings = $weddings;
    }

    /**
     * @param int|null $id
     */
    public function actionEdit($id = null)
    {
        $husbands = $this->personManager->getMalesPairs();
        $wives = $this->personManager->getFemalesPairs();

        $this['form-husbandId']->setItems($husbands);
        $this['form-wifeId']->setItems($wives);

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
        $form->addSelect('husbandId', $this->getTranslator()->translate('wedding_husband'))
            ->setTranslator(null)
            ->setPrompt($this->getTranslator()->translate('wedding_select_husband'))
            ->setRequired('wedding_husband_required');

        $form->addSelect('wifeId', $this->getTranslator()->translate('wedding_wife'))
            ->setTranslator(null)
            ->setPrompt($this->getTranslator()->translate('wedding_select_wife'))
            ->setRequired('wedding_wife_required');

        $form->addTbDatePicker('dateSince', 'date_since')
            ->setNullable()
            ->setHtmlAttribute('class', 'form-control datepicker')
            ->setHtmlAttribute('data-toggle', 'datepicker')
            ->setHtmlAttribute('data-target', '#date');

        $form->addTbDatePicker('dateTo', 'date_to')
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
