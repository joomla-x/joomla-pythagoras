# Installer
 
Using composer for 3rd party extensions

## Decision

Composer isn’t usable in mass distributed Joomla because of issues with the time it takes to 
update dependencies and the inability of users to debug failed dependency resolutions. Most 
users also do not have SSH abilities on Shared Hosting and requiring users to build their sites 
on a local machine is not feasible. 

Nevertheless, a composer­like dependency management is wanted for Joomla’s installer. 
Dependencies, which cannot be resolved automatically, are represented to the user (admin) as 
a choice of possible resolutions. 

## Reason

The design for change encourages components to only provide their core functionality and pull 
in other features as needed from other components. As an example, a forum component 
provides the thread management and pulls in any commenting component to manage 
follow­ups. It is up to the site builder to decide ​which commenting system to use.
 
## References

  - White paper by Nic about improving the Joomla Installer system: 
    [Joomla! Extensions Installer TNG.pdf](https://dl.dropboxusercontent.com/u/5168399/Joomla%21%20Extensions%20Installer%20TNG.pdf)
  - Drupal is dealing with composer for D8 between core and third party and how it all affects distro, site management, etc.​
  ​  [drupal.org](https://www.drupal.org/node/2002304​)  has some useful resources.
 
## Discussion

*This is a collection of statements and comments on Glip regarding installation.*

@2015-09-05 05:05
