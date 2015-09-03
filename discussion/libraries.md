# Libraries

*This is a collection of statements and comments on Glip regarding libraries.*

George Wilson
> well the libraries will become one at some point. the joomla folder stuff will go to the framework,
> the cms ones will be the cms and the legacy ones for the large part will be removed as deprecated code

Michael Babker
> Just for reference, it wasn’t packaging that decided to check the vendor folder into the repo, rather some folks
> unwillingness to require contributors be able to run CLI commands to work from Git.  I already have a script based
> on Joomla’s that deals with this and can implement it once the repo has moved to a state where the vendor folder is gitignored.

Niels Braczek
> Would it make problems to include it in the pythagoras repo now? We all know how to type 'composer install'....

George Wilson
> we do. many of our regular contributors do not

Marco Dings
> but we are talking pythagoras now.. so imho that should not be a problem

George Wilson
> No because when pythagoras becomes joomla 4 we are going to want all the regular people to still contribute?

Niels Braczek
> Well, first, we don't have those contributors in pythagoras. Second, with all respect, I'm not sure, that I want
> code contributions from "developers" unable to follow best practices, that are in place for years now.
> All modern IDEs support composer out of the box.

George Wilson
> No but we will in Joomla 4 - which is what pythagoras will become.
> And secondly I'm not joking it's like over 90% of our contributors. We can't just ditch that many people....

Marco Dings
> ditch or educate that will be the question, but your right ( don't you dare quote me on that ) that we should take
> care if there is that many people. Could we use it now and look for a long term solution for the "agnostic"

George Wilson
> i'd say of our regular contributors only me and michael know how to use composer probably

Niels Braczek
> I see it as 'educate'. George, where does that number come from?

George Wilson
> watching many many pull requests where people try to fix something by first core hacking composer,
> then updating the library by overwrtiting the files,
> before me and michael step in and try and help them use composer.

Niels Braczek
> That would not happen, if the vendor library was left out from the repo.
> I use composer all the time, and the only place, where I encounter problems, is Joomla!.

George Wilson
> that's because the majority of our contirbutors aren't proper developers (in case you couldn't tell from the state of our code base :P)

Niels Braczek
> You got the point. Don't we want a better code base? The proper use of Composer just filters out that kind of people, that is incapable of learning.

Robert Deutz
> you can teach a monkey to use composer, he might not understand what he is doing but it will work

Michael Babker
> can people learn?  yes… will they be willing to?  to-be-determined
>
> we’ve been on git for 4 years and that dongle dude still says that move keeps people from contributing because git is
> SOOOOOOOO much more of a developer tool than SVN  [facepalm] 
>
> we all know the “One Right Way™” for composer is to gitignore the vendor folder and we practically shun everyone who
> doesn’t… there are exceptional cases where that one way isn’t going to work for everyone, us adding composer in the
> way we did for 3.4 is one of those exceptions, and we can look over to drupal and see they too have the vendor folder
> checked in (for what reasons i don’t know but they do it)… to me, there is no one right answer to this, in the end
> it’s going to come down to what battles are folks willing to fight and what requirements we’re willing to impose

Niels Braczek
> In the end, I see only two possibilities.
>
>   1. We remove the vendor directory. Contributors will have to learn to type 'composer install'. There are hundreds of tutorials and descriptions for that out in the wild.
>   2. We keep the vendor directory. Then we need to provide documentation and tutorials on how to add / exchange / update libraries without messing up everything.
>
> Have in mind, that the complete `libraries/joomla` directory gets replaced with composer dependencies. In the current situation, I find this task scaring.

Michael Babker
> reading from the drupal-verse on their decision process - https://www.drupal.org/node/1424924 (and associated links)

## Email Validation

Nicholas Dionysopoulos
> [nojacko/email-validator](https://github.com/nojacko/email-validator)
> Just an insteresting library should we wish to add email validation in user registration in Joomla! 4 

George Wilson
> why are they using regex for validating emails instead of the built in php method  (filter_var has a FILTER_VALIDATE_EMAIL)

@2015-09-02 20:50 UTC
