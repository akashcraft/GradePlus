<?php

use Tests\Support\AcceptanceTester;

class RegisterUserCest {
    public function RegisterUser(AcceptanceTester $I) {
        $I->haveHttpHeader('Content-Type', 'application/x-www-form-urlencoded');

        //Reset the database so I know exactly what is in it
        $I->sendPost('/services/reset-demo.php', [
            'authorize' => 'gradeplus'
        ]);

        //Test that I can make a new account that isn't in the system.
        $I->sendPost('/services/register.php', [
            'authorize' => 'gradeplus',
            'username' => 'elahey',
            'dname' => 'Emma Lahey',
            'email' => 'elahey@mun.ca',
            'password' => 'testPassword',
            'usertype' => 'student'
        ]);

        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(["success" => 1, "exists" => 0, "error" => 0, "empty" => 0, "invalid_email" => 0]);

        $I->seeInSource('success');

        //This is used to make sure that when an account already exists we don't re-add it
        $I->sendPost('/services/register.php', [
            'authorize' => 'gradeplus',
            'username' => 'elahey',
            'dname' => 'Emma Lahey',
            'email' => 'elahey@mun.ca',
            'password' => 'testPassword',
            'usertype' => 'student'
        ]);

        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(["success" => 0, "exists" => 1, "error" => 0, "empty" => 0, "invalid_email" => 0]);

        $I->seeInSource('success');

        //This is used to make sure that when an account that uses the same username we can't
        $I->sendPost('/services/register.php', [
            'authorize' => 'gradeplus',
            'username' => 'mafewer',
            'dname' => 'Marcus Fewer',
            'email' => 'marcus@mun.ca',
            'password' => 'testPassword_1',
            'usertype' => 'student'
        ]);

        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(["success" => 0, "exists" => 1, "error" => 0, "empty" => 0, "invalid_email" => 0]);

        $I->seeInSource('success');

        //This is used to make sure that an empty input won't be accepted
        $I->sendPost('/services/register.php', [
            'authorize' => 'gradeplus',
            'username' => '',
            'dname' => 'Marcus Fewer',
            'email' => 'marcus@mun.ca',
            'password' => 'testPassword_1',
            'usertype' => 'student'
        ]);

        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(["success" => 0, "exists" => 0, "error" => 0, "empty" => 1, "invalid_email" => 0]);

        $I->seeInSource('success');

        //This is used to make sure that a valid email is used
        $I->sendPost('/services/register.php', [
            'authorize' => 'gradeplus',
            'username' => 'marcus',
            'dname' => 'Marcus Fewer',
            'email' => 'marcusmun.ca',
            'password' => 'testPassword_1',
            'usertype' => 'student'
        ]);

        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(["success" => 0, "exists" => 0, "error" => 0, "empty" => 0, "invalid_email" => 1]);

        $I->seeInSource('success');

        //This is used to make sure that a valid email is used
        $I->sendPost('/services/register.php', [
            'authorize' => 'gradeplus',
            'username' => 'marcus',
            'dname' => 'Marcus Fewer',
            'email' => 'marcus@munca',
            'password' => 'testPassword_1',
            'usertype' => 'student'
        ]);

        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(["success" => 0, "exists" => 0, "error" => 0, "empty" => 0, "invalid_email" => 1]);

        $I->seeInSource('success');

    }
}