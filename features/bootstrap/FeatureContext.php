<?php
/**
 * Created by PhpStorm.
 * User: jpicornell
 * Date: 12/10/2018
 * Time: 10:58
 */


use Illuminate\Contracts\Auth\Authenticatable;
use Tests\TestCase;
use \Behat\Behat\Context\Context;
use \Behat\Behat\Hook\Scope\BeforeScenarioScope;

class FeatureContext extends TestCase implements Context
{
    /** @var UserContext */
    protected $userContext;
    /** @var OfferContext */
    protected $offerContext;
    /** @var GlobalContext */
    protected $globalContext;
    /** @var \Company\ActionsContext */
    protected $companyActionsContext;

    public function __construct()
    {
    }

    public function actingAs(Authenticatable $user, $driver = null)
    {
        parent::actingAs($user, $driver);
        auth()->unsetToken();
        $token = JWTAuth::fromUser($user);
        $this->withHeader('Authorization', 'Bearer ' . $token);

        return $this;
    }


    /** @BeforeScenario */
    public function gatherContexts(BeforeScenarioScope $scope)
    {
        $environment = $scope->getEnvironment();
        $this->userContext = $environment->getContext('UserContext');
        $this->offerContext = $environment->getContext('OfferContext');
        $this->globalContext = $environment->getContext('GlobalContext');
        $this->companyActionsContext = $environment->getContext('Company\ActionsContext');
        $this->app = $this->globalContext->app;
    }
}