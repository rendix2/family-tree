<?php
/**
 *
 * Created by PhpStorm.
 * Filename: s.php
 * User: Tomáš Babický
 * Date: 23.08.2020
 * Time: 15:11
 */

namespace Rendix2\FamilyTree\App\Managers;

use Dibi\Connection;

/**
 * Class Person2JobManager
 *
 * @package Rendix2\FamilyTree\App\Managers
 */
class Person2JobManager extends M2NManager
{

    /**
     * Person2JobManager constructor.
     *
     * @param Connection $dibi
     * @param PersonManager $left
     * @param JobManager $right
     * @param BackupManager $backupManager
     */
    public function __construct(Connection $dibi, PersonManager $left, JobManager $right, BackupManager $backupManager)
    {
        parent::__construct($dibi, $left, $right, $backupManager);
    }
}
