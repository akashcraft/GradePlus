<?php

use Tests\Support\AcceptanceTester;

class ClassListCest {

    // Helper function to reset the database
    private function resetDatabase(AcceptanceTester $I) {
        $I->haveHttpHeader('Content-Type', 'application/x-www-form-urlencoded');
        $I->sendPost('/services/reset-demo.php', [
            'authorize' => 'gradeplus'
        ]);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson([
            'success' => 1,
            'error'   => 0
        ]);
    }

    // Test for fetching students of a valid course
    public function fetchValidCourseStudents(AcceptanceTester $I) {
        $this->resetDatabase($I);  // Ensure a clean slate

        $I->haveHttpHeader('Content-Type', 'application/x-www-form-urlencoded');
        $I->sendPost('/services/get-students.php', [
            'authorize'   => 'gradeplus',
            'course_code' => 'CS 3301'
        ]);

        $I->seeResponseIsJson();
        $I->seeResponseContainsJson([
            'success' => 1,
            'error'   => 0,
            'illegal' => 0,
            'data'    => [
                [
                    'profilePicture' => "",  // Adjust if necessary
                    'dname'          => 'mafewer',
                    'username'       => 'mafewer'
                ],
                [
                    'profilePicture' => "",  // Adjust if necessary
                    'dname'          => 'ddolomount',
                    'username'       => 'ddolomount'
                ]
            ],
            'message' => 'Students retrieved successfully.'
        ]);
    }

    // Test for handling a non-existent course code
    public function fetchNonExistentCourse(AcceptanceTester $I) {
        $this->resetDatabase($I);

        $course_code = 'ECE 3301';

        $I->haveHttpHeader('Content-Type', 'application/x-www-form-urlencoded');
        $I->sendPost('/services/get-students.php', [
            'authorize'   => 'gradeplus',
            'course_code' => $course_code
        ]);

        $I->seeResponseIsJson();
        $I->seeResponseContainsJson([
            'success' => 0,
            'error'   => 1,
            'illegal' => 0,
            'data'    => [],
            'message' => "Course code '$course_code' does not exist."
        ]);
    }
}
