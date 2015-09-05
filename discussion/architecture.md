# Architecture

*This is a collection of statements and comments on Glip regarding general architecture questions.*

Herman Peeren uploaded a file
> Design for Change - Thoughts about Joomla 4 Architecture [Joomla4Architecture.pdf](attachments/Joomla4Architecture.pdf)

Andrew Eddie
> anyone looked at Elgg and it's data model?

Marco Dings shared a link
> [nibralab/joomla-architecture](https://github.com/nibralab/joomla-architecture)

Herman Peeren
> Joomla is much more  than just a CMS. Joomla is a platform on which you can build any application. That is the power of Joomla. In the core some CMS-facilities are provided.
> I think it is a wrong starting point, at this stage of Joomla 4 development, to limit ourselves and not look at best practices and modern software development, because most Joomla developers are not (yet) used to it. 

Niels Braczek
> Well, Herman, I still did not see any PoC. What I found out in the world, did not convince me. So, I see no value in ES in our context. But you know, even if i use to have strong oppionions, I am convicible. But not by repeating arguments. I need examples and or code.
>
> You are more on the 'academic' side than I am. Nic is more on the practical side, than I am. I understand most of the concepts you're talking about. I see, however, that most of it is too much to make it in one leap. Being more practical than you (not ment offending!), I prefer to enable Joomla to a. make things better than now, b. evolve into the direction of 'perfect', c. satisfy the current expectations (requirements) of our audience (Joomla! is a mass product).

Nicholas Dionysopoulos
> An event system would work fine if I can guarantee that I have proper caching which usually means in memory caching. The kind of stuff I CANNOT guarantee on a shared hosting account or even most dedicated servers for reasons we all understand.
>
> Since Joomla! is primarily a mass distributed CMS, NOT a framework, we have to cater for that market. If we were writing the next Symfony I would agree on the architecture.

Niels Braczek
> Well, maybe J!5 is a candidate for the next Symfony . J!4 should maybe be seen as a way for educating our developers in that direction. I'd love to build Joomla! in a way, that Martin Fowler would stand up and applaude - but unfortunately, that's not realistic.

Nicholas Dionysopoulos
> I agree. Full ACK.
> We first have to teach developers to walk before we can ask them to be world class sprinters.

George Wilson
> I agree as well.
> It's all about incremental progress

Allon Moritz
> +1 from me too

Herman Peeren
> OK, then it is not for nothing that I stepped out of this group. Good to know. Thank you. No hard feelings, just a totally different goal. We should not hinder each other. You continue with your incremental change, I continue with my quest for a better system. That also fits my market better: custom enterprise applications in a Joomla environment. 

@2015-09-05 05:05
