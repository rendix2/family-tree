<?php
/**
 *
 * Created by PhpStorm.
 * Filename: BackupManager.php
 * User: Tomáš Babický
 * Date: 11.10.2020
 * Time: 18:20
 */

namespace Rendix2\FamilyTree\App\Managers;

use Exception;
use Google_Client;
use Google_Http_MediaFileUpload;
use Google_Service_Drive;
use Google_Service_Drive_DriveFile;
use Ifsnop\Mysqldump\Mysqldump;
use Tracy\Debugger;
use Tracy\ILogger;

/**
 * Class BackupManager
 * @package Rendix2\FamilyTree\App\Managers
 */
class BackupManager
{
    /**
     * @var Mysqldump $mysqlDump
     */
    private $mysqlDump;

    /**
     * @var string $folderId
     */
    private $folderId;

    /**
     * @var string $tempDir
     */
    private $tempDir;

    /**
     * @var string $appDir
     */
    private $appDir;

    /**
     * BackupManager constructor.
     *
     * @param string $appDir
     * @param string $tempDir
     * @param string $folderId
     * @param Mysqldump $mysqlDump
     */
    public function __construct($appDir, $tempDir, $folderId, Mysqldump $mysqlDump)
    {
        $this->mysqlDump = $mysqlDump;
        $this->folderId = $folderId;
        $this->tempDir = $tempDir;
        $this->appDir = $appDir;
    }

    /**
     * run backup mechanism
     */
    public function backup()
    {
        $sep = DIRECTORY_SEPARATOR;

        $fileToUpload = $this->tempDir . $sep .'dump.sql';

        $this->mysqlDump->start($fileToUpload);

        $client = new Google_Client();
        $client->setApplicationName("BackupDrive");
        $chunkSizeBytes = 5 * 1024 * 1024;

        $keyFileLocation = $this->appDir . $sep . 'config' . $sep . 'ft-google-drive.json';

        $uploadedName = $uploadedName = "backup - " . date("Y-m-d H:i:s") . '.sql';

        try {
            $client->setAuthConfig($keyFileLocation);
            $client->useApplicationDefaultCredentials();
            $client->addScope(
                [
                    Google_Service_Drive::DRIVE,
                    Google_Service_Drive::DRIVE_METADATA
                ]
            );

            $service = new Google_Service_Drive($client);

            $file = new Google_Service_Drive_DriveFile();
            $file->setName($uploadedName);
            $client->setDefer(true);

            $file->setParents([$this->folderId]);

            $createdFile = $service->files->create($file);
            $contentType = mime_content_type($fileToUpload);
            $media = new Google_Http_MediaFileUpload(
                $client,
                $createdFile,
                $contentType,
                null,
                true,
                $chunkSizeBytes
            );
            $media->setFileSize(filesize($fileToUpload));

            $status = false;
            $result = false;
            $handle = fopen($fileToUpload, "rb");

            while (!$status && !feof($handle)) {
                $chunk = fread($handle, $chunkSizeBytes);
                $status = $media->nextChunk($chunk);
            }

            if ($status !== false) {
                $result = $status;
            }

            fclose($handle);

            $client->setDefer(false);
        } catch (Exception $e) {
            Debugger::log($e->getMessage(), ILogger::EXCEPTION);
            Debugger::barDump($e->getMessage());
        }
    }
}