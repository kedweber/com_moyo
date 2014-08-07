# com_moyo

## Introduction

Package com_moyo has a number of behaviors and helpers that are common among Moyo's projects. For the sake of speed and
maintainability, it is a complement to the standard nooku / koowa behaviors and a rewrite for common Joomla! helpers.

Please note that several if not all Moyo components are dependent on `com_moyo` and that not installing this component
will most probably trigger nasty errorses.

## Behaviors

### Controller behaviors

#### Cacheable

Handles caching of non-standard elements, e.g. modules etc.

### Database

#### Creatable

Retrieved the creator of a certain element (e.g. Article).

#### Sluggable

Makes sure that slugs are created on the `_beforeTableInsert` . The default behavior for Koowa is `_afterTableInsert`,
which sometimes creates errors on duplicate slugs.

## Helpers

### Behavior

This backend template helper provides a number of form elements: select2, validator, input, control group and multiple
dates.

### Listbox

Generates listboxes, but with a twist. When the options are stored hierarchically in the database, this is represented
by indenting the options by level. Useful for categories, taxonomies, regions and other structured elements.

### Date

A refactor of the date formatter.

### Paginator

A refactor of the Joomla paginator with Bootstrap 3 support.

### Parser

Handles parsing of URLs, e.g. for URL cloaking.

### String

Contains a string truncator.