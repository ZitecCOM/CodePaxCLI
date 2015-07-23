# CodePaxCLI

CodePaxCLI is a command line interface (just for database versioning) of [CodePax@Zitec](https://github.com/ZitecCOM/CodePax).

## Installation

Clone this repository into your project root directory:

	$ git clone https://github.com/IulianCristianGrigoritaZitec/CodePaxCLI.git

Change directory to the repository you've just cloned and install all dependencies using Composer:
	
	$ cd CodePaxCLI
	$ composer install

Now you just need to add your settings into `app/config/config.php`. You can copy and rename `config.sample.php` to `config.php` and change that file.

## Commands

You can see all the commands available, from root directory of CodePaxCLI, by running `app/console`.

### `dbv:info`
Usage:
	$ app/console dbv:info
Shows all the informations about database versioning (versions, updates, change scripts, etc.).

### `dbv:install`
Usage:
	$ app/console dbv:install
Creates the table that holds database versioning data inside the project's database.

### `dbv:newbaseline`
Usage:
	$ app/console dbv:newbaseline
Creates a new baseline from current database state.

### `dbv:run`
Usage:
	$ app/console dbv:run
	or
	$ app/console dbv:run --preserve-test-data
Runs change scripts.
