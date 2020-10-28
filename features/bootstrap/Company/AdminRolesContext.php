<?php
/**
 * Created by PhpStorm.
 * User: jpicornell
 * Date: 12/10/2018
 * Time: 10:47
 */
namespace Company;

use App\Models\Companies\Company;
use App\Models\Permission;
use App\Models\Portal;
use App\Portal\Models\Role;
use App\Portal\Models\Supplier;
use App\Portal\Models\User;
use Behat\Gherkin\Node\TableNode;
use PHPUnit\Framework\Assert;

class AdminRolesContext extends \FeatureContext
{
    public $permissionRequest;
    public $newCompanyResponse;
    public $companyPermissionsResponse;
    public $lastResponse;

    /**
     * @When /^portal admin "([^"]*)" adds permission "([^"]*)" to "([^"]*)"$/
     */
    public function portalAdminAddsPermissionTo($portalAdminName, $permissionName, $userName)
    {
        $user = $this->userContext->users[$userName];
        $portalAdminUser = $this->userContext->users[$portalAdminName];
        $this->permissionRequest = $this->put("/portal-api/v1/users/$user->id/permissions", [
            'permissions' => [
                $permissionName
            ]],
            [
                'Accept'        => 'application/json',
                'Origin'        => 'http://'.$portalAdminUser->portal->domain,
                'Authorization' => 'Bearer '. $portalAdminUser->accessToken,
            ]
        );
        Assert::assertEquals(200, $this->permissionRequest->getStatusCode(),
            'Statuscode of the request was wrong. Response: ' . $this->permissionRequest->getContent() );
    }

    /**
     * @Then /^user "([^"]*)" has the permission "([^"]*)" for the company "([^"]*)"$/
     */
    public function permissionIsSetToUserForTheCompany($userName, $permissionName, $companyName)
    {
        $user = $this->userContext->users[$userName];
        $company = $this->userContext->companies[$companyName];
        $user->hasPermissionTo($permissionName, 'company');
        Assert::assertEquals($user->company->id, $company->id);
    }

    /**
     * @Given /^"([^"]*)" permission is added to user "([^"]*)"$/
     */
    public function permissionIsAddedToUser($permissionName, $userName)
    {
        $user = $this->userContext->users[$userName];
        $user->guard_name = 'company';
        $user->givePermissionTo($permissionName);
        $user->refresh();
    }

    /**
     * @When /^portal admin "([^"]*)" removes all the permissions to "([^"]*)"$/
     */
    public function portalAdminRemovesAllThePermissions($portalAdminName, $userName)
    {
        $user = $this->userContext->users[$userName];
        $portalAdminUser = $this->userContext->users[$portalAdminName];
        $this->permissionRequest = $this->put("/portal-api/v1/users/$user->id/permissions", [
            'permissions' => [
            ]],
            [
                'Accept'        => 'application/json',
                'Origin'        => 'http://'.$portalAdminUser->portal->domain,
                'Authorization' => 'Bearer '. $portalAdminUser->accessToken,
            ]
        );
        Assert::assertEquals(200, $this->permissionRequest->getStatusCode(),
            'Statuscode of the request was wrong. Response: ' . $this->permissionRequest->getContent() );
    }

    /**
     * @Then /^"([^"]*)" has no "([^"]*)" role$/
     */
    public function hasNoRole($userName, $role)
    {
        $user = $this->userContext->users[$userName];
        $user->refresh();
        Assert::assertFalse($user->hasRole($role, Role::GUARD_API));
    }

    /**
     * @When /^"([^"]*)" requests the list of permissions$/
     */
    public function requestsTheListOfPermissions($userName)
    {
        $user = $this->userContext->users[$userName];
        $this->companyPermissionsResponse = $this->get('portal-api/v1/users/company-permissions', [
            'Accept' => 'application/json',
            'Origin' => 'http://'.$user->portal->domain,
            'Authorization' => 'Bearer '. $user->accessToken,
        ]);
        Assert::assertEquals(200, $this->companyPermissionsResponse->getStatusCode(),
            'Message of the error was wrong. Response: ' . $this->companyPermissionsResponse->getContent());
    }

    /**
     * @Then /^those permissions are shown:$/
     */
    public function thosePermissionsAreShown(TableNode $table)
    {
        $response = $this->companyPermissionsResponse->json()['payload'];
        $names =  array_map(function($permission) {
            return $permission['name'];
        }, $response);
        $permissionMap = array_combine($names, $response);
        foreach($table->getHash() as $row) {
            Assert::assertContains($row['permission'], $names, "there's no permission returned");
            Assert::assertEquals($permissionMap[$row['permission']]['label'], $row['label']);
        }
    }

    /**
     * @When /^"([^"]*)" creates new company "([^"]*)"$/
     */
    public function createsNewCompany($userName, $companyName)
    {
        $user = $this->userContext->users[$userName];
        $companyData = factory(Company::class)->make(['name' => $companyName])->toArray();
        $supplier = factory(Supplier::class)->create();
        $companyData['vat'] = 'T'.$companyData['vat'];
        $companyData['supplier_ids'] = [$supplier->id];
        $productCategories = \App\Models\ProductCategory::query()->get(['id']);
        $leasingSettings = [];
        foreach ($productCategories as $productCategory) {
            $leasingSettings[] = factory(\App\Models\LeasingCondition::class)->make([
                'product_category_id' => $productCategory->id,
            ])->toArray();
        }
        $companyData['leasing_settings'] = $leasingSettings;
        $this->newCompanyResponse = $this->post("/portal-api/v1/companies", $companyData, [
            'Accept' => 'application/json',
            'Origin' => 'http://'.$user->portal->domain,
            'Authorization' => 'Bearer'. $user->accessToken,
        ]);
        Assert::assertEquals(200, $this->newCompanyResponse->getStatusCode(),
            'Statuscode of the request was wrong. Response: ' . $this->newCompanyResponse->getContent() );

    }

    /**
     * @Then /^company "([^"]*)" has a Company User with all the permissions$/
     */
    public function companyHasACompanyUserWithAllThePermissions($companyName)
    {
        $company = Company::query()->where(['name' => $companyName])->first();
        $user = User::query()->where('company_id', '=', $company->id)->first();
        $permissions = Permission::query()->where('guard_name', '=', 'company')->get();
        Assert::assertNotEmpty($permissions);
        foreach($permissions as $permission) {
            Assert::assertTrue($user->hasPermissionTo($permission), "User of company $companyName has no $permission->name");
        }
    }


    /**
     * @Then /^user "([^"]*)" is accepted$/
     */
    public function userIsAccepted($userName)
    {
        $user = $this->userContext->users[$userName];
        $user->refresh();
        Assert::assertEquals(User::STATUS_ACTIVE, $user->status_id);
    }


    /**
     * @Then /^last request throws an unauthorized action error$/
     */
    public function theUserGetsAnErrorOfUnauthorizedAction()
    {
        $lastResponse = $this->lastResponse;
        Assert::assertEquals(403, $lastResponse->getStatusCode(),
            'Statuscode of the request was wrong. Response: ' . $lastResponse->getContent() );
    }

    /**
     * @Then /^last company request throws an unauthorized action error$/
     */
    public function lastCompanyRequestTrowsAnUnauthorizedError()
    {
        $lastResponse = $this->companyActionsContext->lastResponse;
        Assert::assertEquals(403, $lastResponse->getStatusCode(),
            'Statuscode of the request was wrong. Response: ' . $lastResponse->getContent() );
    }

    /**
     * @Then /^insurance covered amount of company "([^"]*)" is "([^"]*)"$/
     */
    public function theDataEditedOnCompanyIsReflected($companyName, $insurance)
    {
        $company = $this->userContext->companies[$companyName];
        $company->refresh();
        $lastResponse = $this->companyActionsContext->lastResponse;
        Assert::assertEquals(200, $lastResponse->getStatusCode(),
            'Statuscode of the request was wrong. Response: ' . $lastResponse->getContent() );
        Assert::assertEquals($insurance, $company->insurance_covered_amount);
    }

    /**
     * @Given /^supplier "([^"]*)" is created on the portal "([^"]*)"$/
     */
    public function supplierIsCreatedOnThePortal($supplierName, $portalName)
    {
        $portal = Portal::query()->where('name', '=', $portalName)->first();
        $supplier = factory(Supplier::class)->create(['name' => $supplierName]);
        $supplier->portals()->save($portal, ['status_id' => Supplier::STATUS_ACTIVE]);
        $this->assertDatabaseHas('suppliers', ['name' => $supplierName]);
        $this->assertDatabaseHas('portal_supplier', ['supplier_id' => $supplier->id, 'portal_id' => $portal->id]);
    }

    /**
     * @When /^"([^"]*)" adds supplier "([^"]*)" to company "([^"]*)"$/
     */
    public function editsAddsSupplierToCompany($userName, $supplierName, $companyName)
    {
        $company = Company::query()->where('name', '=', $companyName)->first();
        $supplier = Supplier::query()->where('name', '=', $supplierName)->first();
        $user = $this->userContext->users[$userName];
        $this->lastResponse = $this->post("/company-api/v1/suppliers", [
            'ids' => [$supplier->id],
        ],
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
     * @Then /^supplier "([^"]*)" is added to company "([^"]*)"$/
     */
    public function supplierIsAddedToTheCompany($supplierName, $companyName)
    {
        /** @var Company $company */
        $company = Company::query()->where('name', '=', $companyName)->first();
        $supplier = Supplier::query()->where('name', '=', $supplierName)->first();
        $supplierFind = $company->suppliers()->find($supplier->id);
        Assert::assertEquals(200, $this->lastResponse->getStatusCode(),
        'Statuscode of the request was wrong. Response: ' . $this->lastResponse->getContent() );
        Assert::assertNotNull($supplierFind);
    }

    /**
     * @When /^"([^"]*)" accesses offers of company "([^"]*)"$/
     */
    public function accessesOffersOfCompany($userName, $companyName)
    {
        $user = $this->userContext->users[$userName];
        $company = $this->userContext->companies[$companyName];
        $this->lastResponse = $this->get("/company-api/v1/offers",
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
     * @Then /^"([^"]*)" is able to see the offers of company "([^"]*)"$/
     */
    public function isAbleToSeeTheOffersOfCompany($arg1, $arg2)
    {

        Assert::assertEquals(200, $this->lastResponse->getStatusCode(),
            'Statuscode of the request was wrong. Response: ' . $this->lastResponse->getContent() );
    }

    /**
     * @When /^"([^"]*)" accesses contracts of company "([^"]*)"$/
     */
    public function accessesContractsOfCompany($userName, $companyName)
    {
        $user = $this->userContext->users[$userName];
        $company = $this->userContext->companies[$companyName];
        $this->lastResponse = $this->get("/company-api/v1/contracts",
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
     * @When /^"([^"]*)" accesses orders of company "([^"]*)"$/
     */
    public function accessesOrdersOfCompany($userName, $companyName)
    {
        $user = $this->userContext->users[$userName];
        $company = $this->userContext->companies[$companyName];
        $this->lastResponse = $this->get("/company-api/v1/orders",
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
     * @Then /^"([^"]*)" is able to see the company settings of company "([^"]*)"$/
     */
    public function isAbleToSeeTheCompanySettingsOfCompany($arg1, $arg2)
    {
        $lastResponse = $this->companyActionsContext->lastResponse;
        Assert::assertEquals(200, $lastResponse->getStatusCode(),
            'Statuscode of the request was wrong. Response: ' . $lastResponse->getContent() );
    }

    /**
     * @When /^"([^"]*)" accesses users of company "([^"]*)"$/
     */
    public function accessesUsersOfCompany($userName, $companyName)
    {
        $user = $this->userContext->users[$userName];
        $company = $this->userContext->companies[$companyName];
        $this->lastResponse = $this->get("/company-api/v1/users",
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
     * @Then /^"([^"]*)" is able to see the users of company "([^"]*)"$/
     */
    public function isAbleToSeeTheUsersOfCompany($arg1, $arg2)
    {
        Assert::assertEquals(200, $this->lastResponse->getStatusCode(),
            'Statuscode of the request was wrong. Response: ' . $this->lastResponse->getContent() );
    }

    /**
     * @When /^"([^"]*)" accesses suppliers of company "([^"]*)"$/
     */
    public function accessesSuppliersOfCompany($userName, $companyName)
    {
        $user = $this->userContext->users[$userName];
        $company = $this->userContext->companies[$companyName];
        $this->lastResponse = $this->get("/company-api/v1/suppliers",
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
     * @Then /^"([^"]*)" is able to see the suppliers of company "([^"]*)"$/
     */
    public function isAbleToSeeTheSuppliersOfCompany($arg1, $arg2)
    {
        Assert::assertEquals(200, $this->lastResponse->getStatusCode(),
            'Statuscode of the request was wrong. Response: ' . $this->lastResponse->getContent() );
    }

    /**
     * @When /^"([^"]*)" lists employees of company "([^"]*)"$/
     */
    public function listsEmployeesOfCompany($userName, $companyName)
    {
        $user = $this->userContext->users[$userName];
        $company = $this->userContext->companies[$companyName];
        $this->lastResponse = $this->get("/portal-api/v1/companies/$company->id/employees",
            [
                'Accept'        => 'application/json',
                'Origin'        => 'http://'.$user->portal->domain,
                'Authorization' => 'Bearer '. $user->accessToken,
            ]
        );

        Assert::assertLessThan(500, $this->lastResponse->getStatusCode(),
            'Statuscode of the request was wrong. Response: ' . $this->lastResponse->getContent() );
    }

    /**
     * @Then /^"([^"]*)" company admin should be listed with permissions assigned$/
     */
    public function shouldBeListedWithPermissionsAssigned($userName)
    {
        $user = $this->userContext->users[$userName];
        $companyAdmins = $this->lastResponse->json()['payload'];
        Assert::assertEquals(200, $this->lastResponse->getStatusCode(),
            'Statuscode of the request was wrong. Response: ' . $this->lastResponse->getContent() );
        Assert::assertEquals(1, count($companyAdmins));
        $companyAdmin = array_first($companyAdmins, function($companyAdmin) use ($userName) {
          return $companyAdmin['first_name'] === $userName;
        });
        Assert::assertEquals($user->permissions->count(), count($companyAdmin['permissions']));
    }

    /**
     * @Then /^"([^"]*)" should be listed$/
     */
    public function shouldBeListed($userName)
    {
        $user = $this->userContext->users[$userName];
        $employees = $this->lastResponse->json()['payload'];
        Assert::assertEquals(200, $this->lastResponse->getStatusCode(),
            'Statuscode of the request was wrong. Response: ' . $this->lastResponse->getContent() );
        Assert::assertEquals(1, count($employees));
        $employee = array_first($employees, function($employee) use ($userName) {
            return $employee['first_name'] === $userName;
        });
        Assert::assertEquals($user->first_name, $employee['first_name']);
    }

    /**
     * @When /^"([^"]*)" lists company admins of company "([^"]*)"$/
     */
    public function listsCompanyAdminsOfCompany($userName, $companyName)
    {
        $user = $this->userContext->users[$userName];
        $company = $this->userContext->companies[$companyName];
        $this->lastResponse = $this->get("/portal-api/v1/companies/$company->id/admins",
            [
                'Accept'        => 'application/json',
                'Origin'        => 'http://'.$user->portal->domain,
                'Authorization' => 'Bearer '. $user->accessToken,
            ]
        );

        Assert::assertLessThan(500, $this->lastResponse->getStatusCode(),
            'Statuscode of the request was wrong. Response: ' . $this->lastResponse->getContent() );
    }


}
