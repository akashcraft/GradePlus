<?php

use Tests\Support\AcceptanceTester;

class GetAssignmentCest {
    public function getAssignmentById(AcceptanceTester $I) {
        // Set the request headers
        $I->haveHttpHeader('Content-Type', 'application/x-www-form-urlencoded');

        // Send a POST request to the get-assignment endpoint with a valid assignment_id
        $I->sendPost('/services/get-individual-assignment.php', [
            'authorize' => 'gradeplus',
            'assignment_id' => 1 // Use a valid assignment_id from your database
        ]);

        // Verify the response is in JSON format
        $I->seeResponseIsJson();

        // Ensure the response indicates success
        $I->seeResponseContainsJson(['success' => 1]);

        // Check for the presence of specific keys in the response data
        $I->seeResponseContainsJson([
            'data' => [
                'assignment_name' => 'A1',
                'description' => 'I am a description 2',
                'assignment_file' => null,
                'due_date' => null,
                'instructor' => 'Hammed'
            ]
        ]);

        // Optionally verify that the 'illegal' flag is 0
        $I->seeResponseContainsJson(['illegal' => 0]);

        // Check if the message confirms successful retrieval
        $I->seeResponseContains('"message":"Assignment retrieved successfully."');

        // Ensure the response contains specific strings or keywords
        $I->seeInSource('assignment_name');
        $I->seeInSource('due_date');
    }

    public function getAssignmentWithInvalidId(AcceptanceTester $I) {
        // Send a request with an invalid assignment ID
        $I->sendPost('/services/get-individual-assignment.php', [
            'authorize' => 'gradeplus',
            'assignment_id' => 9999 // Use a non-existent ID
        ]);

        // Verify the response is JSON
        $I->seeResponseIsJson();

        // Ensure the response indicates an error
        $I->seeResponseContainsJson(['success' => 0]);

        // Verify that the message reports the assignment was not found
        $I->seeResponseContains('"message":"No assignment found with ID: 9999"');
    }
}
