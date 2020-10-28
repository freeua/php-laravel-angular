Feature: Role login check for all modules

  Scenario: Check Portal admin can login to portal
    Given portal "MyPortal" is created on the system
    And portal user "Peter" is created on the portal "MyPortal"
    And "Portal Admin" role is assigned to "Peter"
    Then "Peter" is able to login to portal "MyPortal"

  Scenario: Check Supplier admin can login to supplier
    Given portal "MyPortal" is created on the system
    And portal user "Peter" is created on the portal "MyPortal"
    And "Supplier Admin" role is assigned to "Peter"
    Then "Peter" is able to login to supplier portal

  Scenario: Check Company admin can login to company
    Given portal "MyPortal" is created on the system
    And portal user "Peter" is created on the portal "MyPortal"
    And "Company Admin" role is assigned to "Peter"
    And company "MyCompany" is created on the system
    And user "Peter" has the company "MyCompany" associated
    Then "Peter" is able to login to company admin

  Scenario: Check Employee can login to company
    Given portal "MyPortal" is created on the system
    And portal user "Peter" is created on the portal "MyPortal"
    And "Employee" role is assigned to "Peter"
    And company "MyCompany" is created on the system
    And user "Peter" has the company "MyCompany" associated
    Then "Peter" is able to login to employee portal