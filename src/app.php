<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Reader\Controller\HomeController;
use Reader\Controller\AboutController;
use Reader\Controller\SubscriptionController;
use Reader\Controller\CategoryController;
use Reader\Controller\CollectController;
use Reader\Controller\SearchController;
use Reader\Controller\UserController;
use Reader\Controller\ListController;
use Reader\Controller\ItemController;
use Reader\Controller\DiscoveryController;
use Reader\Provider\PjaxProvider;
use Reader\Category\TreeTwigExtension;
use Silex\Application;
use Silex\Provider\FormServiceProvider;
use Silex\Provider\MonologServiceProvider;
use Silex\Provider\SessionServiceProvider;
use Silex\Provider\TranslationServiceProvider;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Dflydev\Silex\Provider\DoctrineOrm\DoctrineOrmServiceProvider;
use Silex\Provider\DoctrineServiceProvider;
use Silex\Provider\TwigServiceProvider;
use Silex\Provider\UrlGeneratorServiceProvider;
use Symfony\Component\Translation\Loader\YamlFileLoader;

use Symfony\Component\Config\FileLocator as ConfigLocator;
use Reader\Config\YamlFileLoader as ConfigLoader;

$app = new Application();

// routes
$app->mount('', new HomeController());
$app->mount('', new AboutController());
$app->mount('', new SubscriptionController());
$app->mount('', new CollectController());
$app->mount('', new SearchController());
$app->mount('', new UserController());
$app->mount('', new ListController());
$app->mount('', new ItemController());
$app->mount('', new CategoryController());
$app->mount('', new DiscoveryController());

$app['debug'] = true;

$app['rr.config'] = $app->share(function ($c) {
    $configLoader = new ConfigLoader(new ConfigLocator(array(
        dirname(__DIR__) . '/resources'
    )));
    $config = $configLoader->load('config.yml');

    return $config;
});

$app->register(new SessionServiceProvider());
$app->register(new UrlGeneratorServiceProvider());
//$app->register(new FormServiceProvider());
$app->register(new PjaxProvider());

$app->register(new DoctrineServiceProvider(), array(
    'db.options' => array(
//		'driver' => 'pdo_sqlite',
//		'path'   => __DIR__ . '/sqlite.db'
        'driver' => 'pdo_mysql',
//		'dbname' => $config['db']['dbname'],
//		'host' => $config['db']['host'],
//		'user' => $config['db']['user'],
//		'password' => $config['db']['password'],
        'dbname' => $app['rr.config']['db']['dbname'],
        'host' => $app['rr.config']['db']['host'],
        'user' => $app['rr.config']['db']['user'],
        'password' => $app['rr.config']['db']['password'],
    )
));

$app->register(new DoctrineOrmServiceProvider(), array(
    'orm.em.options' => array(
        'mappings' => array(
            array(
                'type' => 'annotation',
                'namespace' => 'Reader\\Entity',
                'path' => __DIR__ . '/Reader/Entity',
            )
        )
    ),
    'orm.proxies_dir' => __DIR__ . '/../resources/cache/doctrine/proxies',
));

$app->register(new TwigServiceProvider(), array(
    'twig.path' => __DIR__ . '/../resources/template',
    'twig.options' => array('cache' => __DIR__ . '/../resources/cache/twig'),
));

$app['twig'] = $app->share($app->extend('twig', function ($twig, $app) {
    $twig->addGlobal('base_path', $app['request']->getBasePath());
    $twig->addGlobal('index_url', $app['url_generator']->generate('home'));

    $twig->addFilter(new \Twig_SimpleFilter('truncate', function ($string, $size, $append = '...') {
        if (mb_strlen($string) <= $size) {
            return $string;
        } else {
            return array_shift(str_split($string, $size)) . $append;
        }
    }));

    return $twig;
}));

$app->before(function () use ($app) {
    $app['twig']->addExtension(new TreeTwigExtension($app));
});

$app->register(new TranslationServiceProvider());
$app['translator'] = $app->share($app->extend('translator', function ($translator, $app) {
    /* @var \Symfony\Component\Translation\Translator $translator */
    $translator->addLoader('yaml', new YamlFileLoader());

    foreach ($app['rr.config']['locale']['locales'] as $locale) {
        $translator->addResource('yaml', dirname(__DIR__) . sprintf('/resources/locale/%s.yml', $locale), $locale);
    }

    return $translator;
}));

$locale = $app['session']->get('session.locale');

if ($locale) {
    $app['locale'] = $locale;
} else {
    $app['locale'] = $app['rr.config']['locale']['default'];
}

$app['translator']->setLocale($app['locale']);

$app->register(new MonologServiceProvider(), array(
    'monolog.logfile' => __DIR__ . '/../resources/log/app.log',
    'monolog.name' => 'app',
    'monolog.level' => 300
));

$app->error(function (\Exception $e) use ($app) {
    if ($app['debug']) {
        return;
    }

    $code = ($e instanceof HttpException) ? $e->getStatusCode() : 500;

    return new Response($app['twig']->render('error.html.twig', array(
        'message' => $e->getMessage(),
    )), $code);
});

return $app;