<?php
/**
 *
 * Created by PhpStorm.
 * Filename: JobFacadeSelector.php
 * User: Tomáš Babický
 * Date: 06.04.2021
 * Time: 15:16
 */

namespace Rendix2\FamilyTree\App\Model\Managers\Job;


use Dibi\Connection;
use Rendix2\FamilyTree\App\Filters\JobFilter;
use Rendix2\FamilyTree\App\Model\CrudManager\DefaultSelector;
use Rendix2\FamilyTree\App\Model\Entities\JobEntity;
use Rendix2\FamilyTree\App\Model\Managers\Job\Interfaces\IJobSelector;

class JobSelector extends DefaultSelector implements IJobSelector
{
    /**
     * JobFacadeSelector constructor.
     *
     * @param Connection $connection
     * @param JobFilter  $jobFilter
     * @param JobTable   $table
     */
    public function __construct(
        Connection $connection,
        JobFilter $jobFilter,
        JobTable $table
    ) {
        parent::__construct($connection, $table, $jobFilter);
    }

    /**
     * @param int $townId
     *
     * @return JobEntity[]
     */
    public function getByTownId($townId)
    {
        return $this->getAllFluent()
            ->where('[townId] = %i', $townId)
            ->execute()
            ->setRowClass(JobEntity::class)
            ->fetchAll();
    }

    /**
     * @param int $addressId
     *
     * @return JobEntity[]
     */
    public function getByAddressId($addressId)
    {
        return $this->getAllFluent()
            ->where('[addressId] = %i', $addressId)
            ->execute()
            ->setRowClass(JobEntity::class)
            ->fetchAll();
    }
}