<?php
/**
 * Created by PhpStorm.
 * User: jpicornell
 * Date: 17/10/2018
 * Time: 13:10
 */
namespace Company;

use App\Models\Companies\Company;
use App\Models\LeasingCondition;
use App\Models\LeasingCondition;
use App\Models\Portal;
use App\Models\ProductCategory;
use Carbon\Carbon;
use PHPUnit\Framework\Assert;

class LeasingConditionsContext extends \FeatureContext
{
    public $leasingConditions = [];
    public $lastResponse;
    /**
     * @Given /^leasing condition "([^"]*)" for product category "([^"]*)" is created to portal "([^"]*)"$/
     */
    public function leasingConditionForProductCategoryIsCreatedToPortal($leasingName, $productCategoryName, $portalName)
    {
        $portal = Portal::query()->where('name', $portalName)->first();
        $productCategory = ProductCategory::query()->where('name', $productCategoryName)->first();
        $this->leasingConditions[$leasingName] = factory(LeasingCondition::class)->create([
            'name' => $leasingName,
            'portal_id' => $portal->id,
            'product_category_id' => $productCategory->id,
        ]);
        $this->assertDatabaseHas('portal_leasing_settings', [
            'name' => $leasingName,
        ]);
    }

    /**
     * @When /^system user "([^"]*)" sets leasing condition "([^"]*)" as a Default for portal "([^"]*)"$/
     */
    public function setsLeasingConditionAsADefaultForPortal($userName, $leasingConditionName, $portalName)
    {
        $systemAdminUser = $this->userContext->users[$userName];
        $portal = Portal::query()->where('name', $portalName)->first();
        $leasingCondition = $portal->leasingSettings()->where('name', $leasingConditionName)->first();
        $this->lastResponse = $this->actingAs($systemAdminUser)->put("/system-api/portals/$portal->id/leasing-conditions/$leasingCondition->id", [
            'default' => true,
        ], [
            'Accept'            => 'application/json',
            'Application-Key'  => env('SYSTEM_ADMIN_APP_KEY'),
        ]);
        Assert::assertEquals(200, $this->lastResponse->getStatusCode(),
            'Request statusCode unexpected: ' . $this->lastResponse->getContent());
    }

    /**
     * @When /^portal user "([^"]*)" sets leasing condition "([^"]*)" as a default for portal "([^"]*)"$/
     */
    public function portalUserSetsLeasingConditionAsADefaultForPortal($userName, $leasingConditionName, $portalName)
    {
        $portalAdmin = $this->userContext->users[$userName];
        $portal = $portalAdmin->portal;
        $leasingCondition = $portal->leasingSettings()->where('name', $leasingConditionName)->first();
        $this->lastResponse = $this->put("/portal-api/v1/leasing-conditions/$leasingCondition->id", [
            'default' => true,
        ], [
            'Accept'            => 'application/json',
            'Origin'            => 'http://' . $portal->domain,
            'Authorization'     => 'Bearer '. $portalAdmin->accessToken,
        ]);
        Assert::assertEquals(200, $this->lastResponse->getStatusCode(),
            'Request statusCode unexpected: ' . $this->lastResponse->getContent());
    }

    /**
     * @Then /^leasing condition "([^"]*)" is marked as default for portal "([^"]*)"$/
     */
    public function leasingConditionIsMarkedAsDefaultForPortal($leasingConditionName, $portalName)
    {
        $portal = Portal::query()->where('name', $portalName)->first();
        $leasingCondition = $portal->leasingSettings()->where('name', $leasingConditionName)->first();
        Assert::assertEquals(1, $leasingCondition->default);
    }

    /**
     * @Given /^leasing condition "([^"]*)" is not marked as default for portal "([^"]*)"$/
     */
    public function leasingConditionIsNotMarkedAsDefaultForPortal($leasingConditionName, $portalName)
    {
        $portal = Portal::query()->where('name', $portalName)->first();
        $leasingCondition = $portal->leasingSettings()->where('name', $leasingConditionName)->first();
        Assert::assertEquals(0, $leasingCondition->default);
    }

    /**
     * @When /^system user "([^"]*)" lists the leasing conditions of portal "([^"]*)"$/
     */
    public function systemUserListsTheLeasingConditionsOfPortal($userName, $portalName)
    {
        $systemAdminUser = $this->userContext->users[$userName];
        $portal = Portal::query()->where('name', $portalName)->first();
        $this->lastResponse = $this->get("/system-api/portals/$portal->id", [
            'Accept'            => 'application/json',
            'Application-Key'  => env('SYSTEM_ADMIN_APP_KEY'),
            'Authorization'     => 'Bearer '. $systemAdminUser->accessToken,
        ]);
        Assert::assertEquals(200, $this->lastResponse->getStatusCode(),
            'Request statusCode unexpected: ' . $this->lastResponse->getContent());
    }

    /**
     * @When /^portal user "([^"]*)" lists the leasing conditions of portal "([^"]*)"$/
     */
    public function portalUserListsTheLeasingConditionsOfPortal($userName, $portalName)
    {
        $systemAdminUser = $this->userContext->users[$userName];
        $portal = Portal::query()->where('name', $portalName)->first();
        $this->lastResponse = $this->put("/portal-api/v1/settings", [
            'default' => true,
        ], [
            'Accept'            => 'application/json',
            'Origin'            => 'http://' . $portal->domain,
            'Authorization'     => 'Bearer '. $systemAdminUser->accessToken,
        ]);
        Assert::assertEquals(200, $this->lastResponse->getStatusCode(),
            'Request statusCode unexpected: ' . $this->lastResponse->getContent());
    }

    /**
     * @Then /^Leasing conditions of portal "([^"]*)" are listed$/
     */
    public function leasingConditionsOfAreListed($portalName)
    {
        /** @var Portal $portal */
        $portal = Portal::query()->where('name', $portalName)->first();
        $leasingSettings = $this->lastResponse->json()['payload']['leasing_settings'];
        $names =  array_map(function($leasingSetting) {
            return $leasingSetting['name'];
        }, $leasingSettings);
        $leasingSettingsMap = array_combine($names, $leasingSettings);
        Assert::assertEquals(count($leasingSettings), $portal->leasingConditions()->count());
        $portal->leasingConditions->each(function(LeasingCondition $leasingSetting) use ($leasingSettingsMap) {
            Assert::assertEquals($leasingSetting->name, $leasingSettingsMap[$leasingSetting->name]['name']);
            Assert::assertEquals($leasingSetting->default, $leasingSettingsMap[$leasingSetting->name]['default']);
        });
    }

    /**
     * @Given /^leasing condition "([^"]*)" for product category "([^"]*)" is created to company "([^"]*)"$/
     */
    public function leasingConditionForProductCategoryIsCreatedToCompany($leasingName, $productCategoryName, $companyName)
    {
        $company = Company::query()->where('name', $companyName)->first();
        $productCategory = ProductCategory::query()->where('name', $productCategoryName)->first();
        $this->leasingConditions[$leasingName] = factory(LeasingCondition::class)->create([
            'name' => $leasingName,
            'company_id' => $company->id,
            'product_category_id' => $productCategory->id,
        ]);
        $this->assertDatabaseHas('company_leasing_settings', [
            'name' => $leasingName,
        ]);
    }

    /**
     * @When /^portal user "([^"]*)" creates a leasing condition "([^"]*)" for product category "([^"]*)" to company "([^"]*)"$/
     */
    public function portalUserCreatesLeasingConditionToCompany($userName, $leasingName, $productCategoryName, $companyName)
    {
        $portalUser = $this->userContext->users[$userName];
        $portal = $portalUser->portal;
        $company = Company::query()->where('name', $companyName)->first();
        $productCategory = ProductCategory::query()->where('name', $productCategoryName)->first();
        $leasingCondition = factory(LeasingCondition::class)->make([
            'name' => $leasingName,
            'product_category_id' => $productCategory->id,
        ]);
        $this->lastResponse = $this->actingAs($portalUser)->post("/portal-api/v1/companies/$company->id/leasing-conditions",
            $leasingCondition->toArray(), [
            'Accept'            => 'application/json',
            'Origin'            => 'http://' . $portal->domain,
        ]);
        Assert::assertLessThan(500, $this->lastResponse->getStatusCode(),
            'Request statusCode unexpected: ' . $this->lastResponse->getContent());
    }


    /**
     * @Then /^leasing condition "([^"]*)" is created$/
     */
    public function leasingConditionIsCreated($leasingName)
    {
        Assert::assertLessThan(500, $this->lastResponse->getStatusCode(),
            'Request statusCode unexpected: ' . $this->lastResponse->getContent());
        $this->assertDatabaseHas('company_leasing_settings', ['name' => $leasingName]);
    }

    /**
     * @Then /^leasing condition "([^"]*)" of company "([^"]*)" is active until the day after deactivation$/
     */
    public function leasingConditionOfCompanyIsActiveUntilTheDayAfterDeactivation($leasingName, $companyName)
    {
        $company = $this->userContext->companies[$companyName];
        $leasingCondition = $company->leasingSettings()->where('name', $leasingName)->first();
        Assert::assertEquals(Carbon::tomorrow(new \DateTimeZone('Europe/Berlin')), $leasingCondition->inactive_at);
    }

    /**
     * @Given /^leasing condition "([^"]*)" of company "([^"]*)" is active the day after creation$/
     */
    public function leasingConditionOfCompanyIsActiveTheDayAfterCreation($leasingName, $companyName)
    {
        $company = $this->userContext->companies[$companyName];
        $leasingCondition = $company->leasingSettings()->where('name', $leasingName)->first();
        Assert::assertEquals(Carbon::tomorrow(new \DateTimeZone('Europe/Berlin')), $leasingCondition->active_at);
        Assert::assertNull($leasingCondition->inactive_at);
    }

    /**
     * @Given /^inactive leasing condition "([^"]*)" for product category "([^"]*)" is created to company "([^"]*)"$/
     */
    public function inactiveLeasingConditionForProductCategoryIsCreatedToCompany($leasingName, $productCategoryName, $companyName)
    {
        $company = Company::query()->where('name', $companyName)->first();
        $productCategory = ProductCategory::query()->where('name', $productCategoryName)->first();
        $this->leasingConditions[$leasingName] = factory(LeasingCondition::class)->create([
            'name' => $leasingName,
            'company_id' => $company->id,
            'product_category_id' => $productCategory->id,
            'inactive_at' => Carbon::yesterday(),
        ]);
        $this->assertDatabaseHas('company_leasing_settings', [
            'name' => $leasingName,
        ]);
    }

    /**
     * @Then /^the company admin is not able to see the Leasing conditions$/
     */
    public function theCompanyAdminIsNotAbleToSeeTheLeasingConditions()
    {
        $response = $this->companyActionsContext->lastResponse->json()['payload'];
        Assert::assertArrayNotHasKey('leasing_settings', $response);
    }


    /**
     * @When /^user "([^"]*)" lists default leasing conditions of portal "([^"]*)"$/
     */
    public function userListsDefaultLeasingConditionsOfPortal($userName, $portalName)
    {
        $portalUser = $this->userContext->users[$userName];
        $portal = Portal::query()->where('name', $portalName)->first();
        $this->lastResponse = $this->get("/portal-api/v1/leasing-conditions?default=1", [
                'Accept'            => 'application/json',
                'Origin'            => 'http://' . $portal->domain,
                'Authorization'     => 'Bearer '. $portalUser->accessToken,
            ]);
        Assert::assertLessThan(500, $this->lastResponse->getStatusCode(),
            "Response statusCode is unexpected: ". $this->lastResponse->getContent());

    }

    /**
     * @Then /^default leasing conditions are listed$/
     */
    public function defaultLeasingConditionsAreListed()
    {
        $response = $this->lastResponse->json()['payload'];
        $productCategories = ProductCategory::query()->get();
        Assert::assertEquals($productCategories->count(),count($response));
        foreach ($response as $leasingCondition) {
            Assert::assertEquals(1, $leasingCondition['default']);
        }
    }


    /**
     * @Given /^"([^"]*)" activates leasing condition "([^"]*)" of company "([^"]*)"$/
     */
    public function activatesLeasingConditionOfCompany($userName, $leasingConditionName, $companyName)
    {
        $portalUser = $this->userContext->users[$userName];
        $company = $this->userContext->companies[$companyName];
        $leasingCondition = $company->leasingSettings()->where('name', $leasingConditionName)->first();
        $this->lastResponse =
            $this->actingAs($portalUser)->put("/portal-api/v1/companies/$company->id/leasing-conditions/$leasingCondition->id/activate",
                [], [
                'Accept'            => 'application/json',
                'Origin'            => 'http://' . $portalUser->portal->domain,
            ]);
        Assert::assertEquals(200, $this->lastResponse->getStatusCode(),
            'Request statusCode unexpected: ' . $this->lastResponse->getContent());
    }

    /**
     * @When /^user "([^"]*)" lists leasing conditions of company "([^"]*)"$/
     */
    public function userListsLeasingConditionsOfCompany($userName, $companyName)
    {
        $portalUser = $this->userContext->users[$userName];
        $company = $this->userContext->companies[$companyName];
        $this->lastResponse = $this->get("/portal-api/v1/companies/$company->id", [
            'Accept'            => 'application/json',
            'Origin'            => 'http://' . $portalUser->portal->domain,
            'Authorization'     => 'Bearer '. $portalUser->accessToken,
        ]);
        Assert::assertLessThan(500, $this->lastResponse->getStatusCode(),
            "Response statusCode is unexpected: ". $this->lastResponse->getContent());
    }

    /**
     * @Then /^all leasing conditions of company "([^"]*)" are listed$/
     */
    public function allLeasingConditionsOfAreListed($arg1)
    {
        Assert::assertEquals(200, $this->lastResponse->getStatusCode(),
            "Response statusCode is unexpected: ". $this->lastResponse->getContent());
    }

    /**
     * @When /^user "([^"]*)" gets contract data for offer created$/
     */
    public function userGetsContractDataForOfferCreated($userName)
    {

        $user = $this->userContext->users[$userName];
        $offer = $this->offerContext->offer;
        $this->lastResponse = $this->actingAs($user)->get("/employee-api/v1/offers/$offer->id/contract-data", [
            'Accept'            => 'application/json',
            'Origin'            => 'http://' . $user->portal->domain,
            'Company-Slug'            => $user->company->slug,
        ]);
        Assert::assertLessThan(500, $this->lastResponse->getStatusCode(),
            "Response statusCode is unexpected: ". $this->lastResponse->getContent());
    }

    /**
     * @Then /^leasing condition "([^"]*)" is listed as contract leasing setting$/
     */
    public function leasingConditionIsListedAsContractLeasingSetting($leasingName)
    {
        Assert::assertEquals(200, $this->lastResponse->getStatusCode(),
            "Response statusCode is unexpected: ". $this->lastResponse->getContent());
        $leasingConditions = $this->lastResponse->json()['payload']['user']['company']['leasing_settings'];
        Assert::assertArrayHasKey(0, $leasingConditions);
        Assert::assertEquals($leasingName, $leasingConditions[0]['name']);
    }

}
