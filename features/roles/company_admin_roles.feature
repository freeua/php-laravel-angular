@sprint-1
Feature: Company Admin Roles


  Scenario Outline: Portal admin upgrades employee to any role
    Given portal "MyPortal" is created on the system
    And company "MyCompany" is created on the system
    And portal user "<user>" is created on the portal "MyPortal"
    And portal user "<adminUser>" is created on the portal "MyPortal"
    And "Portal Admin" role is assigned to "<adminUser>"
    And "Employee" role is assigned to "<user>"
    And user "<user>" has the company "MyCompany" associated
    And "<user>" has logged in to the employee portal "MyCompany" of the portal "MyPortal"
    And user "<adminUser>" has logged in to the portal "MyPortal"
    When portal admin "<adminUser>" adds permission "<permission>" to "<user>"
    Then user "<user>" has the permission "<permission>" for the company "MyCompany"
    And "<user>" is able to login to employee portal
    And "<user>" is able to login to company admin

    Examples:
      | user  | adminUser | permission               |
      | Peter | David     | Read Company Data        |
      | John  | Alex      | Edit Company Data        |
      | Jamie | Emy       | Manage Company Employees |


  Scenario: Portal Admin lists employees to assign roles
    Given portal "MyPortal" is created on the system
    And company "MyCompany" is created on the system
    And portal user "John" is created on the portal "MyPortal"
    And portal user "Peter" is created on the portal "MyPortal"
    And "Portal Admin" role is assigned to "Peter"
    And "Employee" role is assigned to "John"
    And "Company Admin" role is assigned to "John"
    And "Read Company Data" permission is added to user "John"
    And "Edit Company Data" permission is added to user "John"
    And user "John" has the company "MyCompany" associated
    And user "Peter" has logged in to the portal "MyPortal"
    When "Peter" lists employees of company "MyCompany"
    Then "John" should be listed

  Scenario: Portal Admin lists company admins to modify
    Given portal "MyPortal" is created on the system
    And company "MyCompany" is created on the system
    And portal user "John" is created on the portal "MyPortal"
    And portal user "Peter" is created on the portal "MyPortal"
    And "Portal Admin" role is assigned to "Peter"
    And "Employee" role is assigned to "John"
    And "Company Admin" role is assigned to "John"
    And "Read Company Data" permission is added to user "John"
    And "Edit Company Data" permission is added to user "John"
    And user "John" has the company "MyCompany" associated
    And user "Peter" has logged in to the portal "MyPortal"
    When "Peter" lists company admins of company "MyCompany"
    Then "John" company admin should be listed with permissions assigned

  Scenario: Portal admin wants to change permission of employee from company admin back to employee
    Given portal "MyPortal" is created on the system
    And company "MyCompany" is created on the system
    And portal user "John" is created on the portal "MyPortal"
    And portal user "Peter" is created on the portal "MyPortal"
    And "Portal Admin" role is assigned to "Peter"
    And "Employee" role is assigned to "John"
    And "Company Admin" role is assigned to "John"
    And user "Peter" has logged in to the portal "MyPortal"
    And portal admin "Peter" adds permission "Read Company Data" to "John"
    And portal admin "Peter" adds permission "Edit Company Data" to "John"
    And portal admin "Peter" adds permission "Manage Company Employees" to "John"
    And user "John" has the company "MyCompany" associated
    When portal admin "Peter" removes all the permissions to "John"
    Then "John" has no "Company Admin" role
    And "John" is not able to login to company admin

  Scenario: When creating a company, company admin should have all the permissions
    Given portal "MyPortal" is created on the system
    And portal user "John" is created on the portal "MyPortal"
    And "Portal Admin" role is assigned to "John"
    And user "John" has logged in to the portal "MyPortal"
    When "John" creates new company "MyCompany"
    Then company "MyCompany" has a Company User with all the permissions

  Scenario: Requesting company permissions should return labels in german
    Given portal "MyPortal" is created on the system
    And portal user "John" is created on the portal "MyPortal"
    And "Portal Admin" role is assigned to "John"
    And user "John" has logged in to the portal "MyPortal"
    When "John" requests the list of permissions
    Then those permissions are shown:
      | permission               | label                    |
      | Manage Company Employees | Mitarbeiter freischalten |
      | Edit Company Data        | Bearbeiten               |
      | Read Company Data        | Lesen                    |


  Scenario: Company Admin "Read Company Data" wants to accept an employee
    Given portal "MyPortal" is created on the system
    And company "MyCompany" is created on the system
    And portal user "John" is created on the portal "MyPortal"
    And portal user "Chris" is created on the portal "MyPortal"
    And user "Chris" status is set to Pending
    And user "John" has the company "MyCompany" associated
    And user "Chris" has the company "MyCompany" associated
    And "Employee" role is assigned to "John"
    And "Employee" role is assigned to "Chris"
    And "Company Admin" role is assigned to "John"
    And "Read Company Data" permission is added to user "John"
    And user "John" has the company "MyCompany" associated
    And "John" has logged in to the company "MyCompany" of the portal "MyPortal"
    When "John" accepts the employee "Chris"
    Then last company request throws an unauthorized action error


  Scenario: Company Admin with permission to "Edit Company Data" wants to accept an employee
    Given portal "MyPortal" is created on the system
    And company "MyCompany" is created on the system
    And portal user "John" is created on the portal "MyPortal"
    And portal user "Chris" is created on the portal "MyPortal"
    And user "Chris" status is set to Pending
    And user "John" has the company "MyCompany" associated
    And user "Chris" has the company "MyCompany" associated
    And "Employee" role is assigned to "John"
    And "Employee" role is assigned to "Chris"
    And "Company Admin" role is assigned to "John"
    And "Edit Company Data" permission is added to user "John"
    And user "John" has the company "MyCompany" associated
    And "John" has logged in to the company "MyCompany" of the portal "MyPortal"
    When "John" accepts the employee "Chris"
    Then last company request throws an unauthorized action error

  Scenario: Company Admin with permission to "Manage Company Employees" wants to accept an employee
    Given portal "MyPortal" is created on the system
    And company "MyCompany" is created on the system
    And portal user "John" is created on the portal "MyPortal"
    And portal user "Peter" is created on the portal "MyPortal"
    And portal user "Chris" is created on the portal "MyPortal"
    And user "Chris" status is set to Pending
    And user "John" has the company "MyCompany" associated
    And user "Chris" has the company "MyCompany" associated
    And "Employee" role is assigned to "John"
    And "Employee" role is assigned to "Chris"
    And "Portal Admin" role is assigned to "Peter"
    And "Company Admin" role is assigned to "John"
    And "Read Company Data" permission is added to user "John"
    And "Manage Company Employees" permission is added to user "John"
    And "John" has logged in to the company "MyCompany" of the portal "MyPortal"
    When "John" accepts the employee "Chris"
    Then user "Chris" is accepted
    And "Chris" is able to login to employee portal

  Scenario: Company Admin "Read Company Data" wants to edit data
    Given portal "MyPortal" is created on the system
    And company "MyCompany" is created on the system
    And portal user "John" is created on the portal "MyPortal"
    And portal user "Peter" is created on the portal "MyPortal"
    And portal user "Chris" is created on the portal "MyPortal"
    And user "Chris" status is set to Pending
    And user "John" has the company "MyCompany" associated
    And user "Chris" has the company "MyCompany" associated
    And "Employee" role is assigned to "John"
    And "Employee" role is assigned to "Chris"
    And "Portal Admin" role is assigned to "Peter"
    And "Company Admin" role is assigned to "John"
    And "Read Company Data" permission is added to user "John"
    And "John" has logged in to the company "MyCompany" of the portal "MyPortal"
    When "John" edits insurance covered amount of company "MyCompany" to "4.1"
    Then last company request throws an unauthorized action error

  Scenario: Company Admin "Edit Company Data" wants to edit data
    Given portal "MyPortal" is created on the system
    And company "MyCompany" is created on the system
    And portal user "John" is created on the portal "MyPortal"
    And portal user "Peter" is created on the portal "MyPortal"
    And "Portal Admin" role is assigned to "Peter"
    And "Employee" role is assigned to "John"
    And "Company Admin" role is assigned to "John"
    And "Read Company Data" permission is added to user "John"
    And "Edit Company Data" permission is added to user "John"
    And user "John" has the company "MyCompany" associated
    And "John" has logged in to the company "MyCompany" of the portal "MyPortal"
    When "John" edits insurance covered amount of company "MyCompany" to "4.1"
    Then insurance covered amount of company "MyCompany" is "4.1"

  Scenario: Company Admin "Edit Company Data" wants to edit suppliers
    Given portal "MyPortal" is created on the system
    And company "MyCompany" is created on the system
    And portal user "John" is created on the portal "MyPortal"
    And portal user "Peter" is created on the portal "MyPortal"
    And "Portal Admin" role is assigned to "Peter"
    And "Employee" role is assigned to "John"
    And "Company Admin" role is assigned to "John"
    And "Read Company Data" permission is added to user "John"
    And "Edit Company Data" permission is added to user "John"
    And user "John" has the company "MyCompany" associated
    And supplier "MySupplier" is created on the portal "MyPortal"
    And "John" has logged in to the company "MyCompany" of the portal "MyPortal"
    When "John" adds supplier "MySupplier" to company "MyCompany"
    Then supplier "MySupplier" is added to company "MyCompany"

  Scenario: Company Admin "Manage Company Employees" wants to edit data
    Given portal "MyPortal" is created on the system
    And company "MyCompany" is created on the system
    And portal user "John" is created on the portal "MyPortal"
    And portal user "Peter" is created on the portal "MyPortal"
    And "Portal Admin" role is assigned to "Peter"
    And "Employee" role is assigned to "John"
    And "Company Admin" role is assigned to "John"
    And "Read Company Data" permission is added to user "John"
    And "Manage Company Employees" permission is added to user "John"
    And user "John" has the company "MyCompany" associated
    And "John" has logged in to the company "MyCompany" of the portal "MyPortal"
    When "John" edits insurance covered amount of company "MyCompany" to "4.1"
    Then last company request throws an unauthorized action error

  Scenario: Company Admin "Read Company Data" wants to access offers
    Given portal "MyPortal" is created on the system
    And company "MyCompany" is created on the system
    And portal user "John" is created on the portal "MyPortal"
    And portal user "Peter" is created on the portal "MyPortal"
    And "Portal Admin" role is assigned to "Peter"
    And "Employee" role is assigned to "John"
    And "Company Admin" role is assigned to "John"
    And "Read Company Data" permission is added to user "John"
    And user "John" has the company "MyCompany" associated
    And "John" has logged in to the company "MyCompany" of the portal "MyPortal"
    When "John" accesses offers of company "MyCompany"
    Then "John" is able to see the offers of company "MyCompany"

  Scenario: Company Admin "Read Company Data" wants to access contracts
    Given portal "MyPortal" is created on the system
    And company "MyCompany" is created on the system
    And portal user "John" is created on the portal "MyPortal"
    And portal user "Peter" is created on the portal "MyPortal"
    And "Portal Admin" role is assigned to "Peter"
    And "Employee" role is assigned to "John"
    And "Company Admin" role is assigned to "John"
    And "Read Company Data" permission is added to user "John"
    And user "John" has the company "MyCompany" associated
    And "John" has logged in to the company "MyCompany" of the portal "MyPortal"
    When "John" accesses contracts of company "MyCompany"
    Then "John" is able to see the offers of company "MyCompany"

  Scenario: Company Admin "Read Company Data" wants to access orders
    Given portal "MyPortal" is created on the system
    And company "MyCompany" is created on the system
    And portal user "John" is created on the portal "MyPortal"
    And portal user "Peter" is created on the portal "MyPortal"
    And "Portal Admin" role is assigned to "Peter"
    And "Employee" role is assigned to "John"
    And "Company Admin" role is assigned to "John"
    And "Read Company Data" permission is added to user "John"
    And user "John" has the company "MyCompany" associated
    And "John" has logged in to the company "MyCompany" of the portal "MyPortal"
    When "John" accesses orders of company "MyCompany"
    Then "John" is able to see the offers of company "MyCompany"

  Scenario: Company Admin "Read Company Data" wants to access company settings
    Given portal "MyPortal" is created on the system
    And company "MyCompany" is created on the system
    And portal user "John" is created on the portal "MyPortal"
    And portal user "Peter" is created on the portal "MyPortal"
    And "Portal Admin" role is assigned to "Peter"
    And "Employee" role is assigned to "John"
    And "Company Admin" role is assigned to "John"
    And "Read Company Data" permission is added to user "John"
    And user "John" has the company "MyCompany" associated
    And "John" has logged in to the company "MyCompany" of the portal "MyPortal"
    When "John" accesses company settings of company "MyCompany"
    Then "John" is able to see the company settings of company "MyCompany"

  Scenario: Company Admin "Read Company Data" wants to access users
    Given portal "MyPortal" is created on the system
    And company "MyCompany" is created on the system
    And portal user "John" is created on the portal "MyPortal"
    And portal user "Peter" is created on the portal "MyPortal"
    And "Portal Admin" role is assigned to "Peter"
    And "Employee" role is assigned to "John"
    And "Company Admin" role is assigned to "John"
    And "Read Company Data" permission is added to user "John"
    And user "John" has the company "MyCompany" associated
    And "John" has logged in to the company "MyCompany" of the portal "MyPortal"
    When "John" accesses users of company "MyCompany"
    Then "John" is able to see the users of company "MyCompany"

  Scenario: Company Admin "Read Company Data" wants to access suppliers
    Given portal "MyPortal" is created on the system
    And company "MyCompany" is created on the system
    And portal user "John" is created on the portal "MyPortal"
    And portal user "Peter" is created on the portal "MyPortal"
    And "Portal Admin" role is assigned to "Peter"
    And "Employee" role is assigned to "John"
    And "Company Admin" role is assigned to "John"
    And "Read Company Data" permission is added to user "John"
    And user "John" has the company "MyCompany" associated
    And "John" has logged in to the company "MyCompany" of the portal "MyPortal"
    When "John" accesses suppliers of company "MyCompany"
    Then "John" is able to see the suppliers of company "MyCompany"

  Scenario: Company Admin without "Read Company Data" wants to access offers
    Given portal "MyPortal" is created on the system
    And company "MyCompany" is created on the system
    And portal user "John" is created on the portal "MyPortal"
    And portal user "Peter" is created on the portal "MyPortal"
    And "Portal Admin" role is assigned to "Peter"
    And "Employee" role is assigned to "John"
    And "Company Admin" role is assigned to "John"
    And user "John" has the company "MyCompany" associated
    And "John" has logged in to the company "MyCompany" of the portal "MyPortal"
    When "John" accesses offers of company "MyCompany"
    Then last request throws an unauthorized action error

  Scenario: Company Admin without "Read Company Data" wants to access contracts
    Given portal "MyPortal" is created on the system
    And company "MyCompany" is created on the system
    And portal user "John" is created on the portal "MyPortal"
    And portal user "Peter" is created on the portal "MyPortal"
    And "Portal Admin" role is assigned to "Peter"
    And "Employee" role is assigned to "John"
    And "Company Admin" role is assigned to "John"
    And user "John" has the company "MyCompany" associated
    And "John" has logged in to the company "MyCompany" of the portal "MyPortal"
    When "John" accesses contracts of company "MyCompany"
    Then last request throws an unauthorized action error

  Scenario: Company Admin without "Read Company Data" wants to access orders
    Given portal "MyPortal" is created on the system
    And company "MyCompany" is created on the system
    And portal user "John" is created on the portal "MyPortal"
    And portal user "Peter" is created on the portal "MyPortal"
    And "Portal Admin" role is assigned to "Peter"
    And "Employee" role is assigned to "John"
    And "Company Admin" role is assigned to "John"
    And user "John" has the company "MyCompany" associated
    And "John" has logged in to the company "MyCompany" of the portal "MyPortal"
    When "John" accesses orders of company "MyCompany"
    Then last request throws an unauthorized action error

  Scenario: Company Admin without "Read Company Data" wants to access company settings
    Given portal "MyPortal" is created on the system
    And company "MyCompany" is created on the system
    And portal user "John" is created on the portal "MyPortal"
    And portal user "Peter" is created on the portal "MyPortal"
    And "Portal Admin" role is assigned to "Peter"
    And "Employee" role is assigned to "John"
    And "Company Admin" role is assigned to "John"
    And user "John" has the company "MyCompany" associated
    And "John" has logged in to the company "MyCompany" of the portal "MyPortal"
    When "John" accesses company settings of company "MyCompany"
    Then last company request throws an unauthorized action error

  Scenario: Company Admin without "Read Company Data" wants to access users
    Given portal "MyPortal" is created on the system
    And company "MyCompany" is created on the system
    And portal user "John" is created on the portal "MyPortal"
    And portal user "Peter" is created on the portal "MyPortal"
    And "Portal Admin" role is assigned to "Peter"
    And "Employee" role is assigned to "John"
    And "Company Admin" role is assigned to "John"
    And user "John" has the company "MyCompany" associated
    And "John" has logged in to the company "MyCompany" of the portal "MyPortal"
    When "John" accesses users of company "MyCompany"
    Then last request throws an unauthorized action error

  Scenario: Company Admin without "Read Company Data" wants to access suppliers
    Given portal "MyPortal" is created on the system
    And company "MyCompany" is created on the system
    And portal user "John" is created on the portal "MyPortal"
    And portal user "Peter" is created on the portal "MyPortal"
    And "Portal Admin" role is assigned to "Peter"
    And "Employee" role is assigned to "John"
    And "Company Admin" role is assigned to "John"
    And user "John" has the company "MyCompany" associated
    And "John" has logged in to the company "MyCompany" of the portal "MyPortal"
    When "John" accesses suppliers of company "MyCompany"
    Then last request throws an unauthorized action error