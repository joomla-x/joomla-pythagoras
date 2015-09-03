# Filesystem Abstraction

## Decision

We will use flysystem as our filesystem abstraction layer. 

## Reason

Framework already has planned to create a wrapper for the version 2 Flysystem package.  It 
has existing adapters for FTP, SFTP and Local wrappers. The AWS adapter might prove to be 
useful when rebuilding the media manager. 

## References

  - [Flysystem](https://github.com/thephpleague/flysystem)

## Discussion

*This is a collection of statements and comments on Glip regarding filesystem questions.*

Herman Peeren
> About getting FlySystem also avalable for Joomla 3.x:
>
> yesterday I wrote a mail about it to Frank de Jonge from FlySystem. About backporting to php5.3 he wrote back
> (in Dutch, translation mine): "Backporting to 5.3 is not a tough job, but it has been done already (read: less
> work for you). The guys from BoltCMS maintain (very regularly)a fork that is 5.3 supported:
> [rossriley/flysystem](https://github.com/rossriley/flysystem).
> That might also be useful for Joomla. Shared maintenance (if necessary) would be less load for everybody."
>
> after the hangout I also looked at short arrray syntax in FlySystem and it was a bit more than I thought:
> I counted 70 times (core system without extra adapters and without cache). So I was very happy with Frank's message.
> I'll get in touch with Ross Riley about it.
>
> Yesterday late David White wrote about J!3.x having to support PHP 5.3.10: "it's a shame really as PHP 5.3 support
> is starting to be dropped already by the likes of Akeeba but we still have to keep it going for a while longer".
> Yes, but that is a concequence if we would like te have FlySystem available in Joomla 3 core. A solution would be
> to just get FlySystem for Joomla 3 available as not-core-supported extension, maybe even only combined with an
> alternative Media Manager. Available for those who want to use it. And then only available for PHP 5.4+.
> However I'd like to try first to get it core supported as we will have Joomla 3 around for the coming 3 years at least. 

George Wilson
> Actually I'd be open to sharing with Bolt. That's much more preferable to me than having to maintain it ourselves.

Niels Braczek
> If Ross Riley's version is a drop-in for the original FlySystem (I'm missing such a statement on the project page),
> I'd say, that's the way to go.

George Wilson
> As it's 158 commits behind I'd say it's a complete fork but it's still the same interface

Niels Braczek
> Yes, but that's a guess. No promise about being a 5.3 compatible version of the original. I just want to avoid overlooking something.

George Wilson
> [convert important tests to run on 53 · rossriley/flysystem@07f9509](https://github.com/rossriley/flysystem/commit/07f95099b3809ca782532f6cf840a54ee0f8c111)
> there's a wholebunch of commits backporting it to 5.3

Niels Braczek
> You know, George, I'm German  I'm willing to believe, that it fits our needs. But at least,
> I'd like to have a promise (i.e., official statement, ideally in the project's `README.md`).

Herman Peeren
> I'll get in contact with him to check all questions. I'd also like to take responsibility for it too, so I'll
> check with my own finds of where FlySystem is not 5.3-supported. I want to guarantee (and be the person to support
> that and be accountable for it in the future) that the version we use in J!3 is >=php5.3.10.

George Wilson
> OK. The only other worry i have is that Bolt CM is build on Silex + Symfony - so as soon as symfony 3 comes out in
> november there's a risk they could upgrade - so if you could find out what there's plans are there Herman that would
> be good. We don't want to contribute to this now only to find out in december that they've dropped it as they upgrade
> to symfony 3 and php 5.5 min

Herman Peeren
> That is also why I want to take responsibility and be accountable for support as long as J3.x is supported.
> Preferably with someone else (to get a higher bus-factor).
> And I think promises like that should be registred in our own readme file.

Michael Babker
> it’s gonna depend on how bolt moves with symfony releases, 2.3 and 2.7 are symfony’s LTS’s which are supporting
> PHP 5.3 so unless they jump off that track it shouldn’t be a major issue, still a valid question to ask though

George Wilson
> yah i mean the first LTS symfony 3 is 3.3 in may 2017 - so it may be that that's enough - but as you say it's still a question we should ask

@2015-09-02 20:50 UTC
