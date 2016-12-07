# The Build Environment

The build environment is powered by [Robo](http://robo.li/), a PHP based task runner.
The RoboFile contained in the development version of Joomla! provides a collection of useful commands.

## Check

### Code Style

Check the code style according to the [Joomla! Code Style Guide](https://joomla.github.io/coding-standards/) using PHP CodeSniffer.

```bash
$ robo check:style
```

Outputs a full report on the console and generates an XML file for further use
in `build/reports/checkstyle.xml`.

See [PHP CodeSniffer documentation](http://pear.php.net/package/PHP_CodeSniffer/docs/latest/) for further information.

### Copy&Paste Detection

Detect PHP code duplication using `phpcpd`.

```bash
$ robo check:cpd
```

Outputs a report on the console and generates an XML file for further use
in `build/reports/pmd-cpd.xml`.

See [`phpcpd` documentation](https://github.com/sebastianbergmann/phpcpd/blob/master/README.md) for further information.

### Size and Structure

Quickly measure the size of a PHP project using `phploc`.

```bash
$ robo check:loc
```

Outputs a report on the console and generates an XML file for further use
in `build/reports/phploc.xml`.

See [`phploc` documentation](https://github.com/sebastianbergmann/phploc/blob/master/README.md) for further information.

### Dependancies

Analyse a PHP project using `pdepend`, an adaption of the established Java development tool JDepend. This tool shows you the quality of your design in the terms of extensibility, reusability and maintainability.

```bash
$ robo check:depend
```

Outputs a report on the console and generates XML files for further use
in `build/reports/dependency.xml`, `build/reports/jdepend.xml`, and `build/reports/summary.xml`. Additionally, two illustrations are generated, `build/reports/pyramid.svg` and `build/reports/jdepend.svg`.

If present, the code coverage `build/reports/coverage.xml` will be taken into account for metrics calculation.

See [PHP_Depend documentation](https://pdepend.org/documentation/getting-started.html) for further information.

## Document

### API Documentation

```bash
$ robo document:api
```

Generates HTML files documenting the API of Joomla! in `build/docs/api`.

### Developer Documentation

```bash
$ robo document:full
```

Generates HTML files documenting Joomla! for developers in `build/docs/full`.
The documentation not only contains the API, but also protected members, and members marked as `@internal`.

### Coding Standard Documentation

```bash
$ robo document:style
```

Generates a Markdown file documenting the Coding Standard for Joomla! in
`docs/coding-standard.md`.

### All Documentation At Once

```bash
$ robo document
```

Generates `api`, `full`, and `style` documentation.

## Fix

```bash
$ robo fix:style
```

Uses PHP CodeSniffer to fix most of the Coding Standard violations.

## Report

### Code Browser

```bash
$ robo report:cb
```

Creates a code listing with syntax highlighting and colored error-sections found by QA tools in `build/reports/code/`.

See [PHP_CodeBrowser documentation](https://github.com/mayflower/PHP_CodeBrowser/blob/master/README.markdown) for further information.

### Metrics

```bash
$ robo report:metrics
```

Provides a large set of software metrics from a given code base. 
Generates readable and accessible reports about maintainability, quality and complexity in `build/reports/phpmetrics.html`.

See [PhpMetrics website](http://www.phpmetrics.org) for further information.

## Test

### Unit Tests

Run the CodeCeption or PHPUnit tests from the `unit` suite. 
These tests have no requirements other than an installed CodeCeption.

```bash
$ robo test:unit [--coverage]
```

> **Note**: The `unit` suite contains not only unit tests, but all tests,
> that can be conducted without external services like database or webserver.

If the `--coverage` option is provided, a PHP coverage dump is created in `build/reports/coverage.unit.php`, which is used internally for creation of a merged test coverage report.

### System Tests for the Command Line

Run the CodeCeption or PHPUnit tests from the `cli` suite.

```bash
$ robo test:cli [--coverage]
```

If the `--coverage` option is provided, a PHP coverage dump is created in `build/reports/coverage.cli.php`, which is used internally for creation of a merged test coverage report.

The `cli` tests are run in a docker container, so be sure to have `docker` installed and the docker demon running. The container gets built and started automatically.

### All Test Suites At Once

Run the CodeCeption or PHPUnit tests from all suites.

```bash
$ robo test [--coverage]
```

Performs the CodeCeption or PHPUnit tests from `unit` and `cli` suites.

If the `--coverage` option is provided, a combined coverage report is created in `build/reports/coverage`. Additionally, a standard XML report is written to `build/reports/junit.xml` for use in other tools, like PHP CodeBrowser (see `report:cb`).

## Miscellaneous

### Converting Markdown to PDF

The current build chain does not include a tool to convert `.md` to `.pdf`.
Instead, an [online service](http://markdown2pdf.com/) can be used for that.
To do the conversion locally, install `pandoc` and use this command:

```bash
$ pandoc -f markdown_github -t latex -o <file>.pdf <file>.md
```
