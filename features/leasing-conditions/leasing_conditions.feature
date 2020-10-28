@sprint-1
Feature: Leasing Conditions


  Scenario: System admin sets a default leasing condition for portal
    Given portal "MyPortal" is created on the system
    And system user "John" exists on the system
    And user "John" logs in to the system admin
    And leasing condition "MyLeasingCondition" for product category "Fahrrad" is created to portal "MyPortal"
    And leasing condition "MyLeasingCondition2" for product category "Fahrrad" is created to portal "MyPortal"
    When system user "John" sets leasing condition "MyLeasingCondition" as a Default for portal "MyPortal"
    Then leasing condition "MyLeasingCondition" is marked as default for portal "MyPortal"
    And leasing condition "MyLeasingCondition2" is not marked as default for portal "MyPortal"

  Scenario: System admin sets two default leasing condition for the same product category
    Given portal "MyPortal" is created on the system
    And system user "John" exists on the system
    And user "John" logs in to the system admin
    And leasing condition "MyLeasingCondition" for product category "Fahrrad" is created to portal "MyPortal"
    And leasing condition "MyLeasingCondition2" for product category "Fahrrad" is created to portal "MyPortal"
    And system user "John" sets leasing condition "MyLeasingCondition" as a Default for portal "MyPortal"
    When system user "John" sets leasing condition "MyLeasingCondition2" as a Default for portal "MyPortal"
    Then leasing condition "MyLeasingCondition2" is marked as default for portal "MyPortal"
    And leasing condition "MyLeasingCondition" is not marked as default for portal "MyPortal"

  Scenario: System admin lists the leasing conditions of a portal
    Given portal "MyPortal" is created on the system
    And system user "John" exists on the system
    And user "John" logs in to the system admin
    And leasing condition "MyLeasingCondition" for product category "Fahrrad" is created to portal "MyPortal"
    And leasing condition "MyLeasingCondition2" for product category "Fahrrad" is created to portal "MyPortal"
    And system user "John" sets leasing condition "MyLeasingCondition" as a Default for portal "MyPortal"
    When system user "John" lists the leasing conditions of portal "MyPortal"
    Then Leasing conditions of portal "MyPortal" are listed

  Scenario: Portal admin admin sets a default leasing condition for a portal
    Given portal "MyPortal" is created on the system
    And portal user "John" exists on the portal "MyPortal"
    And "Portal Admin" role is assigned to "John"
    And user "John" has logged in to the portal "MyPortal"
    And leasing condition "MyLeasingCondition" for product category "Fahrrad" is created to portal "MyPortal"
    And leasing condition "MyLeasingCondition2" for product category "Fahrrad" is created to portal "MyPortal"
    When portal user "John" sets leasing condition "MyLeasingCondition" as a default for portal "MyPortal"
    Then leasing condition "MyLeasingCondition" is marked as default for portal "MyPortal"
    And leasing condition "MyLeasingCondition2" is not marked as default for portal "MyPortal"

  Scenario: Portal admin sets two default leasing condition for the same product category
    Given portal "MyPortal" is created on the system
    And company "MyCompany" is created on the system
    And portal user "John" exists on the portal "MyPortal"
    And "Portal Admin" role is assigned to "John"
    And user "John" has logged in to the portal "MyPortal"
    And leasing condition "MyLeasingCondition" for product category "Fahrrad" is created to portal "MyPortal"
    And leasing condition "MyLeasingCondition2" for product category "Fahrrad" is created to portal "MyPortal"
    And portal user "John" sets leasing condition "MyLeasingCondition" as a default for portal "MyPortal"
    When portal user "John" sets leasing condition "MyLeasingCondition2" as a default for portal "MyPortal"
    Then leasing condition "MyLeasingCondition2" is marked as default for portal "MyPortal"
    And leasing condition "MyLeasingCondition" is not marked as default for portal "MyPortal"

  Scenario: Portal admin cannot delete a leasing condition of a company, only able to add new and deactivate the older
    Given portal "MyPortal" is created on the system
    And company "MyCompany" is created on the system
    And portal user "John" exists on the portal "MyPortal"
    And "Portal Admin" role is assigned to "John"
    And user "John" has logged in to the portal "MyPortal"
    And leasing condition "MyLeasingCondition" for product category "Fahrrad" is created to company "MyCompany"
    When portal user "John" creates a leasing condition "MyLeasingCondition2" for product category "Fahrrad" to company "MyCompany"
    Then leasing condition "MyLeasingCondition2" is created
    And leasing condition "MyLeasingCondition" of company "MyCompany" is active until the day after deactivation
    And leasing condition "MyLeasingCondition2" of company "MyCompany" is active the day after creation


  Scenario: Portal admin can activate an inactive leasing condition, and the old one becomes inactive
    Given portal "MyPortal" is created on the system
    And company "MyCompany" is created on the system
    And portal user "John" exists on the portal "MyPortal"
    And "Portal Admin" role is assigned to "John"
    And user "John" has logged in to the portal "MyPortal"
    And leasing condition "MyLeasingCondition" for product category "Fahrrad" is created to company "MyCompany"
    And inactive leasing condition "MyLeasingCondition2" for product category "Fahrrad" is created to company "MyCompany"
    When "John" activates leasing condition "MyLeasingCondition2" of company "MyCompany"
    Then leasing condition "MyLeasingCondition2" is created
    And leasing condition "MyLeasingCondition" of company "MyCompany" is active until the day after deactivation
    And leasing condition "MyLeasingCondition2" of company "MyCompany" is active the day after creation

  Scenario: Activating an old leasing and accepting offers same day should pick up old leasing condition, not the new
    Given portal "MyPortal" is created on the system
    And company "MyCompany" is created on the system without leasing conditions
    And portal user "John" exists on the portal "MyPortal"
    And "Portal Admin" role is assigned to "John"
    And portal user "Peter" exists on the portal "MyPortal"
    And user "Peter" has the company "MyCompany" associated
    And "Employee" role is assigned to "Peter"
    And "Company Admin" role is assigned to "Peter"
    And leasing condition "MyLeasingCondition" for product category "Fahrrad" is created to company "MyCompany"
    And inactive leasing condition "MyLeasingCondition2" for product category "Fahrrad" is created to company "MyCompany"
    And "John" activates leasing condition "MyLeasingCondition2" of company "MyCompany"
    And offer for user "Peter" with product category "Fahrrad" is created with accepted status
    When user "Peter" gets contract data for offer created
    Then leasing condition "MyLeasingCondition" is listed as contract leasing setting

  Scenario: Creating a leasing and accepting offers same day should pick up old leasing condition, not the new
    Given portal "MyPortal" is created on the system
    And company "MyCompany" is created on the system without leasing conditions
    And portal user "John" exists on the portal "MyPortal"
    And "Portal Admin" role is assigned to "John"
    And portal user "Peter" exists on the portal "MyPortal"
    And user "Peter" has the company "MyCompany" associated
    And "Employee" role is assigned to "Peter"
    And "Company Admin" role is assigned to "Peter"
    And leasing condition "MyLeasingCondition" for product category "Fahrrad" is created to company "MyCompany"
    And portal user "John" creates a leasing condition "MyLeasingCondition2" for product category "Fahrrad" to company "MyCompany"
    And offer for user "Peter" with product category "Fahrrad" is created with accepted status
    When user "Peter" gets contract data for offer created
    Then leasing condition "MyLeasingCondition" is listed as contract leasing setting

  Scenario: Activating an old leasing and accepting offers the day after should pick up old leasing condition, not the new
    Given portal "MyPortal" is created on the system
    And company "MyCompany" is created on the system without leasing conditions
    And portal user "John" exists on the portal "MyPortal"
    And "Portal Admin" role is assigned to "John"
    And portal user "Peter" exists on the portal "MyPortal"
    And user "Peter" has the company "MyCompany" associated
    And "Employee" role is assigned to "Peter"
    And "Company Admin" role is assigned to "Peter"
    And leasing condition "MyLeasingCondition" for product category "Fahrrad" is created to company "MyCompany"
    And inactive leasing condition "MyLeasingCondition2" for product category "Fahrrad" is created to company "MyCompany"
    And "John" activates leasing condition "MyLeasingCondition2" of company "MyCompany"
    And offer for user "Peter" with product category "Fahrrad" is created with accepted status
    And time is set to tomorrow
    When user "Peter" gets contract data for offer created
    Then leasing condition "MyLeasingCondition2" is listed as contract leasing setting

  Scenario: Creating a leasing and accepting offers the day after should pick up old leasing condition, not the new
    Given portal "MyPortal" is created on the system
    And company "MyCompany" is created on the system without leasing conditions
    And portal user "John" exists on the portal "MyPortal"
    And "Portal Admin" role is assigned to "John"
    And portal user "Peter" exists on the portal "MyPortal"
    And user "Peter" has the company "MyCompany" associated
    And "Employee" role is assigned to "Peter"
    And "Company Admin" role is assigned to "Peter"
    And leasing condition "MyLeasingCondition" for product category "Fahrrad" is created to company "MyCompany"
    And portal user "John" creates a leasing condition "MyLeasingCondition2" for product category "Fahrrad" to company "MyCompany"
    And offer for user "Peter" with product category "Fahrrad" is created with accepted status
    And time is set to tomorrow
    When user "Peter" gets contract data for offer created
    Then leasing condition "MyLeasingCondition2" is listed as contract leasing setting

  Scenario: Company admin should not be able to see leasing conditions
    Given portal "MyPortal" is created on the system
    And company "MyCompany" is created on the system
    And portal user "John" exists on the portal "MyPortal"
    And user "John" has the company "MyCompany" associated
    And "Company Admin" role is assigned to "John"
    And "Read Company Data" permission is added to user "John"
    And "Edit Company Data" permission is added to user "John"
    And "John" has logged in to the company "MyCompany" of the portal "MyPortal"
    When "John" accesses company settings of company "MyCompany"
    Then the company admin is not able to see the Leasing conditions

  Scenario: Default leasing conditions should be returned when creating a new company
    Given portal "MyPortal" is created on the system
    And company "MyCompany" is created on the system
    And portal user "John" exists on the portal "MyPortal"
    And "Portal Admin" role is assigned to "John"
    And user "John" has logged in to the portal "MyPortal"
    And leasing condition "MyLeasingCondition" for product category "Fahrrad" is created to portal "MyPortal"
    And leasing condition "MyLeasingCondition2" for product category "Pedelac" is created to portal "MyPortal"
    And leasing condition "MyLeasingCondition3" for product category "S-Pedelac" is created to portal "MyPortal"
    And leasing condition "OtherLeasingCondition" for product category "Fahrrad" is created to portal "MyPortal"
    And leasing condition "OtherLeasingCondition2" for product category "Pedelac" is created to portal "MyPortal"
    And leasing condition "OtherLeasingCondition3" for product category "S-Pedelac" is created to portal "MyPortal"
    And portal user "John" sets leasing condition "MyLeasingCondition" as a default for portal "MyPortal"
    And portal user "John" sets leasing condition "MyLeasingCondition2" as a default for portal "MyPortal"
    And portal user "John" sets leasing condition "MyLeasingCondition3" as a default for portal "MyPortal"
    When user "John" lists default leasing conditions of portal "MyPortal"
    Then default leasing conditions are listed

  Scenario: leasing conditions should be returned when editing a company
    Given portal "MyPortal" is created on the system
    And company "MyCompany" is created on the system
    And portal user "John" exists on the portal "MyPortal"
    And "Portal Admin" role is assigned to "John"
    And user "John" has logged in to the portal "MyPortal"
    And leasing condition "MyLeasingCondition" for product category "Fahrrad" is created to company "MyCompany"
    And leasing condition "MyLeasingCondition2" for product category "Pedelac" is created to company "MyCompany"
    And leasing condition "MyLeasingCondition3" for product category "S-Pedelac" is created to company "MyCompany"
    And inactive leasing condition "MyLeasingCondition" for product category "Fahrrad" is created to company "MyCompany"
    And inactive leasing condition "MyLeasingCondition2" for product category "Pedelac" is created to company "MyCompany"
    And inactive leasing condition "MyLeasingCondition3" for product category "S-Pedelac" is created to company "MyCompany"
    When user "John" lists leasing conditions of company "MyCompany"
    Then all leasing conditions of company "MyCompany" are listed
