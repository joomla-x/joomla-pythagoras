# The Build Environment

The build environment is powered by [Robo](http://robo.li/), a PHP based task runner.
The RoboFile contained in the development version of Joomla! provides a collection of useful commands.

## Check

### Check code style

```bash
$ robo check:style
```

Outputs a full report on the console and generates an XML file for further use
in `build/reports/checkstyle.xml`.

## Document

### Generate API documentation

```bash
$ robo document:api
```

Generates HTML files documenting the API of Joomla! in `build/docs/api`.

### Generate developer documentation

```bash
$ robo document:full
```

Generates HTML files documenting Joomla! for developers in `build/docs/full`.
The documentation not only contains the API, but also protected members, and members marked as `@internal`.

### Generate the Coding Standard documentation

```bash
$ robo document:style
```

Generates a Markdown file documenting the Coding Standard for Joomla! in
`docs/coding-standard.md`.

### Generate all the documentation at once

```bash
$ robo document
```

Generates `api`, `full`, and `style` documentation.


## Fix

```bash
$ robo fix:style
```

Uses PHP CodeSniffer to fix most of the Coding Standard violations.

## Test

### Unit tests

```bash
$ robo test:unit
```

Performs the CodeCeption or PHPUnit tests from the `unit` suite.

> **Note**: The `unit` suite contains not only unit tests, but all tests,
> that can be conducted without external services like database or webserver.

## Miscellaneous

### Converting Markdown to PDF

The current build chain does not include a tool to convert `.md` to `.pdf`.
Instead, an [online service](http://markdown2pdf.com/) can be used for that.
To do the conversion locally, install `pandoc` and use this command:

```bash
$ pandoc -f markdown_github -t latex -o <file>.pdf <file>.md
```
