# Use of database abstraction layer

## Decision

Our intention is to use a DBAL. We will investigate using Doctrine 2’s database abstraction 
layer. We are not going to put an abstraction layer in front of the DBAL. 

## Reason

The DBAL allows us to target all of its supported database types without having to write queries 
specifically for the database type. It supports testing by making database mocking easy to 
accomplish. 
 
Its performance is near to what we can do by manually optimising queries and generally better 
than our under­optimised queries, something we have in common occurrence in Joomla!. In 
case a developer needs to use native queries, for example to get the best out of performance 
optimisation, the DBAL does allow them to do that and even lets them know the database type 
to target it with the correct query format. 
 
Finally, the DBAL has data importers and exporters that work which can create and update both 
the schema and the data of the database. 
 
We are not going to put an abstraction in front of the DBAL because of performance reasons 
and because it has no added value. 

## References

  - [Doctrine 2 DBAL Code](https://github.com/doctrine/dbal/)
  - [Doctrine 2 DBAL Docs](http://doctrine­dbal.readthedocs.org/en/latest/)
   
## Discussion

*This is a collection of statements and comments on Glip regarding database abstraction.*

Nicholas Dionysopoulos
> Robert, only the Database Abstraction Layer (DBAL) of Doctrine is proposed to be included, NOT the Doctrine ORM
> itself. Basically, instead of reinventing the wheel with our db drivers we'll use Doctrine DBAL and get
> cross-database queries for free as well.
>
> I also disagreed on putting Doctrine ORM in J! 4. It's not on the table.

Robert Deutz
> I just thought about putting something like eloquent in, but have to save I didn't really looked into comparing doctrine/eloquent

Nicholas Dionysopoulos
> Hm, we did mention that we'd implement a new MVC. And what I had in mind for a data-aware model is close to Eloquent
> (after all I implemented exactly that in FOF 3 because I find it convenient) but the ultimate decision is left to Herman and Chris.

Herman Peeren
> As for Doctrine DBAL I was looking at writing an adapter between current Joomla querybuilder and Doctrine DBAL querybuilder.
> In that way all queries that were written with Joomla's querybuilder can still be used. "Migration without frustration."
>
> The 2 main advantages of using Doctrine DBAL are:
>
>   1. all PDO-databases are covered without us doing any maintenance
>   2. all db schema creation and the example data are made on 1 spot, not separate for all databases

Nicholas Dionysopoulos
> I agree. Especially if you can write the adapter, that would be just GREAT!

Nicholas Dionysopoulos
> Would it be a safer thing for me to start thinking of how DBAL can fit into Joomla! instead of messing with things I've not used before?

Niels Braczek
> We can start straight ahead with the DBAL; refactoring it the CQRS way isn't that hard.

@2015-09-05 05:05
