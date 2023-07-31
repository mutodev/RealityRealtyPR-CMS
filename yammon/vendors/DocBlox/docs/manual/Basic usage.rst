Basic usage
===========

You can use the ``docblox`` command to generate your documentation
for you.

In this document is shown how DocBlox can be used to generate your
documentation. It is expected that you have installed DocBlox using
PEAR; thus whenever we ask you to run a command it would be in the
following form:

::

    $ docblox

When you have installed a version directly from Github your should
invoke the ``docblox.php`` script in the ``bin`` folder of your
DocBlox installation unless you have added a symlink as described in the chapter
:doc:`Installation`.

Under Linux / MacOSX that would be:

::

    $ [DOCBLOX_FOLDER]/bin/docblox.php

And under Windows that would be:

::

    $ [DOCBLOX_FOLDER]\bin\docblox

Introduction
------------

DocBlox takes a two-step approach to generating documentation:


1. Parse the source files and create an intermediate structure file(called
   structure.xml) containing all meta-data.
2. Transform the intermediate structure file to a form of human readable output,
   such as HTML.

These steps can be executed at once or separate, depending upon
your preference.

Generating documentation
------------------------

To generate your documentation you can use the ``run`` task:

::

    $ docblox run

When ran without parameters (as shown above) it will try to get the
location of the source code and the target folder from a
configuration file (which is discussed in the :doc:`Configuration` chapter) or
exit with an error. You can use the help option (``-h`` or ``--help``) to view
a list of all possible actions.

::

    $ docblox run -h

The simplest action would be to invoke docblox to parse the given
location (``-d`` for a directory, ``-f`` for a file) and tell it to
output your documentation to the given target (``-t``) folder using
the following command:

::

    $ docblox run -d [SOURCE_PATH] -t [TARGET_PATH]

Please be aware that DocBlox expects the target location to exist
and that it is writable. If it is not, the application will exit
and tell you so.


