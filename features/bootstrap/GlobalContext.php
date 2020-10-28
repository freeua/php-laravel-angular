<?php

/**
 * Created by PhpStorm.
 * User: jpicornell
 * Date: 12/10/2018
 * Time: 10:58
 */


use Illuminate\Support\Carbon;
use Laracasts\Behat\Context\DatabaseTransactions;
use Tests\TestCase;
use \Behat\Behat\Context\Context;
use \Behat\Behat\Hook\Scope\BeforeScenarioScope;

class GlobalContext extends TestCase implements Context
{

    use DatabaseTransactions;

    protected $userContext;

    public function __construct()
    {
        parent::setUp();
    }

    /** @BeforeSuite */
    public static function prepare()
    {
        Artisan::call('migrate', ['-v' => true]);
        // Artisan::call('db:seed', ['-v' => true]);
    }

    /**
     * @Given /^time is set to tomorrow$/
     */
    public function timeIsSetTo()
    {
        if(Carbon::getTestNow()) {
            Carbon::setTestNow();
        }
        Carbon::setTestNow(Carbon::tomorrow('UTC'));
    }

    /** @AfterScenario */
    public static function timeIsSetBackToNow()
    {
        if(Carbon::getTestNow()) {
            Carbon::setTestNow();
        }
    }
}
