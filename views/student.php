<?php

namespace Stanford\LectureEvaluation;

/** @var \Stanford\ClerkshipEvaluations\ClerkshipEvaluations $module */

use \REDCap;
use Stanford\ClerkshipEvaluations\Preceptor;
use Stanford\ClerkshipEvaluations\Rotation;

?>
<!doctype html>
<html lang="en">
<head>
    <title>Student - Clerkship Evaluations</title>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css"
          integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.19/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="<?php echo $module->getUrl('assets/css/main.css') ?>">
    <link rel="stylesheet" type="text/css"
          href="https://cdn.datatables.net/1.10.15/css/jquery.dataTables.min.css"></link>

    <script src="https://code.jquery.com/jquery-3.3.1.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

    <!-- DataTable Implementation -->
    <script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.5.6/js/dataTables.buttons.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"
            integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl"
            crossorigin="anonymous"></script>
    <style>
        body {
            word-wrap: break-word;
        }
    </style>
</head>
<body>

<div id="app" class="container">
    <div class="row p-1">
        <h3><?php echo $module->getProjectSetting("header_text") ?></h3>
    </div>
    <div class="row p-1">
        <table id="review-table" class="display table table-striped table-bordered">
            <thead>
            <th>ID</th>
            <th>Site</th>
            <th>Clerkship Period</th>
            <th>Preceptor</th>
            <th>Evaluate</th>
            </thead>
            <tbody>
            <?php
            $rotations = $module->getStudent()->getRotations($module->getRotation()->getEventId());
            $months = $module->getProject()->metadata['month']["element_enum"];
            foreach ($rotations as $rotation) {
                $reviews = $module->getPreceptorStudentReview()->getRotationReviews($rotation[$module->getRotation()->getEventId()][REDCap::getRecordIdField()]);
                if ($reviews) {
                    foreach ($reviews as $review) {
                        if ($review[$module->getPreceptorStudentReview()->getEventId()]['student_evaluation_complete'] != 2) {
                            $url = REDCap::getSurveyLink($review[$module->getPreceptorStudentReview()->getEventId()][REDCap::getRecordIdField()], $module->getStudent()->getReviewInstrument(), $module->getPreceptorStudentReview()->getEventId());
                        }
                        ?>
                        <tr>
                            <td><?php echo $rotation[$module->getRotation()->getEventId()][REDCap::getRecordIdField()] ?></td>
                            <td><?php echo $rotation[$module->getRotation()->getEventId()]['location'] ?></td>
                            <td><?php echo Rotation::getMonthValue($months, $rotation[$module->getRotation()->getEventId()]['month']) ?></td>
                            <td><?php echo Preceptor::getPreceptorName($module->getPreceptor()->getEventId(), $review[$module->getPreceptorStudentReview()->getEventId()]['preceptor_id']) ?></td>
                            <td><?php echo !$review[$module->getPreceptorStudentReview()->getEventId()]['student_evaluation_complete'] ? '<a href="' . $url . '" target="_blank">Evaluate</a>' : 'Review Completed' ?></td>
                        </tr>
                        <?php
                    }
                }
            }
            ?>
            </tbody>
        </table>
    </div>
    <div class="row p-1">
    </div>
</div>
<script src="<?php echo $module->getUrl('assets/js/main.js') ?>"></script>
</body>
</html>
