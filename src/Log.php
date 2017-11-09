<?php

namespace zhuravljov\yii\logreader;

use Yii;
use yii\base\BaseObject;
use yii\caching\FileDependency;
use yii\helpers\FileHelper;
use yii\helpers\Inflector;

/**
 * Class Log
 *
 * @property string $name
 * @property string $alias
 * @property null|string $stamp
 * @property string $slug
 * @property string $fileName
 * @property boolean $isExist
 * @property integer|null $size
 * @property integer|null $updatedAt
 * @property string $downloadName
 * @property array $counts
 *
 * @author Roman Zhuravlev <zhuravljov@gmail.com>
 */
class Log extends BaseObject
{
    private $_name;
    private $_alias;
    private $_stamp;
    private $_fileName;

    /**
     * @param string $name
     * @param string $alias
     * @param null|string $stamp
     * @param array $config
     */
    public function __construct($name, $alias, $stamp = null, $config = [])
    {
        $this->_name = $name;
        $this->_alias = $alias;
        $this->_stamp = $stamp;
        $this->_fileName = static::extractFileName($alias, $stamp);
        parent::__construct($config);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->_name;
    }

    /**
     * @return string
     */
    public function getAlias()
    {
        return $this->_alias;
    }

    /**
     * @return string
     */
    public function getStamp()
    {
        return $this->_stamp;
    }

    /**
     * @return string
     */
    public function getSlug()
    {
        return static::extractSlug($this->_name);
    }

    /**
     * @return string
     */
    public function getFileName()
    {
        return $this->_fileName;
    }

    /**
     * @return boolean
     */
    public function getIsExist()
    {
        return file_exists($this->getFileName());
    }

    /**
     * @return integer|null
     */
    public function getSize()
    {
        return $this->getIsExist() ? filesize($this->getFileName()) : null;
    }

    /**
     * @return integer|null
     */
    public function getUpdatedAt()
    {
        return $this->getIsExist() ? filemtime($this->getFileName()) : null;
    }

    /**
     * @return string
     */
    public function getDownloadName()
    {
        return $this->getSlug() . '.log';
    }

    /**
     * @param bool $force
     * @return array
     */
    public function getCounts($force = false)
    {
        if (!$this->getIsExist()) return [];

        $key = $this->getFileName() . '#counts';
        if (!$force && ($counts = Yii::$app->cache->get($key)) !== false) {
            return $counts;
        }

        $counts = [];
        if ($h = fopen($this->getFileName(), 'r')) {
            while (($line = fgets($h)) !== false) {
                if (preg_match('/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $line)) {
                    if (preg_match('/^[\d\-\: ]+\[.*\]\[.*\]\[.*\]\[(.*)\]/U', $line, $m)) {
                        $level = $m[1];
                        if (!isset($counts[$level])) $counts[$level] = 0;
                        $counts[$level]++;
                    }
                }
            }
            fclose($h);
            Yii::$app->cache->set($key, $counts, 0, new FileDependency([
                'fileName' => $this->getFileName(),
            ]));
        }

        return $counts;
    }

    /**
     * @param string $stamp
     * @return boolean
     */
    public function archive($stamp)
    {
        if ($this->getStamp() === null && $this->getIsExist()) {
            rename($this->getFileName(), static::extractFileName($this->getAlias(), $stamp));
            return true;
        } else {
            return false;
        }
    }

    /**
     * @param string $name
     * @return string
     */
    public static function extractSlug($name)
    {
        return Inflector::slug($name);
    }

    /**
     * @param string $alias
     * @param null|string $stamp
     * @return string
     */
    public static function extractFileName($alias, $stamp = null)
    {
        $fileName = FileHelper::normalizePath(Yii::getAlias($alias, false));
        if ($stamp === null) return $fileName;

        $info = pathinfo($fileName);
        return strtr('{dir}/{name}.{stamp}.{ext}', [
            '{dir}' => $info['dirname'],
            '{name}' => $info['filename'],
            '{ext}' => $info['extension'],
            '{stamp}' => $stamp,
        ]);
    }

    /**
     * @param string $alias
     * @param string $fileName
     * @return string|null
     */
    public static function extractFileStamp($alias, $fileName)
    {
        $originName = FileHelper::normalizePath(Yii::getAlias($alias, false));
        $origInfo = pathinfo($originName);
        $fileInfo = pathinfo($fileName);
        if (
            $origInfo['dirname'] === $fileInfo['dirname'] &&
            $origInfo['extension'] === $fileInfo['extension'] &&
            strpos($fileInfo['filename'], $origInfo['filename']) === 0
        ) {
            return substr($fileInfo['filename'], strlen($origInfo['filename']) + 1);
        } else {
            return null;
        }
    }
}