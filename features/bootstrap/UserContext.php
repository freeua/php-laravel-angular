<?php

use App\Models\Companies\Company;
use App\Models\Portal;
use App\Portal\Models\Role;
use App\Portal\Models\User;
use App\Portal\Repositories\CompanyRepository;
use App\System\Models\User as SystemUser;
use App\System\Repositories\PortalRepository;
use PHPUnit\Framework\Assert;

/**
 * Defines application features from the specific context.
 */
class UserContext extends FeatureContext
{

    public $portal;
    public $companies = [];
    public $users = [];

    public $loginResponse;

    /**
     * @Given portal :arg1 is created on the system
     */
    public function portalExistsOnTheSystem($portalName)
    {
        $this->portal = factory(Portal::class)->create([
            'name' => $portalName,
        ]);
        $this->assertDatabaseHas('portals', [
            'name' => $portalName,
        ]);
    }

    /**
     * @Given company :companyName is created on the system
     */
    public function companyExistsOnTheSystem($companyName)
    {
        $company = factory(Company::class)->create([
            'name' => $companyName,
            'status_id' => Company::STATUS_ACTIVE,
        ]);

        $productCategories = \App\Models\ProductCategory::query()->get(['id']);
        foreach ($productCategories as $productCategory) {
            factory(\App\Models\LeasingCondition::class)->create([
                'company_id' => $company->id,
                'product_category_id' => $productCategory->id,
            ]);
        }
        $this->companies[$companyName] = $company;
        Assert::assertArrayHasKey($companyName, $this->companies);
        Assert::assertEquals($this->companies[$companyName]->name, $companyName);
    }

    /**
     * @Given company :companyName is created on the system without leasing conditions
     */
    public function companyExistsOnTheSystemWithoutLeasingConditions($companyName)
    {

        $this->companies[$companyName] = factory(Company::class)->create([
            'name' => $companyName,
            'status_id' => Company::STATUS_ACTIVE,
        ]);;
        Assert::assertArrayHasKey($companyName, $this->companies);
        Assert::assertEquals($this->companies[$companyName]->name, $companyName);
    }

    /**
     * @Given /^portal "([^"]*)" has the company "([^"]*)" associated$/
     */
    public function portalHasTheCompanyAssociated($portalName, $companyName)
    {
        $portal = app(PortalRepository::class)->findBy('name', $portalName);
        $company = app(CompanyRepository::class)->findBy('name', $companyName);
        $company->first()->portal()->associate($portal);
    }

    /**
     * @Given /^portal user "([^"]*)" exists on the portal "([^"]*)"$/
     * @Given /^portal user "(.*)" is created on the portal "(.*)"$/
     */
    public function portalUserExistsOnThePortal($userName, $portalName)
    {
        $portal = app(PortalRepository::class)->findBy('name', $portalName)->first();
        $this->users[$userName] = factory(User::class)->create([
            'first_name' => $userName,
            'portal_id' => $portal->id,
        ]);
        $this->assertDatabaseHas('portal_users', ['first_name' => $userName]);
    }

    /**
     * @Given :arg1 has the Portal Admin role
     * @Given Portal Admin role is assigned to (.*)
     */
    public function hasThePortalAdminRole($userName)
    {
        $this->users[$userName]->guard_name = Role::GUARD_API;
        $this->users[$userName]->assignRole(Role::ROLE_PORTAL_ADMIN);
        $this->assertTrue($this->users[$userName]->hasRole(Role::ROLE_PORTAL_ADMIN));
    }

    /**
     * @Given /^"(.*)" role is assigned to "(.*)"$/
     */
    public function roleIsAssignedTo($role, $userName)
    {
        $this->users[$userName]->guard_name = Role::GUARD_API;
        $this->users[$userName]->assignRole($role);
        $this->users[$userName]->refresh();
        $this->assertTrue($this->users[$userName]->hasRole($role));
    }

    /**
     * @Given user :arg1 has logged in to the portal :arg2
     * @Given user "(.*)" has logged in to the portal "(.*)"
     */
    public function userHasLoggedInToThePortal($userName, $portalName)
    {
        $portal = app(PortalRepository::class)->findBy('name', $portalName)->first();

        $loginResponse = $this->post('/portal-api/v1/login', [
            'email' => $this->users[$userName]->email,
            'password' => 'Aa123654', // Password on factory
        ], ['Accept' => 'application/json', 'Origin' => 'http://'.$portal->domain]);

        Assert::assertEquals(200, $loginResponse->getStatusCode(),
            'Statuscode of the request was wrong. Response: ' . $loginResponse->getContent() );
        $accessToken = $loginResponse->json()['payload']['token']['access_token'];
        $this->users[$userName]->accessToken = $accessToken;
    }

    /**
     * @Given company status of :arg1 is active
     */
    public function companyStatusOfIsActive($companyName)
    {
        $this->companies[$companyName]->update(['status_id' => Company::STATUS_ACTIVE]);
        Assert::assertTrue($this->companies[$companyName]->isActive());
    }

    /**
     * @Given /^user "([^"]*)" has the company "([^"]*)" associated$/
     */
    public function userHasTheCompanyAssociated($userName, $companyName)
    {
        $user = $this->users[$userName];
        $company = $this->companies[$companyName];
        $user->company()->associate($company);
        $user->save();
    }

    /**
     * @Given user :arg1 has Employee role
     */
    public function userHasEmployeeRole($userName)
    {
        $user = $this->users[$userName];
        $user->refresh();
        $user->guard_name = Role::GUARD_API;
        $user->assignRole(Role::ROLE_EMPLOYEE);
        $this->assertTrue($user->hasRole(Role::ROLE_EMPLOYEE));
    }

    /**
     * @Given :arg1 has logged in to the company :arg2 of the portal :arg3
     * @Given "(.*)" has logged in to the company "(.*)" of the portal "(.*)"
     */
    public function hasLoggedInToTheCompanyPortal($userName, $companyName, $portalName)
    {
        $user = $this->users[$userName];
        $portal = app(PortalRepository::class)->findBy('name', $portalName)->first();
        $company = $this->companies[$companyName];
        $loginResponse = $this->post('/company-api/v1/login', [
            'email' => $user->email,
            'password' => 'Aa123654', // Password on factory
        ], [
            'Accept' => 'application/json',
            'Origin' => 'http://'.$portal->domain,
            'Company-Slug' => $company->slug,
        ]);
        Assert::assertEquals(200, $loginResponse->getStatusCode(),
            'Statuscode of the request was wrong. Response: ' . $loginResponse->getContent() );
        $user->accessToken = $loginResponse->json()['payload']['token']['access_token'];
        Assert::assertArrayHasKey('roles', $loginResponse->json('payload')['user']);
        Assert::assertTrue(in_array(Role::ROLE_COMPANY_ADMIN,$loginResponse->json('payload')['user']['roles']));
    }

    /**
     * @Given :arg1 has logged in to the employee portal :arg2 of the portal :arg3
     * @Given "(.*)" has logged in to the employee portal "(.*)" of the portal "(.*)"
     */
    public function hasLoggedInToTheEmployeePortal($userName, $companyName, $portalName)
    {
        $user = $this->users[$userName];
        $portal = app(PortalRepository::class)->findBy('name', $portalName)->first();
        $company = $this->companies[$companyName];
        $loginResponse = $this->post('/company-api/v1/login', [
            'email' => $user->email,
            'password' => 'Aa123654', // Password on factory
        ], [
            'Accept' => 'application/json',
            'Origin' => 'http://'.$portal->domain,
            'Company-Slug' => $company->slug,
        ]);
        Assert::assertEquals(200, $loginResponse->getStatusCode(),
            'Statuscode of the request was wrong. Response: ' . $loginResponse->getContent() );
        $user->accessToken = $loginResponse->json()['payload']['token']['access_token'];
        Assert::assertArrayHasKey('roles', $loginResponse->json('payload')['user']);
        Assert::assertTrue(in_array(Role::ROLE_EMPLOYEE,$loginResponse->json('payload')['user']['roles']));
    }

    /**
     * @Then /^"([^"]*)" is able to login to employee portal$/
     */
    public function isAbleToLoginThroughToPortal($userName)
    {
        $user = $this->userContext->users[$userName];
        $this->loginResponse = $this->post("/company-api/v1/login", [
            'email' => $user->email,
            'password' => 'Aa123654', // Password on factory
        ],
            [
                'Accept'        => 'application/json',
                'Origin'        => 'http://'.$user->portal->domain,
                'Company-Slug'  => $user->company->slug,
            ]
        );
        Assert::assertEquals(200, $this->loginResponse->getStatusCode(),
            'Statuscode of the request was wrong. Response: ' . $this->loginResponse->getContent() );
        Assert::assertArrayHasKey('roles', $this->loginResponse->json('payload')['user']);
        Assert::assertTrue(in_array(Role::ROLE_EMPLOYEE,$this->loginResponse->json('payload')['user']['roles']));
    }

    /**
     * @Then /^"(.*)" is able to login to portal "(.*)"$/
     */
    public function isAbleToLoginToPortal($userName, $portal)
    {
        $portal = Portal::query()->where('name', '=', $portal)->first();
        $user = $this->userContext->users[$userName];
        $this->loginResponse = $this->post("/portal-api/v1/login", [
            'email' => $user->email,
            'password' => 'Aa123654', // Password on factory
        ],
            [
                'Accept'        => 'application/json',
                'Origin'        => 'http://'.$portal->domain,
            ]
        );
        Assert::assertEquals(200, $this->loginResponse->getStatusCode(),
            'Statuscode of the request was wrong. Response: ' . $this->loginResponse->getContent() );
    }

    /**
     * @Then /^"([^"]*)" is able to login to company admin$/
     */
    public function isAbleToLoginToCompanyAdmin($userName)
    {
        $user = $this->userContext->users[$userName];
        $this->loginResponse = $this->post("/company-api/v1/login", [
            'email' => $user->email,
            'password' => 'Aa123654', // Password on factory
        ],
            [
                'Accept'        => 'application/json',
                'Origin'        => 'http://'.$user->portal->domain,
                'Company-Slug'  => $user->company->slug,
            ]
        );
        Assert::assertEquals(200, $this->loginResponse->getStatusCode(),
            'Statuscode of the request was wrong. Response: ' . $this->loginResponse->getContent() );
    }

    /**
     * @Then /^"([^"]*)" is not able to login to company admin$/
     */
    public function isNotAbleToLoginToCompanyAdmin($userName)
    {
        $user = $this->userContext->users[$userName];
        $this->loginResponse = $this->post("/company-api/v1/login", [
            'email' => $user->email,
            'password' => 'Aa123654', // Password on factory
        ],
            [
                'Accept'        => 'application/json',
                'Origin'        => 'http://'.$user->portal->domain,
                'Company-Slug'  => $user->company->slug,
            ]
        );
        Assert::assertEquals(200, $this->loginResponse->getStatusCode(),
            'Statuscode of the request was wrong. Response: ' . $this->loginResponse->getContent() );
        Assert::assertArrayHasKey('roles', $this->loginResponse->json('payload')['user']);
        Assert::assertArrayNotHasKey(Role::ROLE_COMPANY_ADMIN, $this->loginResponse->json('payload')['user']['roles']);
    }


    /**
     * @Given (.*) is configured as company admin of (.*)
     * @Given :arg1 is configured as company admin of :arg2
     */
    public function isConfiguredAsACompanyAdminOf($userName, $companyName)
    {
        $user = $this->users[$userName];
        $user->refresh();
        $company = $this->companies[$companyName];
        $user->company()->associate($company);
        $user->save();
        $user->guard_name = Role::GUARD_API;
        $user->assignRole(Role::ROLE_COMPANY_ADMIN);
        Assert::assertTrue($user->hasRole(Role::ROLE_COMPANY_ADMIN));
        Assert::assertEquals($user->company, $company);
    }

    /**
     * @Then /^"([^"]*)" is able to login to supplier portal$/
     */
    public function isAbleToLoginToSupplierAdmin($userName)
    {
        $user = $this->userContext->users[$userName];
        $this->loginResponse = $this->post("/supplier-api/v1/login", [
            'email' => $user->email,
            'password' => 'Aa123654', // Password on factory
        ],
            [
                'Accept'        => 'application/json',
                'Origin'        => 'http://'.$user->portal->domain,
            ]
        );
        Assert::assertEquals(200, $this->loginResponse->getStatusCode(),
            'Statuscode of the request was wrong. Response: ' . $this->loginResponse->getContent() );
    }

    /**
     * @Given /^user "([^"]*)" status is set to Pending$/
     */
    public function userStatusIsSetToPending($userName)
    {
        $this->users[$userName]->update(['status_id' => User::STATUS_PENDING]);
    }

    /**
     * @Given /^system user "([^"]*)" exists on the system$/
     */
    public function systemUserExistsOnThePortal($userName)
    {
        $this->users[$userName] = factory(SystemUser::class)->create([
            'first_name' => $userName,
        ]);
        $this->assertDatabaseHas('users', ['first_name' => $userName]);
    }

    /**
     * @Given /^user "([^"]*)" logs in to the system admin$/
     */
    public function userLogsInToTheSystemAdmin($userName)
    {
        $loginResponse = $this->post('/system-api/login', [
            'email' => $this->users[$userName]->email,
            'password' => 'Aa123654', // Password on factory
        ], ['Accept' => 'application/json', 'Application-Key' => env('SYSTEM_APP_KEY')]);

        Assert::assertEquals(200, $loginResponse->getStatusCode(),
            'Statuscode of the request was wrong. Response: ' . $loginResponse->getContent() );
        $accessToken = $loginResponse->json()['payload']['token']['access_token'];
        $this->users[$userName]->accessToken = $accessToken;
    }


}
