onPHP Inception
====
**onPHP Inception** is a onPHP sub-framework design for rapid development of CRUD-heavy applications. It's facilities allow
to implement full CRUD functionality for an entity in 5 to 15 minutes.

On the other hand, most of it's functionality is completely optional. So, if you have a complex application with some need for CRUD
and you want to save time writing dull and repetitive code for creating, reading, updating and deleting entities,
this project might prove to be handy to you.

Most of sub-framework's facilities are located in classes under **'classes/Inception/'** directory.  
Testing facilities are located in classes under **'tests/'** directory.  

You should explore those classes to fully grasp all of the **onPHP Inception** functionality.

---

#Main features
- Extremely fast implementation of standard CRUD functionality
- Out of the box [RBAC](http://en.wikipedia.org/wiki/Role-based_access_control) authorization system
- Fully established testing environment
- Many convenient helper classes and methods to ease the pain of writing code for some repetitive operations

#Dependencies
onPHP Inception uses a modified and extended [version of onPHP framework](https://github.com/ShadeSlider/onphp-framework).  
It also relies on [Composer](https://getcomposer.org/) for test related packages.

#Installation
1\. Create a new project directory and move into it:
```bash
$ mkdir onphp_inception
$ cd ./onphp_inception
```

2\. Clone this repository:
```bash
$ git clone https://github.com/ShadeSlider/onPHP-Inception.git .
```

3\.  Move to **'./externals/'** directory and clone **[onPHP Extended](https://github.com/ShadeSlider/onphp-framework.git)** repository.
```bash
$ cd ./externals
$ git clone https://github.com/ShadeSlider/onPHP-Inception.git onphp-extended
```

4\. Move to project root directory and install dependencies.  
For help installing and configuring **Composer** see [this page](https://getcomposer.org/doc/00-intro.md#installation-nix)
```bash
$ cd ../
$ composer.phar install
```

Installation is complete.

#Setting Up

1. Set up web server
2. Set up production PostgreSQL database
3. Set up PostgreSQL database running tests, unless you don't plan to use any tests other than unit tests.
4. Go to **./config** directory and edit **const.dev.inc.php** file. Names of constants are self-explanatory.
5. Go to **./tests** directory and edit **config.tests.inc.php** file.
6. Go to project root directory and run meta builder to make sure everything is in order: 
```bash
$ sh build-meta.sh
```
If everything was set up correctly, meta builder should run without an error.  
Congratulation! You are all done!
