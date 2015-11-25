
The package contains classes that allow automatically including class sources.


AutoloaderManager is a singleton class that scans a given directory for PHP sources that contain class declarations.

Then, it creates a a list of classes and their source files that is used later when its spl_autoload handler is called.

Because scanning files for class declarations is time consumig operation, AutoloaderManager instance saves the list it created in a config file when the instance is destroyed.


