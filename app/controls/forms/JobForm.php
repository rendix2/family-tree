<?php
/**
 *
 * Created by PhpStorm.
 * Filename: JobForm.php
 * User: Tomáš Babický
 * Date: 19.11.2020
 * Time: 21:38
 */

namespace Rendix2\FamilyTree\App\Controls\Forms;

use Nette\Application\UI\Form;
use Nette\Localization\ITranslator;
use Rendix2\FamilyTree\App\BootstrapRenderer;
use Rendix2\FamilyTree\App\Controls\Forms\Settings\JobSettings;

/**
 * Class JobForm
 *
 * @package Rendix2\FamilyTree\App\Controls\Forms
 */
class JobForm
{
    /**
     * @var ITranslator $translator
     */
    private $translator;

    /**
     * AddressForm constructor.
     *
     * @param ITranslator $translator
     */
    public function __construct(ITranslator $translator)
    {
        $this->translator = $translator;
    }

    /**
     * @param JobSettings $jobSettings
     *
     * @return Form
     */
    public function create(JobSettings $jobSettings)
    {
        $form = new Form();

        $form->setTranslator($this->translator);

        $form->addProtection();

        $form->addGroup('job_job');

        $form->addText('company', 'job_company');
        $form->addText('position', 'job_position');

        $form->addGroup('address_address');

        $form->addSelect('townId', $this->translator->translate('job_town'))
            ->setAttribute('data-link', $jobSettings->selectTownHandle)
            ->setTranslator()
            ->setPrompt($this->translator->translate('job_select_town'));

        $form->addSelect('addressId', $this->translator->translate('job_address'))
            ->setTranslator()
            ->setPrompt($this->translator->translate('job_select_address'));

        $form->addSubmit('send', 'job_save_job');

        $form->onRender[] = [BootstrapRenderer::class, 'makeBootstrap4'];

        return $form;
    }
}
