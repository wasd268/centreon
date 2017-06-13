Feature: Edit host
    As a Centreon user
    I want to configure an host
    To update its properties

    Background:
       Given I am logged in a Centreon server

    Scenario: Edit parent of an host template
        Given a configured host
        When I modify the ip address
        Then the ip address is updated
