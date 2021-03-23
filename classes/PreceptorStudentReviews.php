<?php


namespace Stanford\ClerkshipEvaluations;
use REDCap;

class PreceptorStudentReviews
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


    public function getRotationReviews($rotationId)
    {
        $filter = "[rotation_id] = '" . $rotationId . "'";

        $params = array(
            'return_format' => 'array',
            'filterLogic' => $filter,
            'events' => $this->getEventId(),
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
