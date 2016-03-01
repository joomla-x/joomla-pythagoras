# ToDo List

This is a non-complete list of tasks, that needs some work.
Feel free to pick one to work on. In order to avoid confusion,
it is appreciated if you give a short notice on the "Joomla 4 Working Group" channel on Glip
or to Niels Braczek via email (nbraczek@bsds.de).

## ORM

  - The connection of the ORM (`EntityFinder`, `CollectionFinder`, and `Persistor`) to the 
    Doctrine2 DBAL needs to be implemented.
    
## Renderer

  - A list of ContentTypes must be provided. Decision has to be made about, which types have to be
    implemented by the renderers.
    
## Repository

  - The creation of the Repository in `Joomla\Component\Content\Command\AbstractCommand` 
    must be moved to the Dependency Injection Container.
