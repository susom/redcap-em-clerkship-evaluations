<?php


namespace Stanford\ClerkshipEvaluations;

use REDCap;

/**
 * Class Rotation
 * @package Stanford\ClerkshipEvaluations
 * @property int $eventId
 */
class Rotation
{
    private $eventId;

    public function __construct($eventId)
    {
        try {
            $this->setEventId($eventId);
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }

    public static function getMonthValue($list, $index)
    {
        $months = parseEnum($list);
        return $months[$index];
    }

    public static function getRotationsRecords($recordId, $eventId, $type)
    {
        if ($type == 'students') {
            $filter = "[student_id] = '" . $recordId . "'";

        } elseif ($type == 'preceptor') {
            $filter = "[preceptor] = '" . $recordId . "'";
        }
        $params = array(
            'return_format' => 'array',
            'filterLogic' => $filter,
            'events' => $eventId,
        );
        return REDCap::getData($params);
    }

    /**
     * @return int
     */
    public function getEventId(): int
    {
        return $this->eventId;
    }

    /**
     * @param int $eventId
     */
    public function setEventId(int $eventId): void
    {
        $this->eventId = $eventId;
    }
}
