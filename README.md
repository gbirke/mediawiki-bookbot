# MediaWiki Bookbot
This MediaWiki Bot generates a print page from a table of contents with links to other pages.

## Book Structure in MediaWiki
This plugin assumes a certain structure of the Books in MediaWiki: All chapters 
of a book must be [Subpages][1] of a page. This page is called the "main page" 
of the book. The table of contents may or may not be on the main page.

## Installation
If you want to create books in the main [Namespace][2] of your wiki, you must 
[enable subpages in the main namespace][3].



## TODO
- Make print version name configurable
- Make dependency on FlaggedRevs extension optional
- Configuration option to use newest or stable version when FlaggedRevs is installed
- Better strategy to determine book name
- Describe installation and usage in README. Add passage in installation instructions for using FlaggedRevs.
 

## Possible future improvements
- Fix headline indentation on print page to match the indentation in the table of contents.
- keep the table of contents in sync with the pages it's linking to.

[1]: https://www.mediawiki.org/wiki/Help:Subpages
[2]: https://www.mediawiki.org/wiki/Help:Namespaces
[3]: https://www.mediawiki.org/wiki/Manual:$wgNamespacesWithSubpages#Enabling-for-a-namespace
