# Development

## Decision (BDD, TDD)

Although testing is strictly not within the scope of architecture, we want to encourage test­first 
development. Leading by example we will write Behat behavioral tests, integration tests and unit 
tests for the structures we propose. 

## Reason

Red­Green­Refactor will yield better and less buggy code. Good tests will give developer 
documentation, too. 

## Decision (DDD)

Using DDD is at the discretion of the implementers. Working group is to be informed upfront to 
discuss. Core components should be refactored to follow DDD with minimum impact on the 
outside view. 

## Reason

Affects the components (model) and not so much the system as a whole. 
It should be possible to use the same user interface as in J3 (the paradigm change under the 
hood), so current users do not get distracted. 

## Discussion

*This is a collection of statements and comments on Glip regarding general development questions.*

Andrew Eddie shared a link
> I also started this as a thought experiment [https://github.com/icarus/test-driven-joomla](https://github.com/icarus/test-driven-joomla)

@2015-09-02 20:50 UTC
