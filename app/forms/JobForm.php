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
     * AddressForm constructor.
     *
     * @param ITranslator $translator
     */
    public function __construct(ITranslator $translator)
    {
        $this->translator = $translator;
    }

    /**
     * @return Form
     */
    public function create()
    {
        $form = new Form();

        $form->setTranslator($this->translator);

        $form->addProtection();

        $form->addText('company', 'job_company');
        $form->addText('position', 'job_position');

        $form->addSelect('townId', $this->translator->translate('job_town'))
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
