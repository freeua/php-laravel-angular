<?php
/**
 * Created by PhpStorm.
 * User: jpicornell
 * Date: 16/10/2018
 * Time: 10:41
 */

namespace Company;


use PHPUnit\Framework\Assert;

class ActionsContext extends \FeatureContext
{
    public $lastResponse;

    /**
     * @When /^"([^"]*)" accesses company settings of company "([^"]*)"$/
     */
    public function accessesCompanySettingsOfCompany($userName, $companyName)
    {
        $user = $this->userContext->users[$userName];
        $company = $this->userContext->companies[$companyName];
        $this->lastResponse = $this->get("/company-api/v1/settings",
            [
                'Accept'        => 'application/json',
                'Origin'        => 'http://'.$user->portal->domain,
                'Authorization' => 'Bearer '. $user->accessToken,
                'Company-Slug' => $company->slug,
            ]
        );

        Assert::assertLessThan(500, $this->lastResponse->getStatusCode(),
            'Statuscode of the request was wrong. Response: ' . $this->lastResponse->getContent() );
    }

    /**
     * @When /^"([^"]*)" edits insurance covered amount of company "([^"]*)" to "([^"]*)"$/
     */
    public function editsDataFromCompany($userName, $companyName, $insurance)
    {
        $user = $this->userContext->users[$userName];
        $company = $this->userContext->companies[$companyName];
        $companyGet = $this->get("/company-api/v1/settings",
            [
                'Accept'        => 'application/json',
                'Origin'        => 'http://'.$user->portal->domain,
                'Authorization' => 'Bearer '. $user->accessToken,
                'Company-Slug' => $company->slug,
            ]
        );

        Assert::assertEquals(200, $companyGet->getStatusCode(),
            'Statuscode of the request was wrong. Response: ' . $companyGet->getContent() );
        $companyData = $companyGet->json()['payload'];
        $companyData['insurance_covered_amount'] = $insurance;
        $companyData['status_id'] = $companyData['status']['id'];
        $this->lastResponse = $this->post("/company-api/v1/settings", $companyData,
            [
                'Accept'        => 'application/json',
                'Origin'        => 'http://'.$user->portal->domain,
                'Authorization' => 'Bearer '. $user->accessToken,
                'Company-Slug' => $company->slug,
            ]
        );
        Assert::assertLessThan(500, $this->lastResponse->getStatusCode(),
            'Statuscode of the request was wrong. Response: ' . $this->lastResponse->getContent() );
    }

    /**
     * @When /^"([^"]*)" accepts the employee "([^"]*)"$/
     */
    public function acceptsTheEmployee($companyAdminName, $userName)
    {
        $user = $this->userContext->users[$userName];
        $companyAdminUser = $this->userContext->users[$companyAdminName];
        $this->lastResponse = $this->post("/company-api/v1/users/$user->id/approve", [],
            [
                'Accept'        => 'application/json',
                'Origin'        => 'http://'.$companyAdminUser->portal->domain,
                'Authorization' => 'Bearer '. $companyAdminUser->accessToken,
                'Company-Slug' => $companyAdminUser->company->slug,
            ]
        );
        Assert::assertNotEquals(500, $this->lastResponse->getStatusCode(),
            'Statuscode of the request was wrong. Response: ' . $this->lastResponse->getContent() );
    }

}