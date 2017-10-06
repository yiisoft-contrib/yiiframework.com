yiiframework.com Code Documentation
===================================

UI Concepts
-----------

- [Navigation and Headlines](nav.md)
- [Versioned content](versioned-content.md)

- TODO flash messages

- TODO language dependent stylings


Search
------

Search is implemented based on elasticsearch. For searching accross multiple languages, a set of
different indexes is created.

We also differentiate between indices that are rebuilt completely when docs are generated and indexes that
are update on the fly when data changes.

```
language-en
