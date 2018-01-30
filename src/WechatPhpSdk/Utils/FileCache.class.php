<?php
/**
 * FileCache 文件缓存
 *
 * @author      18y
 * @link       https://github.com/18y/wechat-php-sdk
 */

namespace Gaoming13\WechatPhpSdk\Utils;

class FileCache {

    protected $options = [
        'expire'        => 7000,
        'path'          => './runtime/',
    ];

    /**
     * 构造函数
     * @param array $options
     */
    public function __construct($options = [])
    {
        if (!empty($options)) {
            $this->options = array_merge($this->options, $options);
        }
        if (substr($this->options['path'], -1) != '/') {
            $this->options['path'] .= '/';
        }
        $this->init();
    }
    /**
     * 初始化检查
     * @access private
     * @return boolean
     */
    private function init()
    {
        // 创建项目缓存目录
        if (!is_dir($this->options['path'])) {
            if (mkdir($this->options['path'], 0755, true)) {
                return true;
            }
        }
        return false;
    }

    /**
     * 获取缓存
     * @dateTime 2018-01-30T15:48:50+0800
     * @param    string                   $name 缓存名称    
     * @param    boolean                  $default 默认返回值
     * @return array
     */
    public function get($name, $default = false)
	{
        $filename = $this->getCacheKey($name);
        if (!is_file($filename)) {
            return $default;
        }
        $content = file_get_contents($filename);
        if (false !== $content) {
        	$arr = json_decode($content,true);
        	if($arr['expire'] <= time())
        	{
        		return false;
        	}
        	return $content;
        }
	}

    /**
     * 设置缓存
     * @dateTime 2018-01-30T15:51:07+0800
     * @param    string                   $name   缓存名称
     * @param    string                   $value  缓存值
     * @return bool
     */
    public function set($name, $value, $expire = null)
	{
        if (is_null($expire)) {
            $expire = $this->options['expire'];
        }
        $filename = $this->getCacheKey($name);
        $json = json_encode(array($name=>$value,"expire"=>time()+$expire));
        $result = file_put_contents($filename,$json);
        if ($result) {
        	return true;
        }
        return false;
	}

	/**
	 * 获取缓存文件名
	 * @dateTime 2018-01-29T14:32:49+0800
	 * @return   [type]                   [description]
	 */
	public function getCacheKey($name)
	{
		return $this->options['path']."/".$name.'_cache.php';
	}
}