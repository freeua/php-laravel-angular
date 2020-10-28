<?php

namespace Company;

use App\Portal\Http\Resources\V1\CompanyLeasingSettingResource;
use App\Portal\Models\Offer;
use App\Portal\Models\Role;
use App\Portal\Models\User;
use App\Portal\Notifications\LeasingBudget\LeasingBudgetLow;
use App\Portal\Repositories\CompanyRepository;
use App\Portal\Repositories\UserRepository;
use App\System\Repositories\PortalRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Notification;
use PHPUnit\Framework\Assert;

/**
 * Defines application features from the specific context.
 */
class LeasingBudgetContext extends \FeatureContext
{

    private $acceptOfferResponse;
    private $companyDetailResponse;

    /**
     * Initializes context.
     *
     * Every scenario gets its own context instance.
     * You can also pass arbitrary arguments to the
     * context constructor through behat.yml.
     */
    public function __construct()
    {
        Notification::fake();
    }

    /**
     * @Given the leasing budget of :arg1 is set to :arg2 €
     */
    public function theLeasingBudgetOfIsSetToEur($companyName, $value)
    {
        // searching for the company
        $company = $this->userContext->companies[$companyName];
        $company->update([
            'leasing_budget' => $value,
        ]);
        Assert::assertEquals($value, $company->leasing_budget);
    }

    /**
     * @When :arg1 changes the leasing budget of :arg2 to :arg3€
     */
    public function changesTheLeasingBudgetOfToEur($userName, $companyName, $newLeasingBudget)
    {
        auth()->setUser($this->userContext->users[$userName]);
        $company = $this->userContext->companies[$companyName];
        $user = $this->userContext->users[$userName];
        $companyData = $company->toArray();
        $companyData['vat'] = 'T'.$company->vat;
        $companyData['leasing_budget'] = floatval($newLeasingBudget);
        $companyData['supplier_ids'] = $company->suppliers->pluck('id')->toArray();
        $companyData['leasing_settings'] = CompanyLeasingSettingResource::collection($company->leasingSettings)->toArray([]);
        $loginResponse = $this->actingAs($user)->post("/portal-api/v1/companies/$company->id", $companyData, [
            'Accept' => 'application/json',
            'Origin' => 'http://'.$user->portal->domain,
        ]);
        Assert::assertEquals(200, $loginResponse->getStatusCode(), $loginResponse->getContent());
    }

    /**
     * @Then the leasing budget of :arg1 is :arg2 €
     */
    public function theLeasingBudgetOfIsEur($company, $leasingBudget)
    {
        $this->userContext->companies[$company]->refresh();
        Assert::assertEquals(floatval($leasingBudget), $this->userContext->companies[$company]->leasing_budget);
    }

    /**
     * @Given allowed number of contracts of :arg1 for each employee is :arg2
     */
    public function allowedNumberOfContractsOfForEachEmployeeIs($companyName, $maxContracts)
    {
        $this->userContext->companies[$companyName]->update(['max_user_contracts' => $maxContracts]);
        Assert::assertEquals(intval($maxContracts), $this->userContext->companies[$companyName]->max_user_contracts);
    }

    /**
     * @Given maximum allowed contracts of :arg1 for each employee is :arg2€
     */
    public function maximumAllowedContractsOfForEachEmployeeIsEur($companyName, $maxAmount)
    {
        $company = $this->userContext->companies[$companyName];
        $company->update(['max_user_amount' => $maxAmount]);
        Assert::assertEquals(intval($maxAmount), $company->max_user_amount);
    }



    /**
     * @Given remaining leasing budget of employee :arg1 is set to :arg2€
     */
    public function remainingLeasingBudgetOfEmployeeIsSetToEur($userName, $remainingLeasingBudget)
    {
        $user = $this->userContext->users[$userName];
        $offer = $user->offers->first();
        $maxAmount = $user->company->max_user_amount;

        if ($maxAmount != $remainingLeasingBudget) {
            // as agreed_purchase_price is not fillable on Contract, we update it to fill our Given premise
            Offer::query()->where('id', '=', $offer->id)
                ->update(['discount_price' => $maxAmount - intval($remainingLeasingBudget)]);
            $offer->refresh();
        }
        Assert::assertEquals(intval($remainingLeasingBudget), $user->remaining_leasing_budget);
    }



    /**
     * @Then the application does not allow :arg1 to accept
     */
    public function theApplicationDoesNotAllowToAccept($arg1)
    {
        Assert::assertEquals(JsonResponse::HTTP_UNPROCESSABLE_ENTITY, $this->offerContext->acceptOfferResponse->getStatusCode(),
            'Statuscode of the request was wrong. Response: ' . $this->offerContext->acceptOfferResponse->getContent() );
    }

    /**
     * @Then throw a notification that maximum value of accepted offers exceed :arg1€
     */
    public function throwANotificationThatMaximumValueOfAcceptedOffersExceedEur($arg1)
    {
        Assert::assertEquals(__('offer.accept.contract_limit'), $this->offerContext->acceptOfferResponse->json()['message'],
            'Message of the error was wrong. Response: ' . $this->offerContext->acceptOfferResponse->getContent() );
    }

    /**
     * @Given multiple accepted offers with the value of :arg1 € are created for :arg2
     */
    public function multipleAcceptedOffersWithTheValueOfAreCreatedFor($offerPrice, $companyName)
    {
        $user = factory(User::class)->create([
            'portal_id' => $this->userContext->portal->id,
        ]);
        $company = $this->userContext->companies[$companyName];
        $user->company()->associate($company);
        $user->save();
        $leasingBudget = $this->userContext->companies[$companyName]->leasing_budget;
        $this->offerContext->createOfferOfFor(floatval($offerPrice), $user, Offer::STATUS_ACCEPTED);
        $company->refresh();
        $remainingLeasingBudget = $leasingBudget - floatval($offerPrice);
        Assert::assertEquals($remainingLeasingBudget, $company->remaining_leasing_budget);
    }

    /**
     * @Then throw a notification message: :arg1.
     */
    public function throwANotificationMessage($errorMessage)
    {
        Assert::assertEquals($errorMessage, $this->offerContext->acceptOfferResponse->json()['message'],
            'Message of the error was wrong. Response: ' . $this->offerContext->acceptOfferResponse->getContent() );
    }

    /**
     * @Then the remaining leasing budget of company :arg1 is :arg2€
     */
    public function theRemainingLeasingBudgetOfCompanyIsEur($company, $remainingLeasingBudget)
    {
        $this->userContext->companies[$company]->refresh();
        Assert::assertEquals(floatval($remainingLeasingBudget), $this->userContext->companies[$company]->remaining_leasing_budget);
    }

    /**
     * @Then the remaining number of offers that :arg1 can accept is :arg2
     */
    public function theRemainingNumberOfOffersThatCanAcceptIs($userName, $remainingNumberOffers)
    {
        $this->userContext->users[$userName]->refresh();
        Assert::assertEquals(floatval($remainingNumberOffers), $this->userContext->users[$userName]->remaining_sign_contracts);
    }

    /**
     * @Given remaining leasing budget of employee :arg1 is :arg2€
     */
    public function remainingLeasingBudgetOfEmployeeIsEur($userName, $remainingLeasingBudget)
    {
        $user = $this->userContext->users[$userName];
        $user->refresh();
        Assert::assertEquals(intval($remainingLeasingBudget), $user->remaining_leasing_budget);
    }

    /**
     * @When user :arg1 sees the detail of his company
     */
    public function userSeesTheDetailOfHisCompany($userName)
    {
        $user = $this->userContext->users[$userName];
        $this->companyDetailResponse = $this->get("/company-api/v1/settings", [
            'Accept' => 'application/json',
            'Origin' => 'http://'.$user->portal->domain,
            'Company-Slug' => $user->company->slug,
            'Authorization' => 'Bearer '. $user->accessToken,
        ]);
        Assert::assertEquals(200, $this->companyDetailResponse->getStatusCode(),
            'Message of the error was wrong. Response: ' . $this->companyDetailResponse->getContent());
    }

    /**
     * @Then leasing budget of the detail is :arg1
     */
    public function leasingBudgetOfTheDetailIs($leasingBudget)
    {
        Assert::assertArrayHasKey('leasing_budget', $this->companyDetailResponse->json()['payload']);
        Assert::assertEquals(floatval($leasingBudget), $this->companyDetailResponse->json()['payload']['leasing_budget']);
    }

    /**
     * @Then the remaining leasing budget of :arg1 is less than the :arg2% of the leasing budget
     */
    public function theRemainingLeasingBudgetOfIsLessThanTheOfTheLeasingBudget($companyName, $percent)
    {
        $company = $this->userContext->companies[$companyName];
        $company->refresh();
        $percentRemainingLeasingBudget = $company->remaining_leasing_budget * 100 / $company->leasing_budget;
        Assert::assertLessThanOrEqual(floatval($percent), $percentRemainingLeasingBudget);
    }

    /**
     * @Given the system not notifies to :arg1 Company admins of portal :arg2 that company has low leasing budget
     */
    public function theSystemNotNotifiesToCompanyAdmins($companyName, $portalName)
    {
        $portal = app(PortalRepository::class)->findBy('name', $portalName)->first();
        $company = app(CompanyRepository::class)->findBy('name', $companyName)->first();
        $companyAdmins = app(UserRepository::class)->findByRole(Role::ROLE_COMPANY_ADMIN, $portal->id, null, $company->id);
        Assert::assertGreaterThan(0, $companyAdmins->count());
        Notification::assertNotSentTo(
            $companyAdmins,
            LeasingBudgetLow::class
        );
    }

    /**
     * @Given the system notifies to :arg1 Portal admins and System admins of low leasing budget of company :arg2
     */
    public function theSystemNotifiesToPortalAdminsAndSystemAdminsOfLowLeasingBudget($portalName, $companyName)
    {
        $portal = app(PortalRepository::class)->findBy('name', $portalName)->first();
        $company = app(CompanyRepository::class)->findBy('name', $companyName)->first();
        $portalAdmins = app(UserRepository::class)->findByRole(Role::ROLE_PORTAL_ADMIN, $portal->id);
        $systemAdmins = app(\App\System\Repositories\UserRepository::class)->all();
        $admins = $portalAdmins->concat($systemAdmins);
        Notification::assertSentTo(
            $admins,
            LeasingBudgetLow::class,
            function ($notification) use ($company) {
                Assert::assertEquals($company, $notification->company);
                return $company->name == $notification->company->name;
            }
        );
    }

    /**
     * @Given the system notifies to :companyName Company Admins of portal :portalName low leasing budget
     */
    public function theSystemNotifiesToCompanyAdminsOfLowLeasingBudget($companyName, $portalName)
    {
        $portal = app(PortalRepository::class)->findBy('name', $portalName)->first();
        $company = app(CompanyRepository::class)->findBy('name', $companyName)->first();
        $companyAdmins = app(UserRepository::class)->findByRole(Role::ROLE_COMPANY_ADMIN, $portal->id, null, $company->id);
        Assert::assertGreaterThan(0, $companyAdmins->count());
        Notification::assertSentTo(
            $companyAdmins,
            LeasingBudgetLow::class,
            function ($notification) use ($company) {
                Assert::assertEquals($company, $notification->company);
                return $company->name == $notification->company->name;
            }
        );
    }

}
