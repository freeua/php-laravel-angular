<?php

use App\Portal\Models\Offer;
use App\Portal\Models\Product;
use App\Models\ProductCategory;
use App\Portal\Models\Supplier;
use App\Portal\Models\User;
use PHPUnit\Framework\Assert;

/**
 * Created by PhpStorm.
 * User: jpicornell
 * Date: 19/10/2018
 * Time: 10:35
 */

class OfferContext extends FeatureContext
{
    /** @var Offer */
    public $offer;
    public $acceptOfferResponse;

    public function createOfferOfForUserWithProductCategory(float $euros, User $user, ProductCategory $productCategory, $status)
    {
        $supplier = app(Supplier::class)->newQuery()->get()->random();
        $user->company->suppliers()->save($supplier);
        $product = Product::query()->where('category_id', $productCategory->id)->get()->random();
        $userSuppliers = app(User::class)->newQuery()
            ->where('supplier_id', '=', $supplier->id)->get();
        return factory(Offer::class)->create([
            'number' => 'TST'.rand(1,1000000),
            'portal_id' => $user->portal->id,
            'company_id' => $user->company->id,
            'product_id' => $product->id,
            'user_id' => $user->id,
            'supplier_user_id' => $userSuppliers->random()->id,
            'discount_price' => $euros,
            'status_id' => $status,
        ]);
    }

    public function createOfferOfFor(float $euros, User $user, $status)
    {
        $productCategory = ProductCategory::query()->newQuery()->get()->random();

        return $this->createOfferOfForUserWithProductCategory($euros, $user, $productCategory, $status);
    }

    /**
     * @Given the remaining number of contracts to sign of :arg1 is :arg2
     */
    public function theRemainingNumberOfContractsToSignOfIs($userName, $remainingContracts)
    {
        $user = $this->userContext->users[$userName];
        $user->refresh();
        $currentRemainingContracts = $user->remaining_sign_contracts;
        while ($remainingContracts < $currentRemainingContracts) {
            $this->createOfferOfFor(100, $user, Offer::STATUS_ACCEPTED);
            $currentRemainingContracts--;
        }
        $user->refresh();
        Assert::assertEquals(intval($remainingContracts), $user->remaining_sign_contracts);
    }

    /**
     * @Given /^offer for user "([^"]*)" is created with accepted status$/
     */
    public function offerForUserIsCreatedWithAcceptedStatus($userName)
    {
        $user = $this->userContext->users[$userName];
        $this->offer = $this->createOfferOfFor(100, $user, Offer::STATUS_ACCEPTED);
        Assert::assertNotNull($this->offer);
        $this->assertDatabaseHas('offers', ['id' => $this->offer->id]);
    }

    /**
     * @Given /^offer for user "([^"]*)" with product category "([^"]*)" is created with accepted status$/
     */
    public function offerForUserWithProductCategoryIsCreatedWithAcceptedStatus($userName, $productCategoryName)
    {
        $user = $this->userContext->users[$userName];
        $productCategory = ProductCategory::query()->where('name', $productCategoryName)->first();
        $this->offer = $this->createOfferOfForUserWithProductCategory(100, $user, $productCategory, Offer::STATUS_ACCEPTED);
        Assert::assertNotNull($this->offer);
        $this->assertDatabaseHas('offers', ['id' => $this->offer->id]);
    }

    /**
     * @When :arg1 accepts an offer for :arg2€
     */
    public function acceptsAnOfferForEur($userName, $offerPrice)
    {
        $user = $this->userContext->users[$userName];
        $offer = $this->createOfferOfFor(floatval($offerPrice), $user, Offer::STATUS_PENDING);
        $this->acceptOfferResponse = $this->post("/employee-api/v1/offers/$offer->id/accept", [], [
            'Accept' => 'application/json',
            'Origin' => 'http://'.$user->portal->domain,
            'Company-Slug' => $user->company->slug,
            'Authorization' => 'Bearer '. $user->accessToken,
        ]);
        $this->assertDatabaseHas('offers', ['id' => $offer->id]);
    }
}
