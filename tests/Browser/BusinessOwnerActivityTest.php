<?php

namespace Tests\Browser;

use Tests\DuskTestCase;

use App\BusinessOwner;
use App\Employee;
use App\Activity;

use Carbon\Carbon;

class BusinessOwnerActivityTest extends DuskTestCase
{
	/**
     * Check if activity page exists
     *
     * @return void
     */
    public function testActivityPageExists()
    {
    	// Generate business owner
        $owner = factory(BusinessOwner::class)->create();

        $this->browse(function ($browser) use ($owner) {
            $browser->loginAs($owner, 'web_admin')
                // Visit activity page
            	->visit('/admin/activity')
                // Check if route is /admin
            	->assertPathIs('/admin/activity')
                // See if business name exists on page (header title)
                ->assertSee($owner->business_name);
        });
    }

    /**
     * Add activity at at the admin activity page
     * Create a 2 hour activity
     *
     * @return void
     */
    public function testAddActivity()
    {
    	// Generate fake activity data
    	$activity = factory(Activity::class)->make();

        // Creates business owner
        $bo = factory(BusinessOwner::class)->create();

        // Creates an employee
        $employee = factory(Employee::class)->create();

        $this->browse(function ($browser) use ($activity, $bo, $employee) {
            // Login as Business Owner
            $browser->loginAs($bo, 'web_admin')
                // Visit activity page
                ->visit('/admin/activity')
                ->type('name', $activity->name)
                ->type('description', $activity->description)
                ->keys('#input_duration', '02:00')
                ->press('Add Activity')

                // Check success message
                ->assertSee('Activity has successfully been created.')

                // Check if activity exists on the table
                ->assertSee($activity->name)
                ->assertSee($activity->description);
        });
    }

    /**
     * Activity name validation rules
     *
     * @return void
     */
    public function testNameInputValidate()
    {
        // Generate fake activity data
        $activity = factory(Activity::class)->make();

        // Creates business owner
        $bo = factory(BusinessOwner::class)->create();

        // Creates an employee
        $employee = factory(Employee::class)->create();

        $this->browse(function ($browser) use ($activity, $bo, $employee) {
            // Login as Business Owner
            $browser->loginAs($bo, 'web_admin')
                // Visit activity page
                ->visit('/admin/activity')


                // When name is less than 2 characters
                ->type('name', 'a')
                ->press('Add Activity')

                // Check error message
                ->assertSee('The activity name must be at least 2 characters.')


                // When name is greater than 32 characters
                ->type('name', 'aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa')
                ->press('Add Activity')

                // Check error message
                ->assertSee('The activity name may not be greater than 32 characters.');
        });
    }

    /**
     * Activity description validation rules
     *
     * @return void
     */
    public function testDescriptionInputValidate()
    {
        // Generate fake activity data
        $activity = factory(Activity::class)->make();

        // Creates business owner
        $bo = factory(BusinessOwner::class)->create();

        // Creates an employee
        $employee = factory(Employee::class)->create();

        $this->browse(function ($browser) use ($activity, $bo, $employee) {
            // Login as Business Owner
            $browser->loginAs($bo, 'web_admin')
                // Visit activity page
                ->visit('/admin/activity')


                // When description is greater than 64 characters
                ->type('description', 'aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa')
                ->press('Add Activity')

                // Check error message
                ->assertSee('The description may not be greater than 64 characters.');
        });
    }
}