<?php
namespace Stanford\ClerkshipEvaluations;

include_once("classes/Student.php");
include_once("classes/Preceptor.php");
include_once("classes/Rotation.php");
include_once("classes/PreceptorStudentReviews.php");
require_once "emLoggerTrait.php";

use REDCap;
/**
 * Class ClerkshipEvaluations
 * @package Stanford\ClerkshipEvaluations
 * @property \Stanford\ClerkshipEvaluations\Student $student
 * @property \Stanford\ClerkshipEvaluations\Preceptor $preceptor
 * @property \Stanford\ClerkshipEvaluations\Rotation $rotation
 * @property \Stanford\ClerkshipEvaluations\PreceptorStudentReviews $preceptorStudentReview
 * @property \Project $project
 */
class ClerkshipEvaluations extends \ExternalModules\AbstractExternalModule
{

    use emLoggerTrait;

    private $preceptor;

    private $student;

    private $rotation;

    private $preceptorStudentReview;

    private $project;

    public function __construct()
    {
        parent::__construct();

        try {
            global $Proj;
            $this->setProject($Proj);
            $this->setStudent(new Student($this->getProjectSetting("students")));
            $this->setPreceptor(new Preceptor($this->getProjectSetting("preceptors")));
            $this->setRotation(new Rotation($this->getProjectSetting("rotations")));
            $this->setPreceptorStudentReview(new PreceptorStudentReviews($this->getProjectSetting("preceptor-student-reviews")));

            if (isset($_GET['hash'])) {
                if (!isset($_GET['page'])) {
                    throw new \Exception("page is missing");
                }
                if (strpos($_GET['page'], 'student') !== false) {
                    $this->getStudent()->setRecord(filter_var($_GET['hash'], FILTER_SANITIZE_STRING));
                }
                if (strpos($_GET['page'], 'preceptor') !== false) {
                    $this->getPreceptor()->setRecord(filter_var($_GET['hash'], FILTER_SANITIZE_STRING), 'preceptor_hash');
                }
            }
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }

    public function redcap_save_record($project_id, $record, $instrument, $event_id)
    {
        if ($instrument == "student") {
            $this->getStudent()->setRecord($record, 'id');
            $student = array_pop($this->getStudent()->getRecord());
            if ($student[$this->getStudent()->getEventId()]['hash'] == '') {
                $data['hash'] = $this->getStudent()->generateHash();
            } else {
                $data['hash'] = $student[$this->getStudent()->getEventId()]['hash'];
            }

            $data['id'] = $record;
            $data['student_url'] = $this->generateURL($data['hash'], $instrument);
            $data['redcap_event_name'] = \REDCap::getEventNames(true, false, $this->getStudent()->getEventId());
            $response = \REDCap::saveData('json', json_encode(array($data)));
            if (!empty($response['errors'])) {
                throw new \LogicException(implode(",", $response['errors']));
            }
        } elseif ($instrument == 'preceptor') {
            $this->getPreceptor()->setRecord($record, 'id');
            $preceptor = array_pop($this->getPreceptor()->getRecord());
            if ($preceptor[$this->getPreceptor()->getEventId()]['preceptor_hash'] == '') {
                $data['preceptor_hash'] = $this->getPreceptor()->generateHash();
            } else {
                $data['preceptor_hash'] = $preceptor[$this->getPreceptor()->getEventId()]['hash'];
            }

            $data['id'] = $record;
            $data['preceptor_url'] = $this->generateURL($data['preceptor_hash'], $instrument);
            $data['redcap_event_name'] = \REDCap::getEventNames(true, false, $this->getPreceptor()->getEventId());
            $response = \REDCap::saveData('json', json_encode(array($data)));
            if (!empty($response['errors'])) {
                throw new \LogicException(implode(",", $response['errors']));
            }
        }
    }


    private function generateURL($hash, $instrument)
    {
        return $this->getUrl('views/' . $instrument . '.php', true, true) . '&hash=' . $hash;
    }

    /**
     * @return Student
     */
    public function getStudent(): Student
    {
        return $this->student;
    }

    /**
     * @param Student $student
     */
    public function setStudent(Student $student): void
    {
        $this->student = $student;
    }

    /**
     * @return Preceptor
     */
    public function getPreceptor(): Preceptor
    {
        return $this->preceptor;
    }

    /**
     * @param Preceptor $preceptor
     */
    public function setPreceptor(Preceptor $preceptor): void
    {
        $this->preceptor = $preceptor;
    }

    /**
     * @return Rotation
     */
    public function getRotation(): Rotation
    {
        return $this->rotation;
    }

    /**
     * @param Rotation $rotation
     */
    public function setRotation(Rotation $rotation): void
    {
        $this->rotation = $rotation;
    }

    /**
     * @return PreceptorStudentReviews
     */
    public function getPreceptorStudentReview(): PreceptorStudentReviews
    {
        return $this->preceptorStudentReview;
    }

    /**
     * @param PreceptorStudentReviews $preceptorStudentReview
     */
    public function setPreceptorStudentReview(PreceptorStudentReviews $preceptorStudentReview): void
    {
        $this->preceptorStudentReview = $preceptorStudentReview;
    }

    /**
     * @return \Project
     */
    public function getProject(): \Project
    {
        return $this->project;
    }

    /**
     * @param \Project $project
     */
    public function setProject(\Project $project): void
    {
        $this->project = $project;
    }


}
