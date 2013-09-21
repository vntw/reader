<?php

namespace Reader\Util;

use Buzz\Browser;
use Buzz\Client\Curl;
use Buzz\Exception\ClientException;
use Symfony\Component\Filesystem\Filesystem;
use WideImage\WideImage;

class FavIcon
{
    const TYPE_ICON = 'icon';
    const TYPE_SHORTCUT = 'shortcut icon';
    const TYPE_APPLE_TOUCH = 'apple-touch-icon';
    const TYPE_APPLE_TOUCH_PRECOMPOSED = 'apple-touch-icon-precomposed';

    /**
     * @var Filesystem
     */
    private $fs;

    /**
     * @var MessageInterface
     */
    private $response;

    /**
     * @var string
     */
    private $url;

    /**
     * @var array
     */
    private $icons;

    /**
     * Best is on top
     *
     * @var array
     */
    private $types = array(
        self::TYPE_APPLE_TOUCH_PRECOMPOSED,
        self::TYPE_APPLE_TOUCH,
        self::TYPE_SHORTCUT,
        self::TYPE_ICON
    );

    public function __construct($url)
    {
        $this->url = $url;
        $this->icons = array();
        $this->fs = new Filesystem();
    }

    /**
     * @param  string $type
     * @return bool
     */
    private function isValidType($type)
    {
        return in_array($type, $this->types);
    }

    /**
     * @return string
     */
    private function getCacheDir()
    {
        $dir = __DIR__ . '/../../../resources/cache/static/img/subscription-icons';

        if (!is_dir($dir)) {
            $this->fs->mkdir($dir);
        }

        return $dir;
    }

    /**
     * @return string
     */
    private function getGeneratedDir()
    {
        $dir = __DIR__ . '/../../../web/static/img/subscription-icons/gen/';

        if (!is_dir($dir)) {
            $this->fs->mkdir($dir);
        }

        return $dir;
    }

    /**
     * @return array
     */
    public function getIcons()
    {
        return $this->icons;
    }

    /**
     * @param  string $type
     * @return bool
     */
    public function hasType($type)
    {
        return isset($this->icons[$type]);
    }

    /**
     * @return bool
     */
    public function hasIcons()
    {
        return !empty($this->icons);
    }

    /**
     * @param  string $type
     * @return string
     */
    public function getIcon($type)
    {
        return $this->icons[$type];
    }

    /**
     * @return null|string
     */
    public function getBestAvailable()
    {
        foreach ($this->types as $type) {
            if ($this->hasType($type)) {
                return $type;
            }
        }

        return null;
    }

    /**
     * @param $type
     * @return null|string
     * @throws \Exception
     */
    public function save($type)
    {
        $icon = md5($this->url);
        $iconUrl = $this->getIcon($type);

        $browser = new Browser(new Curl());

        try {
            $response = $browser->get($iconUrl)->getContent();
        } catch (\Exception $e) {
            return null;
        }

        $tmpPath = $this->getCacheDir() . '/' . $icon . '.tmp';

        file_put_contents($tmpPath, $response);

        $info = getimagesize($tmpPath);

        if (false === $info) {
            $this->fs->remove($tmpPath);

            return null;
        }

        if (!($ext = $this->getSupportedMime($info['mime']))) {
            return null;
        }

        if ($ext === 'ico') {
            $fi = new FloIcon();
            $fi->readICO($tmpPath);

            ob_start();
            imagepng($fi->getImage(0));
            $cont = ob_get_contents();
            ob_end_clean();

            file_put_contents($tmpPath, $cont);
            $ext = 'png';
        }

        $path = $this->getGeneratedDir() . $icon . '.' . $ext;

        $wideImage = WideImage::load($tmpPath);
        $resized = $wideImage->resize(16, 16);
        $resized->saveToFile($path);

        $this->fs->remove($tmpPath);

        return $icon . '.' . $ext;
    }

    /**
     * @param $mime
     * @return null
     */
    private function getSupportedMime($mime)
    {
        switch (strtolower($mime)) {
            case 'image/png':
                $ext = 'png';
                break;
            case 'image/gif':
                $ext = 'gif';
                break;
            case 'image/jpeg':
                $ext = 'jpg';
                break;
            case 'image/vnd.microsoft.icon':
                $ext = 'ico';
                break;
            default:
                $ext = null;
        }

        return $ext;
    }

    /**
     * @return FavIcon
     * @throws \InvalidArgumentException
     * @throws \Exception
     */
    public function fetch()
    {
        $urlParts = parse_url($this->url);

        if (!in_array($urlParts['scheme'], array('http', 'https'))) {
            throw new \InvalidArgumentException(sprintf('Invalid URL: %s', $this->url));
        }

        $c = new Curl();
        $c->setTimeout(10);
        $browser = new Browser($c);

        try {
            $this->response = $browser->get($this->url);
        } catch (ClientException $e) {
            return null;
        }

        $response = $this->response->getContent();
        $favicons = array();

        preg_match_all('/<link (.+)\/?>/Ui', $response, $links);

        foreach ($links[1] as $link) {

            // regexp taken from https://github.com/neogeek/Favicache
            if (preg_match('/rel=(?:"|\')(.*icon.*)(?:"|\')/Ui', $link, $typeMatch)
                && preg_match('/href=(?:"|\')(.+)(?:"|\')/Ui', $link, $linkMatch)
            ) {
                $favicon = $linkMatch[1];

                if (strpos($favicon, 'http') !== 0) {
                    $favicon = $urlParts['scheme'] . '://' . $urlParts['host'] . '/' . ltrim($favicon, '/');
                }

                if ($this->isValidType($typeMatch[1])) {
                    $favicons[$typeMatch[1]] = $favicon;
                }
            }
        }

        $this->icons = $favicons;

        return $this;
    }

}
