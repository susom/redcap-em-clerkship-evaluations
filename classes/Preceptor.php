<?php


namespace Stanford\ClerkshipEvaluations;

use REDCap;

/**
 * Class Preceptors
 * @package Stanford\ClerkshipEvaluations
 * @property int $eventId
 * @property array $record
 * @property array $rotations
 * @property string $preRotationReview
 * @property array $postRotationReviewInstruments
 */
class Preceptor
{
    private $eventId;

    private $record;

    private $rotations;

    private $preRotationReview;

    private $postRotationReviewInstruments;

    public function __construct($eventId, $preRotationReview, $postRotationReviewInstruments)
    {
        try {
            $this->setEventId($eventId);
            $this->setPreRotationReview($preRotationReview);
            $this->setPostRotationReviewInstruments($postRotationReviewInstruments);
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }

    public static function getPreceptorName($eventId, $preceptorId)
    {
        $params = array(
            'return_format' => 'array',
            'records' => [$preceptorId],
            'events' => $eventId,
        );
        $data = REDCap::getData($params);
        return $data[$preceptorId][$eventId]['preceptor_first_name'] . ' ' . $data[$preceptorId][$eventId]['preceptor_last_name'];
    }

    /**
     * @return array
     */
    public function getRotations($rotationsEventId): array
    {
        if ($this->rotations) {
            return $this->rotations;
        } else {
            $this->setRotations(Rotation::getRotationsRecords($this->getRecord()[$this->getEventId()][\REDCap::getRecordIdField()], $rotationsEventId, 'preceptor'));
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
     * @return array
     */
    public function getRecord(): array
    {
        return $this->record;
    }

    /**
     * @param array $record
     */
    public function setRecord($identifier, $field = 'hash'): void
    {
        $temp = $this->getStudentViaHash($identifier, $field,
            array());;
        $this->record = array_pop($temp);
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

    public function generateHash()
    {
        //$url_field   = $this->getProjectSetting('personal-url-fields');  // won't work with sub_settings

        $i = 0;
        do {
            $new_hash = generateRandomHash(20, false, true, false);

            $q = $this->getStudentViaHash($new_hash);
            $i++;
        } while (count($q) > 0 and $i < 10); //keep generating until nothing returns from get

        return $new_hash;
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
     * @param \Project $project
     * @param array $review
     */
    public function isPreRotationReviewComplete($project, $review)
    {
        try {
            $preRotationReviewInstrument = $this->getPreRotationReview();
            $fields = array_keys($project->forms[$preRotationReviewInstrument]['fields']);
            $completedField = end($fields);
            if ($review[$completedField] == 2) {
                return true;
            }
            return false;
        } catch (\Exception $e) {
            echo $e->getMessage();
            die();
        }
    }

    public function isPostRotationReviewComplete($project, $review, $specialty)
    {
        try {
            $postRotationReviewInstrument = $this->findSpecialtyPostRotationReviewInstrument($project, $specialty);
            $fields = array_keys($project->forms[$postRotationReviewInstrument]['fields']);
            $completedField = end($fields);
            if ($review[$completedField] == 2) {
                return true;
            }
            return false;
        } catch (\Exception $e) {
            echo $e->getMessage();
            die();
        }
    }

    /**
     * @param \Project $project
     * @param string $specialty
     */
    public function findSpecialtyPostRotationReviewInstrument($project, $specialty)
    {
        foreach ($this->getPostRotationReviewInstruments() as $instrument) {
            // instrument is array not string
            $value = array_pop($instrument);
            if ($specialty == $project->forms[$value]['menu']) {
                return $value;
            }
        }
        throw new \Exception("could not find post rotation review instrument. please  check EM configuration");
    }

    /**
     * @return string
     */
    public function getPreRotationReview(): string
    {
        return $this->preRotationReview;
    }

    /**
     * @param string $preRotationReview
     */
    public function setPreRotationReview(string $preRotationReview): void
    {
        $this->preRotationReview = $preRotationReview;
    }

    /**
     * @return array
     */
    public function getPostRotationReviewInstruments(): array
    {
        return $this->postRotationReviewInstruments;
    }

    /**
     * @param array $postRotationReviewInstruments
     */
    public function setPostRotationReviewInstruments(array $postRotationReviewInstruments): void
    {
        $this->postRotationReviewInstruments = $postRotationReviewInstruments;
    }


}
