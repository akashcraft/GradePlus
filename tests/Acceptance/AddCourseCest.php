<?php

use Tests\Support\AcceptanceTester;

class AddCourseCest {
    public function AddCourse(AcceptanceTester $I) {
        $I->haveHttpHeader('Content-Type', 'application/x-www-form-urlencoded');
        $I->sendPost('/services/add-course.php', [
            'authorize' => 'gradeplus',
            'coursename' => 'Communication Networks',
            'coursebanner' => 'banner.png',
            'instructor_name' => 'Daniel Dolomount',
            'coursecode' => 'ECE 6610',
            'instructor_dname' => 'Daniel Dolomount'
        ]);

        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(['success' => 1]);

        $I->seeInSource('success');
    }
}