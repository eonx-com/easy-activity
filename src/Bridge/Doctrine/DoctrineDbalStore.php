<?php

declare(strict_types=1);

namespace EonX\EasyActivity\Bridge\Doctrine;

use Doctrine\DBAL\Connection;
use EonX\EasyActivity\ActivityLogEntry;
use EonX\EasyActivity\Interfaces\IdFactoryInterface;
use EonX\EasyActivity\Interfaces\StoreInterface;

final class DoctrineDbalStore implements StoreInterface
{
    public function __construct(
        private IdFactoryInterface $idFactory,
        private Connection $connection,
        private string $table,
    ) {
    }

    public function store(ActivityLogEntry $logEntry): ActivityLogEntry
    {
        $data = [
            'id' => $this->idFactory->create(),
            'created_at' => $logEntry->getCreatedAt()
                ->format('Y-m-d H:i:s.u'),
            'updated_at' => $logEntry->getUpdatedAt()
                ->format('Y-m-d H:i:s.u'),
            'actor_type' => $logEntry->getActorType(),
            'actor_id' => $logEntry->getActorId(),
            'actor_name' => $logEntry->getActorName(),
            'action' => $logEntry->getAction(),
            'subject_type' => $logEntry->getSubjectType(),
            'subject_id' => $logEntry->getSubjectId(),
            'subject_data' => $logEntry->getSubjectData(),
            'subject_old_data' => $logEntry->getSubjectOldData(),
        ];

        $this->connection->insert($this->table, $data);

        return $logEntry;
    }
}
