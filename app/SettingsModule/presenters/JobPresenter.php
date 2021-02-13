<?php
/**
 *
 * Created by PhpStorm.
 * Filename: JobPresenter.php
 * User: Tomáš Babický
 * Date: 09.02.2021
 * Time: 23:20
 */

namespace Rendix2\FamilyTree\SettingsModule\App\Presenters;

use dibi;
use Nette\Application\UI\Form;
use Nette\Caching\Cache;
use Nette\Caching\IStorage;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\BootstrapRenderer;
use Rendix2\FamilyTree\App\Presenters\BasePresenter;

/**
 * Class JobPresenter
 *
 * @package Rendix2\FamilyTree\SettingsModule\App\Presenters
 */
class JobPresenter extends BasePresenter
{
    const JOB_ORDERING = 'settings_job_order';

    const JOB_ORDERING_ID = 1;

    const JOB_ORDERING_COMPANY = 2;

    const JOB_ORDERING_POSITION = 3;

    const JOB_ORDERING_COMPANY_POSITION = 4;

    const JOB_ORDERING_POSITION_COMPANY = 5;


    const JOB_ORDERING_WAY = 'settings_job_order_way';


    const JOB_NAME_ORDER = 'settings_job_name_order';

    const JOB_ORDER_NAME_COMPANY_POSITION = 1;

    const JOB_ORDER_NAME_POSITION_COMPANY = 2;

    /**
     * @var Cache $cache
     */
    private $cache;

    /**
     * SettingsPresenter constructor.
     *
     * @param IStorage $storage
     */
    public function __construct(IStorage $storage)
    {
        parent::__construct();

        $this->cache = new Cache($storage, self::class);
    }

    /**
     * @return void
     */
    public function actionDefault()
    {
        $this['jobForm']->setDefaults(
            [
                self::JOB_ORDERING => $this->getHttpRequest()->getCookie(self::JOB_ORDERING),
                self::JOB_NAME_ORDER => $this->getHttpRequest()->getCookie(self::JOB_NAME_ORDER),
                self::JOB_ORDERING_WAY => $this->getHttpRequest()->getCookie(self::JOB_ORDERING_WAY),
            ]
        );
    }

    /**
     * @return Form
     */
    protected function createComponentJobForm()
    {
        $form = new Form();

        $form->setTranslator($this->getTranslator());

        $form->addProtection();

        $jobOrderItems = [
            self::JOB_ORDERING_ID => 'settings_job_order_id',
            self::JOB_ORDERING_COMPANY => 'settings_job_order_company',
            self::JOB_ORDERING_POSITION => 'settings_job_order_position',
            self::JOB_ORDERING_COMPANY_POSITION => 'settings_job_order_company_position',
            self::JOB_ORDERING_POSITION_COMPANY => 'settings_job_order_position_company',
        ];

        $form->addSelect(self::JOB_ORDERING, 'settings_job_order', $jobOrderItems)
            ->setPrompt('settings_select_job_ordering')
            ->setRequired('settings_job_order_required');

        $jobOrderingWayItems = [
            dibi::ASC => dibi::ASC,
            dibi::DESC => dibi::DESC
        ];

        $form->addRadioList(self::JOB_ORDERING_WAY, 'settings_order_way', $jobOrderingWayItems)
            ->setRequired('settings_order_way_required');

        $jobNameOrderItems = [
            self::JOB_ORDER_NAME_COMPANY_POSITION => 'settings_job_order_name_company_position',
            self::JOB_ORDER_NAME_POSITION_COMPANY => 'settings_job_order_name_position_company',
        ];

        $form->addSelect(self::JOB_NAME_ORDER, 'settings_job_name_order', $jobNameOrderItems)
            ->setPrompt('settings_select_job_order_name')
            ->setRequired('settings_job_name_order_required');

        $form->addSubmit('send', 'save');

        $form->onRender[] = [BootstrapRenderer::class, 'makeBootstrap4'];
        $form->onSuccess[]= [$this, 'jobFormSuccess'];

        return $form;
    }

    /**
     * @param Form $form
     * @param ArrayHash $values
     */
    public function jobFormSuccess(Form $form, ArrayHash $values)
    {
        $this->getHttpResponse()->setCookie(self::JOB_ORDERING, $values->{self::JOB_ORDERING}, '1 year');
        $this->getHttpResponse()->setCookie(self::JOB_ORDERING_WAY, $values->{self::JOB_ORDERING_WAY}, '1 year');
        $this->getHttpResponse()->setCookie(self::JOB_NAME_ORDER, $values->{self::JOB_NAME_ORDER}, '1 year');

        $this->cache->clean([Cache::ALL =>true]);

        $this->redirect(':Settings:Job:default');
    }
}
