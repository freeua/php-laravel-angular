@sprint-1
Feature: Leasing Budget


  Scenario: Portal admin changes the Leasing Budget of a company
    Given portal "MyPortal" is created on the system
    And company "MyCompany" is created on the system
    And portal "MyPortal" has the company "MyCompany" associated
    And the leasing budget of "MyCompany" is set to 30000 €
    And portal user "Peter" exists on the portal "MyPortal"
    And "Peter" has the Portal Admin role
    And user "Peter" has logged in to the portal "MyPortal"
    When "Peter" changes the leasing budget of "MyCompany" to 25000€
    Then the leasing budget of "MyCompany" is 25000 €

  Scenario: Company has sufficient leasing budget/w Employee budget achieved
    # This will test when budget for each employee is reached
    Given portal "MyPortal" is created on the system
    And company "MyCompany" is created on the system
    And portal "MyPortal" has the company "MyCompany" associated
    And the leasing budget of "MyCompany" is set to 30000 €
    And company status of "MyCompany" is active
    And allowed number of contracts of "MyCompany" for each employee is 2
    And maximum allowed contracts of "MyCompany" for each employee is 6999€
    And portal user "John" exists on the portal "MyPortal"
    And user "John" has the company "MyCompany" associated
    And user "John" has Employee role
    And "John" has logged in to the employee portal "MyCompany" of the portal "MyPortal"
    And the remaining number of contracts to sign of "John" is 1
    And remaining leasing budget of employee "John" is set to 3000€
    When "John" accepts an offer for 3999€
    Then the application does not allow "John" to accept
    And throw a notification that maximum value of accepted offers exceed 6,999€

  Scenario: Company has insufficient leasing budget
    Given portal "MyPortal" is created on the system
    And company "MyCompany" is created on the system
    And portal "MyPortal" has the company "MyCompany" associated
    And the leasing budget of "MyCompany" is set to 30000 €
    And company status of "MyCompany" is active
    And multiple accepted offers with the value of 28001 € are created for "MyCompany"
    And allowed number of contracts of "MyCompany" for each employee is 2
    And maximum allowed contracts of "MyCompany" for each employee is 6999€
    And portal user "John" exists on the portal "MyPortal"
    And user "John" has the company "MyCompany" associated
    And user "John" has Employee role
    And "John" has logged in to the employee portal "MyCompany" of the portal "MyPortal"
    And the remaining number of contracts to sign of "John" is 1
    And remaining leasing budget of employee "John" is set to 3000€
    When "John" accepts an offer for 2999€
    Then the application does not allow "John" to accept
    And throw a notification message: 'Leasing-Budget erreicht, wenden Sie sich bitte an Ihren Firmenadministrator'.

  Scenario: Employee has insufficient leasing budget and company has insufficient leasing budget
    Given portal "MyPortal" is created on the system
    And company "MyCompany" is created on the system
    And portal "MyPortal" has the company "MyCompany" associated
    And the leasing budget of "MyCompany" is set to 30000 €
    And company status of "MyCompany" is active
    And multiple accepted offers with the value of 27901 € are created for "MyCompany"
    And allowed number of contracts of "MyCompany" for each employee is 3
    And portal user "John" exists on the portal "MyPortal"
    And user "John" has the company "MyCompany" associated
    And user "John" has Employee role
    And "John" has logged in to the employee portal "MyCompany" of the portal "MyPortal"
    And the remaining number of contracts to sign of "John" is 2
    And remaining leasing budget of employee "John" is set to 2000€
    When "John" accepts an offer for 2999€
    Then the application does not allow "John" to accept
    And throw a notification message: 'Leasing-Budget erreicht, wenden Sie sich bitte an Ihren Firmenadministrator'.

  Scenario: Company has sufficient leasing budget and employee has sufficient leasing budget
    Given portal "MyPortal" is created on the system
    And company "MyCompany" is created on the system
    And portal "MyPortal" has the company "MyCompany" associated
    And the leasing budget of "MyCompany" is set to 30000 €
    And company status of "MyCompany" is active
    And allowed number of contracts of "MyCompany" for each employee is 2
    And maximum allowed contracts of "MyCompany" for each employee is 6999€
    And portal user "John" exists on the portal "MyPortal"
    And user "John" has the company "MyCompany" associated
    And user "John" has Employee role
    And "John" has logged in to the employee portal "MyCompany" of the portal "MyPortal"
    And the remaining number of contracts to sign of "John" is 2
    And remaining leasing budget of employee "John" is set to 6999€
    When "John" accepts an offer for 1999€
    Then the remaining leasing budget of company "MyCompany" is 28001€
    And remaining leasing budget of employee "John" is 5000€
    And the remaining number of offers that "John" can accept is 1

  Scenario: Company admin should be able to see leasing budget on company settings page
    Given portal "MyPortal" is created on the system
    And company "MyCompany" is created on the system
    And portal "MyPortal" has the company "MyCompany" associated
    And the leasing budget of "MyCompany" is set to 30000 €
    And portal user "John" exists on the portal "MyPortal"
    And "John" is configured as company admin of "MyCompany"
    And "Read Company Data" permission is added to user "John"
    And "John" has logged in to the company "MyCompany" of the portal "MyPortal"
    When user "John" sees the detail of his company
    Then leasing budget of the detail is 30000

  Scenario: Notify when 80% of the remaining leasing budget is reached
    Given portal "MyPortal" is created on the system
    And company "MyCompany" is created on the system
    And portal "MyPortal" has the company "MyCompany" associated
    And the leasing budget of "MyCompany" is set to 30000 €
    And multiple accepted offers with the value of 23000 € are created for "MyCompany"
    And allowed number of contracts of "MyCompany" for each employee is 2
    And maximum allowed contracts of "MyCompany" for each employee is 6999€
    And portal user "David" exists on the portal "MyPortal"
    And "David" is configured as company admin of "MyCompany"
    And portal user "John" exists on the portal "MyPortal"
    And user "John" has the company "MyCompany" associated
    And user "John" has Employee role
    And "John" has logged in to the employee portal "MyCompany" of the portal "MyPortal"
    And the remaining number of contracts to sign of "John" is 2
    And remaining leasing budget of employee "John" is set to 6999€
    When "John" accepts an offer for 1500€
    Then the remaining leasing budget of company "MyCompany" is 5500€
    And the remaining leasing budget of "MyCompany" is less than the 20% of the leasing budget
    And the system notifies to "MyPortal" Portal admins and System admins of low leasing budget of company "MyCompany"
    But the system not notifies to "MyCompany" Company admins of portal "MyPortal" that company has low leasing budget

  Scenario: Notify when 90% of the remaining leasing budget is reached to Company Admin
    Given portal "MyPortal" is created on the system
    And company "MyCompany" is created on the system
    And portal "MyPortal" has the company "MyCompany" associated
    And the leasing budget of "MyCompany" is set to 30000 €
    And multiple accepted offers with the value of 26000 € are created for "MyCompany"
    And allowed number of contracts of "MyCompany" for each employee is 2
    And maximum allowed contracts of "MyCompany" for each employee is 6999€
    And portal user "David" exists on the portal "MyPortal"
    And "David" is configured as company admin of "MyCompany"
    And portal user "John" exists on the portal "MyPortal"
    And user "John" has the company "MyCompany" associated
    And user "John" has Employee role
    And "John" has logged in to the employee portal "MyCompany" of the portal "MyPortal"
    And the remaining number of contracts to sign of "John" is 2
    And remaining leasing budget of employee "John" is set to 6999€
    When "John" accepts an offer for 1500€
    Then the remaining leasing budget of company "MyCompany" is 2500€
    And the remaining leasing budget of "MyCompany" is less than the 10% of the leasing budget
    And the system notifies to "MyCompany" Company Admins of portal "MyPortal" low leasing budget
    And the system notifies to "MyPortal" Portal admins and System admins of low leasing budget of company "MyCompany"
