# ToDo List

This is a non-complete list of tasks, that needs some work.
Feel free to pick one to work on. In order to avoid confusion,
it is appreciated if you give a short notice on the "Joomla 4 Working Group" channel on Glip
or to Niels Braczek via email (nbraczek@bsds.de).

## Events

  - The Event system is missing completely, although the Event package from the framework already is there.
    This is IMO one of the most important things currently.

## ORM

  - The connection of the ORM (`EntityFinder`, `CollectionFinder`, and `Persistor`) to the 
    Doctrine2 DBAL needs to be implemented.
    
  - Implement `store()` asnd `delete()` in `Joomla\ORM\Storage\CsvModel`.
    
## Renderer

  - A list of ContentTypes must be provided. Decision has to be made about, which types have to be
    implemented by the renderers.
    
  - Complete inline documentation (DocBlocks).
    
## Repository

  - The creation of the Repository in `Joomla\Component\Content\Command\AbstractCommand` 
    must be moved to the Dependency Injection Container.

## Service Layer

The CommandBus (and the QueryBus) are implemented in Chris Davenport's Service Layer using Tactician.

  - In the current 'article' example there's just one DisplayCommand together with its handler.
    However, the basic CRUD commands should be generic and be provided by the core,
    so that components don't have to implement them theirselves.
