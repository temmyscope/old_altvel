## About LaraFell (a.k.a Alt-Vel)

=> LaraFell a.k.a Alt-Laravel is developed by Elisha Temiloluwa a.k.a TemmyScope	
=> Inspired By Phalcon & Laravel Php Web Application Frameworks.

LaraFell is a web application framework with priority on security, performance and standard-compliance. 
We believe development must be an enjoyable, flexible and creative experience to be truly fulfilling; 
hence we take the "boring stuffs" out of web appliction development for php developers (code Engineers). 

LaraFell takes the pain out of development by easing common tasks used in many web projects, such as:

- Php Controller Array Structure [Restricts endpoints/pages accessible, making development on a production server a possiblity] 
- [Simple, efficient & fast routing engine].
- Php Array Syntax For Database data retrieval & manipulation
- Supports all PHP PDO supported database types 
- Improved Model class for eloquent data retrieval[method chaining]
- Engineer Console:
	=>Build Controllers from Console 
	[ To see all commands: php Engineer]

	=>Build Model from console
	
	=>Build Api from Console
	
	=>Automatically generate keys for secured session, cookie and salt

- All Form Data are accessible through the Request Model and are pre-sanitized

- Setup your Application's Api without having to bother about security or incoming data

- A Utility HTML Builder [Fast Development for Backend Php developers]


The following conventions are used:

1.	All class file names in the model directory and their references must only start with Capital letters. 
	The only exception with two capital letters is the database (DB.php) class.

2.	All controllers must contain the word “Controller” in their names and must reside in the app\controller folder.

3. 	All view files must reside in a folder within the view folder with respect to the view they display. 
	The only file exempted is the app.blade.php which is the default view file for rendering.

4.	All classes that extend the Model class, should be placed directly under the app folder.

LaraFell is accessible, flexible, editable, powerful, and provides tools required for large, robust applications.

## Learning LaraFell

Larafell has no syntax of its own but uses the inherited php syntax, making it extremely easy for an average php developer to make use of.

## Larafell How-To

-In order to make the larafell framework aware of other frameworks/libraries: 
	
	=>initialise the library/framework Object in the app\model\Extern.php;

	=>Then call it globally.

-By default, api endpoints are accessible from your_site_domain/api/

-The HTML template builder library is used to ease off frontend development but does not eliminate the need for a frontend designer:
	=> Together with the View helper, pagination becomes a stroll in the park:
	 [
	 	sample: view(view, <optional>send data array to page)->paginate(<int> per_page)
	 ]

	=> Several helper methods are available for use.
	
	=> Generates csrf token for each page to improve security against cross-site request forgery
	
	=> Together with the user_navbar array in Config.php, NavBar with dropdowns can be automatically generated
	
	=> Generates CSRF-secured Form using  Array construct passed to generateForm static method

- All Api classes should be placed in the app\api namespace and folder. It can be auto-generated from the Engineer console.
- In order to access data sent to the View renderer, use $dataSource variable; 

## LaraFell Sponsors
If you are interested in becoming a sponsor, please visit the LaraFell [Patreon page](https://patreon.com/temmyscope).

## Contributing

Improve the code as much as possible; keeping syntax-flebility (ability to use plain php),
security, standard-compliance and performance in mind. All Efforts will be appreciated. 
Ensure to add your name to the comment section on the index.php page. 

## Security Vulnerabilities

If you discover a security vulnerability within Larafell, please send an e-mail to TemmyScope via [temmyscope@protonmail.com] All security vulnerabilities will be promptly addressed.

## License

The LaraFell framework is an open-source software.