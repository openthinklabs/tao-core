<?php

declare(strict_types=1);

namespace oat\tao\migrations;

use Doctrine\DBAL\Schema\Schema;
use oat\tao\model\taskQueue\TaskLogInterface;
use oat\tao\scripts\install\SetUpQueueTasks;
use oat\tao\scripts\tools\migrations\AbstractMigration;

final class Version202106010921112234_tao extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $sm = $this->getServiceManager();
        $taskLog = $sm->get(TaskLogInterface::SERVICE_ID);
        $taskLog->setOption(TaskLogInterface::OPTION_TASK_IGNORE_LIST, $this->getIndexationTasks());
        $sm->register(TaskLogInterface::SERVICE_ID, $taskLog);
    }

    public function down(Schema $schema): void
    {
        $sm = $this->getServiceManager();
        $taskLog = $sm->get(TaskLogInterface::SERVICE_ID);
        $taskLog->setOption(TaskLogInterface::OPTION_TASK_IGNORE_LIST, []);
        $sm->register(TaskLogInterface::SERVICE_ID, $taskLog);
    }

    private function getIndexationTasks(): array
    {
        return SetUpQueueTasks::QUEUE_TASK_IGNORE;
    }
}
