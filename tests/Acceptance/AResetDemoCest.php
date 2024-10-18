<?php

use Tests\Support\AcceptanceTester;

class AResetDemoCest
{
    public function resetDemo(AcceptanceTester $I)
    {
        // Ensure we are hitting the correct endpoint
        $I->haveHttpHeader('Content-Type', 'application/x-www-form-urlencoded');
        $I->sendPost('/services/reset-demo.php', [
            'authorize' => 'gradeplus'
        ]);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(['success' => 1, 'error' => 0]);
    }
}