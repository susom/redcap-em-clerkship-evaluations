<?php
namespace Stanford\ClerkshipEvaluations;

include_once("classes/Student.php");
include_once("classes/Preceptor.php");
include_once("classes/Rotation.php");
include_once("classes/PreceptorStudentReviews.php");
require_once "emLoggerTrait.php";

/**
 * Class ClerkshipEvaluations
 * @package Stanford\ClerkshipEvaluations
 * @property \Stanford\ClerkshipEvaluations\Student $student
 * @property \Stanford\ClerkshipEvaluations\Preceptor $preceptor
 * @property \Stanford\ClerkshipEvaluations\Rotation $rotation
 * @property \Stanford\ClerkshipEvaluations\PreceptorStudentReviews $preceptorStudentReview
 *
 */
class ClerkshipEvaluations extends \ExternalModules\AbstractExternalModule
{

    use emLoggerTrait;

    private $preceptor;

    private $student;

    private $rotation;

    private $preceptorStudentReview;


    public function __construct()
    {
        parent::__construct();

        try {
            $this->setStudent(new Student($this->getProjectSetting("students")));
            $this->setPreceptor(new Preceptor($this->getProjectSetting("preceptors")));
            $this->setRotation(new Rotation($this->getProjectSetting("rotations")));
            $this->setPreceptorStudentReview(new PreceptorStudentReviews($this->getProjectSetting("preceptor-student-reviews")));
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }

    public function redcap_module_system_enable($version ) {

    }


    public function redcap_module_project_enable($version, $project_id)
    {

    }


    public function redcap_module_save_configuration($project_id)
    {

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

}
