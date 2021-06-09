<?php

/**
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; under version 2
 * of the License (non-upgradable).
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *
 * Copyright (c) 2002-2008 (original work) Public Research Centre Henri Tudor & University of Luxembourg (under the project TAO & TAO2);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 *               2013-2020 (update and modification) Open Assessment Technologies SA;
 *
 */

namespace oat\tao\model\mvc;

use common_exception_PreConditionFailure as PreConditionException;
use common_exception_SystemUnderMaintenance as MaintenanceException;
use common_ext_ExtensionsManager;
use common_Logger;
use common_report_Report as Report;
use common_session_SessionManager as SessionManager;
use Exception;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\ServerRequest;
use helpers_Report as ReportHelper;
use oat\oatbox\event\EventAggregator;
use oat\oatbox\service\ServiceConfigDriver;
use oat\oatbox\service\ServiceManager;
use oat\oatbox\service\ServiceManagerAwareInterface;
use oat\oatbox\service\ServiceManagerAwareTrait;
use oat\tao\helpers\Template;
use oat\tao\model\asset\AssetService;
use oat\tao\model\http\RequestRebuilder;
use oat\tao\model\maintenance\Maintenance;
use oat\tao\model\mvc\error\ExceptionInterpreterService;
use oat\tao\model\routing\CliController;
use oat\tao\model\routing\TaoFrontController;
use oat\tao\model\session\Business\Contract\SessionCookieServiceInterface;
use Psr\Http\Message\RequestInterface;
use Request;
use tao_helpers_Context;
use tao_helpers_Request;
use tao_helpers_Scriptloader as ScriptLoader;

/**
 * The Bootstrap Class enables you to drive the application flow for a given extenstion.
 * A bootstrap instance initialize the context and starts all the services:
 *  - session
 *  - database
 *  - user
 *
 * And it's used to disptach the Control Loop
 *  - control the platform status (redirect to the maintenance page if it is required)
 *  - dispatch to the convenient action
 *  - control code exceptions
 *
 * @author Bertrand CHEVRIER <bertrand.chevrier@tudor.lu>
 * @package tao
 * @example
 * <code>
 *  $bootStrap = new BootStrap('tao');  //create the Bootstrap instance
 *  $bootStrap->start();                //start all the services
 *  $bootStrap->dispatch();             //dispatch the http request into the control loop
 * </code>
 */
class Bootstrap implements ServiceManagerAwareInterface
{
    use ServiceManagerAwareTrait;

    const CONFIG_SESSION_HANDLER = 'session';

    /**
     * @var boolean if the context has been started
     */
    protected static $isStarted = false;

    /**
     * @var boolean if the context has been dispatched
     */
    protected static $isDispatched = false;

    /**
     * Bootstrap constructor.
     *
     * Initialize the context
     *
     * @param $configuration
     * @throws \common_Exception If config file is not readable
     */
    public function __construct($configuration)
    {
        new DotEnvReader();

        if (! is_string($configuration) || ! is_readable($configuration)) {
            throw new PreConditionException('TAO platform seems to be not installed.');
        }

        require_once $configuration;
        $serviceManager = new ServiceManager(
            (new ServiceConfigDriver())->connect('config', [
                'dir' => dirname($configuration),
                'humanReadable' => true
            ])
        );

        $this->setServiceLocator($serviceManager);
        // To be removed when getServiceManager will disappear
        ServiceManager::setServiceManager($serviceManager);
        if (PHP_SAPI === 'cli') {
            tao_helpers_Context::load('SCRIPT_MODE');
        } else {
            tao_helpers_Context::load('APP_MODE');
        }
    }

    /**
     * Check if the current context has been started
     * @return boolean
     */
    public static function isStarted()
    {
        return self::$isStarted;
    }

    /**
     * Check if the current context has been dispatched
     * @return boolean
     */
    public static function isDispatched()
    {
        return self::$isDispatched;
    }

    /**
     * Check if the platform is ready
     *
     * @return boolean Return true if the application is ready
     */
    protected function isReady()
    {
        return $this->getMaintenanceService()->isPlatformReady();
    }

    /**
     * Start all the services:
     *  1. Start the session
     *  2. Update the include path
     *  3. Include the global helpers
     *  4. Connect the current user to the generis API
     *  5. Initialize the internationalization
     *  6. Check the application' state
     */
    public function start()
    {
        if (!self::$isStarted) {
            $this->session();
            $this->setDefaultTimezone();
            $this->registerErrorhandler();
            self::$isStarted = true;
        }
    }

    protected function dispatchHttp()
    {
        $isAjax = tao_helpers_Request::isAjax();

        if (tao_helpers_Context::check('APP_MODE')) {
            if (!$isAjax) {
                $this->scripts();
            }
        }

        //Catch all exceptions
        try {
            //the app is ready, process mvc
            if ($this->isReady()) {
                $this->mvc();
            }
            //the app is not ready, put platform on maintenance
            else {
                $this->displayMaintenancePage();
            }
        } catch (Exception $e) {
            $this->catchError($e);
        }

        // explicitly close session
        session_write_close();
    }

    /**
     * Put the platform on maintenance
     * Redirect to maintenance page if http call is not ajax
     * Otherwise throw common_exception_SystemUnderMaintenance
     *
     * @throws MaintenanceException
     */
    protected function displayMaintenancePage()
    {
        //the request is not an ajax request, redirect the user to the maintenance page
        if (! tao_helpers_Request::isAjax()) {
            require_once Template::getTemplate('error/maintenance.tpl', 'tao');
            //else throw an exception, this exception will be send to the client properly
        } else {
            throw new MaintenanceException();
        }
    }


    protected function dispatchCli()
    {
        $params = $_SERVER['argv'];
        array_shift($params);

        if (count($params) < 1) {
            $report = new Report(Report::TYPE_ERROR, __('No action specified'));
        } else {
            $actionIdentifier = array_shift($params);
            $cliController = new CliController();
            $this->propagate($cliController);
            $report = $cliController->runAction($actionIdentifier, $params);
        }

        echo ReportHelper::renderToCommandline($report);
    }

    /**
     * Dispatch the current http request into the control loop:
     *  1. Load the ressources
     *  2. Start the MVC Loop from the ClearFW
     *  manage Exception:
     */
    public function dispatch()
    {
        if (!self::$isDispatched) {
            if (PHP_SAPI === 'cli') {
                $this->dispatchCli();
            } else {
                $this->dispatchHttp();
            }

            $this->getServiceManager()->get(EventAggregator::SERVICE_ID)->triggerAggregatedEvents();

            self::$isDispatched = true;
        }
    }

    /**
     * Catch any errors
     * return a http response in function of client accepted mime type
     *
     * @param Exception $exception
     */
    protected function catchError(Exception $exception)
    {
        $exceptionInterpreterService = $this->getServiceLocator()->get(ExceptionInterpreterService::SERVICE_ID);
        $interpretor = $exceptionInterpreterService->getExceptionInterpreter($exception);
        $interpretor->getResponse()->send();
    }

    /**
     * Start the session
     */
    protected function session()
    {
        if (PHP_SAPI === 'cli') {
            return;
        }

        if (tao_helpers_Context::check('APP_MODE')) {
            // Set a specific ID to the session.
            $request = new Request();
            if ($request->hasParameter('session_id')) {
                session_id($request->getParameter('session_id'));
            }
        }

        // set the session cookie to HTTP only.

        $this->configureSessionHandler();

        $this->getSessionCookieService()->initializeSessionCookie();
    }

    private function configureSessionHandler()
    {
        $sessionHandler = common_ext_ExtensionsManager::singleton()->getExtensionById('tao')->getConfig(self::CONFIG_SESSION_HANDLER);
        if ($sessionHandler !== false) {
            session_set_save_handler(
                [$sessionHandler, 'open'],
                [$sessionHandler, 'close'],
                [$sessionHandler, 'read'],
                [$sessionHandler, 'write'],
                [$sessionHandler, 'destroy'],
                [$sessionHandler, 'gc']
            );
        }
    }

    /**
     * register a custom Errorhandler
     */
    protected function registerErrorhandler()
    {
        // register the logger as erorhandler
        common_Logger::singleton()->register();
    }

    /**
     * Set Timezone quickfix
     */
    protected function setDefaultTimezone()
    {
        if (function_exists("date_default_timezone_set") && defined('TIME_ZONE')) {
            date_default_timezone_set(TIME_ZONE);
        }
    }

    /**
     *  Start the MVC Loop from the ClearFW
     *  @throws \ActionEnforcingException in case of wrong module or action
     *  @throws \tao_models_classes_UserException when a request try to acces a protected area
     */
    protected function mvc()
    {
        $request = $this->buildRequest();
        $response = new Response();
        $frontController = $this->propagate(new TaoFrontController());
        $frontController($request, $response);
    }

    /**
     * Load external resources for the current context
     * @see \tao_helpers_Scriptloader
     */
    protected function scripts()
    {
        $assetService = $this->getServiceLocator()->get(AssetService::SERVICE_ID);
        $cssFiles = [
            $assetService->getAsset('css/layout.css', 'tao'),
            $assetService->getAsset('css/tao-main-style.css', 'tao'),
            $assetService->getAsset('css/tao-3.css', 'tao')
        ];

        //stylesheets to load
        ScriptLoader::addCssFiles($cssFiles);

        if (SessionManager::isAnonymous()) {
            ScriptLoader::addCssFile(
                $assetService->getAsset('css/portal.css', 'tao')
            );
        }
    }

    protected function getMaintenanceService(): Maintenance
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return $this->getServiceLocator()->get(Maintenance::SERVICE_ID);
    }

    private function getSessionCookieService(): SessionCookieServiceInterface
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return $this->getServiceLocator()->get(SessionCookieServiceInterface::class);
    }

    private function buildRequest(): RequestInterface
    {
        $request = ServerRequest::fromGlobals();
        return (new RequestRebuilder())->rebuild($request);
    }
}
