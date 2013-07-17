Zurb Foundation-based wireframing framework.

The intended workflow comprises two processes:
1. Dev - building wireframes dynamically, using php & sass.
2. Demo - displaying wireframe pages statically (eg., to a client).


--------
Dev process
--------
(TBD)
- create a base page in htmlbits, ie htmlbits/home.php
- build out using include files


--------
Demo process
--------
- Export dynamically constructed html (ie., domain.com/dev/homepage :: "view source") and save as a static file in the root directory (ie., domain.com/homepage.html)




Objectives:
. Re-usable page components
. Simple DX
. Static demo versions
. Legend overlay

Controller . wireframe.php
. Handles requests, serving either dev/dynamic or demo/static version
  o On dev version:
    . Adds overlay (if it exists)
    . Includes
      . /source/components/htmltop.php
      . /source/pages/{wireframe}.php
      . /source
. Create a wireframe with a php file in /source/pages, eg. /source/pages/homepage.php
. View wireframe in browser by prefixing path with /dev/, eg. domain.com/dev/homepage (note no ..php. extension)



Sass structure
. app.scss
. _settings.scss
. _settings_custom.scss       Override foundation default default settings
. _normalize.scss
. _base.scss
. _wireframe.scss     Import app-specific files
. _legend_overlay.scss      Legend overlay
. _wf_custom_breakpoint.scss

-- solution-specific files --
. _wf_functions.scss
. _wf_global_styles.scss
. _wf_layout.scss
. _{component}.scss     Optionally, break out elements for readability/etc.
. _{wireframe}.scss     Optionally, wireframe-specific files, eg. _homepage.scss


App file structure

/wireframe.php  - app controller
/source     All source files
/source/pages     Top-level wireframe files
/source/components    (arbitrary . can use whatever structure makes sense) . global .theme. elements, eg masthead, footer
/source/blocks      (arbitrary . can use whatever structure makes sense) . content pieces
/sass       Compass/sass files.  Would like to come up with an organizational scheme


