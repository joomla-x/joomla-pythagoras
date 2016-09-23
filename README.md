[![Stories in Ready](https://badge.waffle.io/joomla-projects/joomla-pythagoras.png?label=ready&title=Ready)](https://waffle.io/joomla-projects/joomla-pythagoras)
# Joomla! X Clean Base Branch

[![Build Status](https://travis-ci.org/joomla-projects/joomla-pythagoras.svg?branch=clean-base)](https://travis-ci.org/joomla-projects/joomla-pythagoras)

This branch contains the current development of Joomla! X (currently, X == 4),
based on a clean-base approach.

## Why Clean Base?

We honestly tried an incremental approach, i.e., to transform J!3 into J!4 using small steps.
The tight coupling and non-sufficient test coverage, however, makes it extremely hard to apply a state-of-the-art architecture.

It was suggested to start from a fresh ground, the clean base, to create the basic architecture,
and to port existing functionality in a second step.
The J!4 Architecture Team decided to try this approach.

## The "Chris Davenport Happy Milestone" (CDHM)

The team defined a milestone, named after the team member, Chris Davenport, who brought up the requirements.
You can see the original document here: [docs/j4cdhms.md](docs/j4cdhms.md).

The final decision is made, when the milestone is reached, or it shows out, that it can not be reached with the clean base approach.

## Installation

As there is no installer yet, a manual installation is required.
Check out this branch (`clean-base`), and run

```bash
$ composer install
```

## Contribute

Joomla! X is a big endeavour, and it surely needs some more hands on it.

  - Add issues to the tracker to discuss things.
  - Fork this repo, write code, and send a PR against this branch.
    Before sending your PR, please check your contribution first using
    
    ```bash
    $ ./libraries/vendor/bin/robo test:unit
    $ ./libraries/vendor/bin/robo check:style
    ```
    
Thank you!

The Joomla! 4 Architecture Team
