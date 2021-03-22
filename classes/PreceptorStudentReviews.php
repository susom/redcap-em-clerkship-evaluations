<?php


namespace Stanford\ClerkshipEvaluations;


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
