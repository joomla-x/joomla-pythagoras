# Workflow

The Workflow Horizontal Component does not really deserve its name - it just adds some states to other entities:

## Preparation

Install the demo. Check out the `master` branch, and enter

```bash
$ composer install
$ ./install.sh
```

on the console. This will install a couple of components, and register their entities.

```bash
Joomla! installation expected in /home/nibra/Development/joomla-pythagoras
 - installing extensions/Article
   - Article
 - installing libraries/incubator/Media
   - Image
 - installing libraries/incubator/PageBuilder
   - Content
   - Layout
   - Page
   - Template
 - finishing
Installed 3 extension(s)
```

## Testing

Now, select the blog articles:

```bash
$ ./joomla show articles --dump-sql --filter="category=blog"
```

You'll see a list of articles, and also an SQL dump.

```bash
+----+----------------+----------+------------------+--------------------------------+----------------------------------+------------+---------+-----------+
| id | title          | category | alias            | teaser                         | body                             | author     | license | parent_id |
+----+----------------+----------+------------------+--------------------------------+----------------------------------+------------+---------+-----------+
| 1  | First Article  | blog     | first-article    | Look at the first example      | This first example is cool       | John Doe   | CC      | 0         |
| 2  | Second Article | blog     | second-article   | Look at another example        | This provides child elements     | Doctor     | CC      | 0         |
| 3  | Part One       | blog     | second-article-1 | No. 1 of 2. article's children | This is the first child element  | Rose Tyler | CC      | 2         |
| 4  | Part Two       | blog     | second-article-2 | No. 2 of 2. article's children | This is the second child element | Doctor     | CC      | 2         |
+----+----------------+----------+------------------+--------------------------------+----------------------------------+------------+---------+-----------+
+---+----------------------------+----------+
| # | SQL                        | Time     |
+---+----------------------------+----------+
| 1 | SELECT * FROM articles a   | 0.760 ms |
|   |   WHERE a.category = blog  |          |
|   |   LIMIT -1 OFFSET 0        |          |
|   |                            |          |
+---+----------------------------+----------+
```

Please notice, that the Article Vertical Component does not know anything about states.
 
Next, install the Workflow Horizontal Component:

```bash
$ ./joomla install extensions/Workflow
```

The installer acknowledges

```bash
Installed 1 extension(s)
```

When you repeat the `joomla show` command, suddenly the result changes:

```bash
$ ./joomla show articles --dump-sql --filter="category=blog"
```
```bash
+----+----------------+----------+----------------+-------------------------+------------------------------+--------+---------+-----------+
| id | title          | category | alias          | teaser                  | body                         | author | license | parent_id |
+----+----------------+----------+----------------+-------------------------+------------------------------+--------+---------+-----------+
| 2  | Second Article | blog     | second-article | Look at another example | This provides child elements | Doctor | CC      | 0         |
+----+----------------+----------+----------------+-------------------------+------------------------------+--------+---------+-----------+
+---+-----------------------------------------------------+----------+
| # | SQL                                                 | Time     |
+---+-----------------------------------------------------+----------+
| 1 | SELECT * FROM articles a                            | 0.814 ms |
|   |   INNER JOIN states_entities b ON a.id=b.entity_id  |          |
|   |   WHERE (a.category = blog)                         |          |
|   |     AND (b.state_id=1)                              |          |
|   |   LIMIT -1 OFFSET 0                                 |          |
|   |                                                     |          |
+---+-----------------------------------------------------+----------+
```

Still, the Article component is not aware of the Workflow component, but the query has been modified to only return published articles (`state_id=1`).

> **Note:** Currently, you can't filter by state. We're working on it!

