<?php

namespace Stanford\ClerkshipEvaluations;

use REDCap;

/**
 * Class Student
 * @package Stanford\ClerkshipEvaluations
 * @property int $eventId
 * @property int $recordId
 * @property array $record
 * @property array $rotations
 * @property string $reviewInstrument
 */
class Student
{


    private $eventId;

    private $record;

    private $rotations;

    private $recordId;

    private $reviewInstrument;

    public function __construct($eventId, $reviewInstrument)
    {
        try {
            $this->setEventId($eventId);
            $this->setReviewInstrument($reviewInstrument);
        } catch (\LogicException $e) {
            echo $e->getMessage();
        }
    }

    /**
     * @return array
     */
    public function getRecord()
    {
        return $this->record;
    }

    /**
     * @param array $record
     */
    public function setRecord($identifier, $field = 'hash')
    {
        $this->record = $this->getStudentViaHash($identifier, $field,
            array());

        $temp = array_pop($this->record);
        $this->setRecordId($temp[$this->getEventId()][REDCap::getRecordIdField()]);
    }

    public static function getStudentName($eventId, $studentId)
    {
        $params = array(
            'return_format' => 'array',
            'records' => [$studentId],
            'events' => $eventId,
        );
        $data = REDCap::getData($params);
        return $data[$studentId][$eventId]['first_name'] . ' ' . $data[$studentId][$eventId]['last_name'];
    }

    /**
     * @return int
     */
    public function getEventId()
    {
        return $this->eventId;
    }

    /**
     * @param int $event
     */
    public function setEventId($event)
    {
        $this->eventId = $event;
    }

    /**
     * @return int
     */
    public function getRecordId(): int
    {
        return $this->recordId;
    }

    /**
     * @param int $recordId
     */
    public function setRecordId(int $recordId): void
    {
        $this->recordId = $recordId;
    }


    public function getAllStudent()
    {
        $param = array(
            'return_format' => 'array',
            'events' => $this->getEventId()
        );
        return REDCap::getData($param);
    }

    public function generateHash()
    {
        //$url_field   = $this->getProjectSetting('personal-url-fields');  // won't work with sub_settings

        $i = 0;
        do {
            $new_hash = generateRandomHash(8, false, true, false);

            $q = $this->getStudentViaHash($new_hash);
            $i++;
        } while (count($q) > 0 and $i < 10); //keep generating until nothing returns from get

        return $new_hash;
    }

    public function getURL($hash)
    {
        $record = $this->getStudentViaHash($hash, 'hash', array('student_url'));
        return $record[0][$this->getEventId()];
    }

    private function getStudentViaHash($identifier, $field = 'hash', $fields = array('hash'))
    {
        # check if fields are requested or not
        if (!empty($fields)) {
            $params = array(
                'return_format' => 'array',
                'fields' => $fields,
                'events' => $this->getEventId(),
            );
        } else {
            $params = array(
                'return_format' => 'array',
                'events' => $this->getEventId(),
            );
        }
        $records = REDCap::getData($params);;
        foreach ($records as $key => $record) {
            if ($record[$this->getEventId()][$field] == $identifier) {
                return array($key => $records[$key]);
            }
        }
        return false;
    }

    /**
     * @return array
     */
    public function getRotations($rotationsEventId): array
    {
        if ($this->rotations) {
            return $this->rotations;
        } else {
            $this->setRotations(Rotation::getRotationsRecords($this->getRecord()[$this->getEventId()][\REDCap::getRecordIdField()], $rotationsEventId, 'student'));
        }
        return $this->rotations;
    }

    /**
     * @param array $rotations
     */
    public function setRotations(array $rotations): void
    {
        $this->rotations = $rotations;
    }

    /**
     * @return string
     */
    public function getReviewInstrument(): string
    {
        return $this->reviewInstrument;
    }

    /**
     * @param string $reviewInstrument
     */
    public function setReviewInstrument(string $reviewInstrument): void
    {
        $this->reviewInstrument = $reviewInstrument;
    }


}
