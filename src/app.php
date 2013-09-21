<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Reader\Controller\HomeController;
use Reader\Controller\AboutController;
use Reader\Controller\SubscriptionController;
use Reader\Controller\TagController;
use Reader\Controller\CollectController;
use Reader\Controller\SearchController;
use Reader\Controller\UserController;
use Reader\Controller\ListController;
use Reader\Controller\ItemController;
use Reader\Controller\DiscoveryController;
use Reader\Provider\PjaxProvider;
use Reader\Tag\TreeTwigExtension;
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
$app->mount('', new TagController());
$app->mount('', new DiscoveryController());

$app['debug'] = true;

$app->register(new SessionServiceProvider());
$app->register(new UrlGeneratorServiceProvider());
$app->register(new FormServiceProvider());
$app->register(new PjaxProvider());

$app->register(new DoctrineServiceProvider, array(
    'db.options' => array(
//		'driver' => 'pdo_sqlite',
//		'path'   => __DIR__ . '/sqlite.db'
        'driver' => 'pdo_mysql',
//		'dbname' => $config['db']['dbname'],
//		'host' => $config['db']['host'],
//		'user' => $config['db']['user'],
//		'password' => $config['db']['password'],
        'dbname' => 'rssreader',
        'host' => 'localhost',
        'user' => 'root',
        'password' => 'ssc7',
    )
));

$app->register(new DoctrineOrmServiceProvider, array(
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

$app->register(new TranslationServiceProvider(), array(
    'locale_fallback' => 'en_GB'
));
$app['translator'] = $app->share($app->extend('translator', function ($translator, $app) {
    /* @var \Symfony\Component\Translation\Trannslator $translator */
    $translator->addLoader('yaml', new YamlFileLoader());

    $translator->addResource('yaml', __DIR__ . '/../resources/locale/de_DE.yml', 'de_DE');
    $translator->addResource('yaml', __DIR__ . '/../resources/locale/en_GB.yml', 'en_GB');

    return $translator;
}));

$locale = $app['session']->get('session.locale');

if ($locale) {
    $app['locale'] = $locale;
} else {
    $app['locale'] = $app['locale_fallback'];
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
