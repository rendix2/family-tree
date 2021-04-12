<?php
/**
 *
 * Created by PhpStorm.
 * Filename: CronPresenter.php
 * User: Tomáš Babický
 * Date: 10.12.2020
 * Time: 14:00
 */

namespace Rendix2\FamilyTree\App\Presenters;

use Rendix2\FamilyTree\App\Model\Managers\BackupManager;

/**
 * Class CronPresenter
 *
 * @package Rendix2\FamilyTree\App\Presenters
 */
class CronPresenter extends BasePresenter
{
    /**
     * @var BackupManager
     */
    private $backupManager;

    /**
     * CronPresenter constructor.
     *
     * @param BackupManager $backupManager
     */
    public function __construct(BackupManager $backupManager)
    {
        parent::__construct();

        $this->backupManager = $backupManager;
    }

    /**
     * @return void
     */
    public function actionBackup()
    {
        $this->backupManager->backup();

        $this->terminate();
    }
}
