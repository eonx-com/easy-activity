<?php
declare(strict_types=1);

namespace EonX\EasyActivity\Common\Resolver;

use EonX\EasyActivity\Common\Entity\ActivitySubjectInterface;
use EonX\EasyActivity\Common\Enum\ActivityAction;
use EonX\EasyActivity\Common\Serializer\ActivitySubjectDataSerializerInterface;
use EonX\EasyActivity\Common\ValueObject\ActivitySubjectData;

final readonly class DefaultActivitySubjectDataResolver implements ActivitySubjectDataResolverInterface
{
    public const CONTEXT_KEY_SUBJECT_DATA_SERIALIZATION = 'easy_activity_subject_data_serialization';

    public const CONTEXT_KEY_SUBJECT_OLD_DATA_SERIALIZATION = 'easy_activity_subject_old_data_serialization';

    public function __construct(
        private ActivitySubjectDataSerializerInterface $serializer,
    ) {
    }

    public function resolve(
        ActivityAction|string $action,
        ActivitySubjectInterface $subject,
        array $changeSet,
    ): ?ActivitySubjectData {
        [$oldData, $data] = $this->resolveChangeData($action, $changeSet);

        $serializedData = $data !== null
            ? $this->serializer->serialize($data, $subject, [
                self::CONTEXT_KEY_SUBJECT_DATA_SERIALIZATION => true,
            ])
            : null;
        $serializedOldData = $oldData !== null
            ? $this->serializer->serialize($oldData, $subject, [
                self::CONTEXT_KEY_SUBJECT_OLD_DATA_SERIALIZATION => true,
            ])
            : null;

        if ($serializedData === null && $serializedOldData === null) {
            return null;
        }

        return new ActivitySubjectData($serializedData, $serializedOldData);
    }

    private function resolveChangeData(ActivityAction|string $action, array $changeSet): array
    {
        $oldData = [];
        $data = [];
        foreach ($changeSet as $field => [$oldValue, $newValue]) {
            $data[$field] = $newValue;
            $oldData[$field] = $oldValue;
        }

        if ($action === ActivityAction::Create) {
            $oldData = null;
        }

        if ($action === ActivityAction::Delete) {
            $data = null;
        }

        return [$oldData, $data];
    }
}
