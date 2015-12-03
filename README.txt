
The package contains classes that allow automate autoloading classes.


AutoloaderManager is a singleton class that scans some given directories for PHP sources that contain class definitions.

Then, it creates a list of classes and their source files that is used later when its spl_autoload handler is called.

Because scanning files for class definitions is a time consumig operation, ClassAutoloader instance saves the list it creates in a config file when its instance is destroyed.

The list is a simple array that has class names as keys and source filenames as values. It is included every time a ClassAutoloader is created.


