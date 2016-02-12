<?php use \Infrastructure\Common;
use \Infrastructure\Constants;
use \Config;
/*
|--------------------------------------------------------------------------
| Register The Laravel Class Loader
|--------------------------------------------------------------------------
|
| In addition to using Composer, you may use the Laravel class loader to
| load your controllers and models. This is useful for keeping all of
| your classes in the "global" namespace without Composer updating.
|
*/

ClassLoader::addDirectories(array(

	app_path().'/commands',
	app_path().'/controllers',
	app_path().'/models',
	app_path().'/database/seeds',

));

/*
|--------------------------------------------------------------------------
| Application Error Logger
|--------------------------------------------------------------------------
|
| Here we will configure the error logger setup for the application which
| is built on top of the wonderful Monolog library. By default we will
| build a basic log file setup which creates a single file for logs.
|
*/

//Log::useFiles(storage_path().'/logs/laravel.log');
$logFile = 'log-'.php_sapi_name().'.txt';

Log::useDailyFiles(storage_path().'/logs/'.$logFile);

/*
|--------------------------------------------------------------------------
| Application Error Handler
|--------------------------------------------------------------------------
|
| Here you may handle any errors that occur in your application, including
| logging them or displaying custom views for specific errors. You may
| even register several error handlers to handle different types of
| exceptions. If nothing is returned, the default error view is
| shown, which includes a detailed stack trace during debug.
|
*/

App::error(function(Exception $exception, $code)
{

    $GeneratedDate = date('D,j M G:i:s T');

    Log::info('Time Of Error:'.$GeneratedDate);
    Log::error($exception);
    $url = Request::url();
    /*$actionName= Request::segment(1);

    $fileName = $exception->getFile();
    $staircaseMessage = $exception->getMessage().' on line '.$exception->getLine();*/


   /* if(App::environment() == 'production'){
        if (Config::get('app.debug')) {
            return;
        }
    }

    if(!Request::ajax())
    {

        switch ($code)
        {
            case 403:

                return Response::view('errors.defaulterror',array('url' => $url,'HeaderMessage'=> Constants::$ForbiddenHeader,'ErrorMessage' =>Constants::$ForbiddenErrorMessage,'ErrorCodeWithMessage'=>Constants::$ForbiddenCodeMsg), $code);

            case 404:
                return Response::view('errors.defaulterror', array('url' => $url,'HeaderMessage'=> Constants::$NotFoundHeader,'ErrorMessage' =>Constants::$NotFoundErrorMessage,'ErrorCodeWithMessage'=>Constants::$NotFoundCodeMsg), $code);

            case 500:
                return Response::view('errors.defaulterror', array('url' => $url,'HeaderMessage'=> Constants::$ServerErrorHeader,'ErrorMessage' =>Constants::$ServerErrorMessage,'ErrorCodeWithMessage'=>Constants::$CommonErrorCodeMsg), $code);

            default:
                return Response::view('errors.defaulterror', array('url' => $url,'HeaderMessage'=> Constants::$DefaultErrorHeader,'ErrorMessage' => $exception->getMessage(),'ErrorCodeWithMessage'=>Constants::$CommonErrorCodeMsg), $code);
        }
    }*/
});


/*
|--------------------------------------------------------------------------
| Maintenance Mode Handler
|--------------------------------------------------------------------------
|
| The "down" Artisan command gives you the ability to put an application
| into maintenance mode. Here, you will define what is displayed back
| to the user if maintenance mode is in effect for the application.
|
*/

App::down(function()
{
	return Response::make("Be right back!", 503);
});

/*
|--------------------------------------------------------------------------
| Require The Filters File
|--------------------------------------------------------------------------
|
| Next we will load the filters file for the application. This gives us
| a nice separate location to store our route and application filter
| definitions instead of putting them all in the main routes file.
|
*/

require app_path().'/filters.php';
