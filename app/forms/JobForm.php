<?php
/**
 *
 * Created by PhpStorm.
 * Filename: JobForm.php
 * User: Tomáš Babický
 * Date: 19.11.2020
 * Time: 21:38
 */

namespace Rendix2\FamilyTree\App\Forms;

use Nette\Application\UI\Form;
use Nette\Localization\ITranslator;
use Rendix2\FamilyTree\App\BootstrapRenderer;
use Rendix2\FamilyTree\App\Forms\Settings\JobSettings;

/**
 * Class JobForm
 *
 * @package Rendix2\FamilyTree\App\Forms
 */
class JobForm
{
    /**
     * @var ITranslator $translator
     */
    private $translator;

    /**
     * @var JobSettings $jobSettings
     */
    private $jobSettings;

    /**
     * AddressForm constructor.
     *
     * @param ITranslator $translator
     * @param JobSettings $jobSettings
     */
    public function __construct(
        ITranslator $translator,
        JobSettings $jobSettings
    ) {
        $this->translator = $translator;
        $this->jobSettings = $jobSettings;
    }

    /**
     * @return Form
     */
    public function create()
    {
        $form = new Form();

        $form->setTranslator($this->translator);

        $form->addProtection();

        $form->addGroup('job_job');

        $form->addText('company', 'job_company');
        $form->addText('position', 'job_position');

        $form->addGroup('address_address');

        $form->addSelect('townId', $this->translator->translate('job_town'))
            ->setAttribute('data-link', $this->jobSettings->selectTownHandle)
            ->setTranslator(null)
            ->setPrompt($this->translator->translate('job_select_town'));

        $form->addSelect('addressId', $this->translator->translate('job_address'))
            ->setTranslator(null)
            ->setPrompt($this->translator->translate('job_select_address'));

        $form->addSubmit('send', 'job_save_job');

        $form->onRender[] = [BootstrapRenderer::class, 'makeBootstrap4'];

        return $form;
    }
}
