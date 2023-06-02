<?php
/**
* Copyright 2023
* 
* @package    Universal Lite
* @version		0.0.1
*	@access			public
* @author     Henrich Barkoczy <me@barkoczy.social>
* @see 	      https://barkoczy.social
* @see				https://github.com/Barkoczy/universal-lite
* @license    https://universal.barkoczy.social/license
*/
declare(strict_types=1);

namespace App\Kernel;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Illuminate\Database\Capsule\Manager as Capsule;
use DI\Container;
use Slim\Factory\AppFactory;
use Slim\Views\Twig;
use Slim\Views\TwigMiddleware;
use Slim\Middleware\ContentLengthMiddleware;
use App\Kernel\Environment;
use App\Kernel\Filesystem\Folder;
use App\Kernel\Controllers\AppController;
use App\Kernel\Router\Enum\Method;
use App\Kernel\Router\Enum\RouteObject;
use App\Kernel\Router\Router;
use App\Kernel\Extensions\Guard;
use App\Kernel\Extensions\Twig\Csrf as TwigCsrf;
use App\Kernel\Extensions\Twig\BasePath as TwigBasePath;
use App\Kernel\Extensions\Twig\Whitespace as TwigWhitespace;
use App\Kernel\Exceptions\CannotReadFileFromFilesource;
use App\Middlewares\PermissionMiddleware;

final class Bootstrap
{
  private static $_instance = null;
	protected $runtime;
	protected $endtime;
	protected $container;
	protected $app;

	/**
	 * Constructor
	 */
	private function __construct() 
	{
		// @Runtime
		$this->runtime = $this->getmicrotime();

		// @Enviroment
		if (!Environment::hasConfigFile())
			throw new CannotReadFileFromFilesource('Unable to read any of the environment file. Use command compose run create-env-config-file.');
		
		// @Dotenv
		(\Dotenv\Dotenv::createImmutable(Folder::getRootPath()))->load();

		// @Container
		$this->container = new Container();

		// @Database
		$this->database();

		// @Template
		$this->template();

		// @setContainer
		AppFactory::setContainer($this->container);

		/**
		 * Instantiate App
		 *
		 * In order for the factory to work you need to ensure you have installed
		 * a supported PSR-7 implementation of your choice e.g.: Slim PSR-7 and a supported
		 * ServerRequest creator (included with Slim PSR-7)
		 */
		$this->app = AppFactory::create();
		
		// @Middlewares
		$this->middlewares();

		// @route
		$this->route();
	}

	/**
	 * UNIVERSAL LITE
	 *
	 * @return Bootstrap
	 */
	public static function boot(): Bootstrap
	{
		if (self::$_instance === null) {
			self::$_instance = new self;
		}
		return self::$_instance;
	}

	/**
	 * @return Bootstrap
	 */
	public function run(): Bootstrap
	{
		// @run
		$this->app->run();
		
		// @self
		return $this;
	}

	/**
	 * Metrics
	 *
	 * @return void
	 */
	public function metrics(): void
	{
		// @Endtime
		$this->endtime = $this->getmicrotime();

		// @Print
		printf("Load: % .2f ms", ($this->endtime - $this->runtime) * 1000);
	}

	/**
	 * Database
	 *
	 * @return void
	 */
	private function database(): void
	{
		// @client
		$capsule = new Capsule;

		// @settings
		$capsule->addConnection([
			'driver'    => 'mysql',
			'host'      => Environment::var('MYSQL_HOST'),
			'database'  => Environment::var('MYSQL_DATABASE'),
			'username'  => Environment::var('MYSQL_USER'),
			'password'  => Environment::var('MYSQL_PASSWORD'),
			'charset'   => Environment::var('MYSQL_CHARSET'),
			'collation' => Environment::var('MYSQL_COLLATION'),
			'prefix'    => ''
		]);

		// Set the event dispatcher used by Eloquent models... (optional)
		$capsule->setEventDispatcher(new \Illuminate\Events\Dispatcher(
			new \Illuminate\Container\Container
		));

		// Make this Capsule instance available globally via static methods... (optional)
		$capsule->setAsGlobal();

		// Setup the Eloquent ORM... (optional; unless you've used setEventDispatcher())
		$capsule->bootEloquent();

		// Put to container
		$this->container->set('db', function () use ($capsule){
			return $capsule;
		});
	}

	/**
	 * Template
	 *
	 * @return void
	 */
	private function template(): void
	{
		$this->container->set('view', function() {
			// @twig
			$twig = Twig::create([
				'console' => Folder::getConsoleTemplatesPath(),
				'theme'   => Folder::getAppThemesPath().'/'.Environment::var('APP_THEME'),
			], [
				'cache' => ("false" === Environment::var('TWIG_CACHE')) ? false : '../cache/twig'
			]);

			// @ext
			$twig->addExtension(new \voku\twig\MinifyHtmlExtension((new \voku\helper\HtmlMin()), true));
			$twig->addExtension(new TwigBasePath());
			$twig->addExtension(new TwigWhitespace());

			// @return
			return $twig;
		});
	}

	/**
	 * Middlewares
	 *
	 * @return void
	 */
	private function middlewares(): void
	{
		// Response Factory
		$responseFactory = $this->app->getResponseFactory();

		// Register Middleware On Container
		$this->container->set('csrf', function () use ($responseFactory) {
			$guard = new Guard($responseFactory);
			$guard->setPersistentTokenMode(true);
			return $guard;
		});

		// Twig Csrf
		$this->container->get('view')->addExtension(new TwigCsrf($this->container->get('csrf')));

		// Add Twig-View Middleware
		$this->app->add(TwigMiddleware::createFromContainer($this->app));

		// Register Middleware To Be Executed On All Routes
		$this->app->add('csrf');

		// Register Middleware Parser JSON body
		$this->app->addBodyParsingMiddleware();

		/**
		 * The two modes available are
		 * OutputBufferingMiddleware::APPEND (default mode) - Appends to existing response body
		 * OutputBufferingMiddleware::PREPEND - Creates entirely new response body
		 */
		// $mode = OutputBufferingMiddleware::APPEND;
		// $outputBufferingMiddleware = new OutputBufferingMiddleware($mode);

		// ContentLengthMiddleware
		$contentLengthMiddleware = new ContentLengthMiddleware();
		$this->app->add($contentLengthMiddleware);

		// Add Routing Middleware
		$this->app->addRoutingMiddleware();

		/**
		 * Add Error Handling Middleware
		 *
		 * @param bool $displayErrorDetails -> Should be set to false in production
		 * @param bool $logErrors -> Parameter is passed to the default ErrorHandler
		 * @param bool $logErrorDetails -> Display error details in error log
		 * which can be replaced by a callable of your choice.
		 
		* Note: This middleware should be added last. It will not handle any exceptions/errors
		* for middleware added after it.
		*/
		$displayErrorDetails = ("false" === Environment::var('DISPLAY_ERROR_DETAILS')) ? false : true;
  	$logErrors = ("false" === Environment::var('LOG_ERRORS')) ? false : true;
    $logErrorDetails = ("false" === Environment::var('LOG_ERROR_DETAILS')) ? false : true;

		// Error Middleware
		$errorMiddleware = $this->app->addErrorMiddleware(
			$displayErrorDetails, 
			$logErrors, 
			$logErrorDetails
		);

		// Set the Not Found Handler
		$errorMiddleware->setErrorHandler(
			HttpNotFoundException::class,
			function (Request $request, \Throwable $exception, bool $displayErrorDetails) {
				$response = new Response();
				$response->getBody()->write('404 NOT FOUND');

				return $response->withStatus(404);
			});

		// Set the Not Allowed Handler
		$errorMiddleware->setErrorHandler(
			HttpMethodNotAllowedException::class,
			function (Request $request, \Throwable $exception, bool $displayErrorDetails) {
				$response = new Response();
				$response->getBody()->write('405 NOT ALLOWED');

				return $response->withStatus(405);
			});
	}

	/**
	 * Route
	 *
	 * @return void
	 */
	private function route(): void
	{
		// @route
		$route = (new Router())->findByURL();

		// @validate
		if([] === $route)
			return;

		// @controller
		$this->container->set(RouteObject::controller, function() use ($route) {
			return new AppController($route);
		});

		// @doGet
		if(Method::GET === $route[RouteObject::method]){
			$this->doGet($route[RouteObject::url], $route[RouteObject::controller]);
		}

		// @doPost
		if(Method::POST === $route[RouteObject::method]){
			$this->doPost($route[RouteObject::url], $route[RouteObject::controller]);
		}
	}

	/**
	 * doGet
	 *
	 * @param string $url
	 * @param string $component
	 * @return void
	 */
	private function doGet(string $url = '', string $component = ''): void
	{
		$this->app->get($url, $component)->add(PermissionMiddleware::class);
	}

	/**
	 * doPost
	 *
	 * @param string $url
	 * @param string $component
	 * @return void
	 */
	private function doPost(string $url = '', string $component = ''): void
	{
		$this->app->post($url, $component)->add(PermissionMiddleware::class);
	}

	/**
	 * Calculate runtime 
	 *
	 * @return float
	 */
	private function getmicrotime(): float
	{
		list($usec, $sec) = explode(" ",microtime());
		return ((float)$usec + (float)$sec);
	}
}