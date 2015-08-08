<?php

namespace Concrete\Core\Cache\Page;
use Concrete\Core\Http\Response;
use Config;
use Request;
use \Page as ConcretePage;
use \Concrete\Core\Page\View\PageView;
use Permissions;
use User;
use Session;

abstract class PageCache {

	static $library;

	public function deliver(PageCacheRecord $record) {
		$response = new Response();
        $headers = array();
        if (defined('APP_CHARSET')) {
            $headers["Content-Type"] = "text/html; charset=" . APP_CHARSET;
		}
        $headers = array_merge($headers, $record->getCacheRecordHeaders());

        $response->headers->add($headers);
        $response->setContent($record->getCacheRecordContent());
        return $response;
	}

	public static function getLibrary() {
		if (!PageCache::$library) {
			$adapter = Config::get('concrete.cache.page.adapter');
			$class = overrideable_core_class(
				'Core\\Cache\\Page\\' . camelcase($adapter) . 'PageCache',
				DIRNAME_CLASSES . '/Cache/Page/' . camelcase($adapter) . 'PageCache.php'
			);
			PageCache::$library = new $class();
		}
		return PageCache::$library;
	}

	/**
	 * Note: can't use the User object directly because it might query the database
	 */
	public function shouldCheckCache(Request $req) {
		if (Session::get('uID') > 0) {
			return false;
		}
		return true;
	}

	public function outputCacheHeaders(ConcretePage $c) {
		foreach ($this->getCacheHeaders($c) as $header) {
			header($header);
		}
	}

	public function getCacheHeaders(ConcretePage $c) {
		$lifetime = $c->getCollectionFullPageCachingLifetimeValue();
		$expires  = gmdate('D, d M Y H:i:s', time() + $lifetime) . ' GMT';

		$headers  = array(
			'Pragma' => 'public',
			'Cache-Control' => 'max-age=' . $lifetime . ',s-maxage=' . $lifetime,
			'Expires' => $expires
		);

		return $headers;
	}

	public function shouldAddToCache(PageView $v) {
		$c = $v->getCollectionObject();
		if (!is_object($c)) {
			return false;
		}

		$cp = new Permissions($c);
		if (!$cp->canViewPage()) {
			return false;
		}

		$u = new User();

		$allowedControllerActions = array('view');
		if (is_object($v->controller)) {
			if (!in_array($v->controller->getTask(), $allowedControllerActions)) {
				return false;
			}
		}

		if (!$c->getCollectionFullPageCaching()) {
			return false;
		}

		if ($u->isRegistered() || $_SERVER['REQUEST_METHOD'] == 'POST') {
			return false;
		}

		if ($c->isGeneratedCollection()) {
			if ((is_object($v->controller) && (!$v->controller->supportsPageCache())) || (!is_object($v->controller))) {
				return false;
			}
		}

		if ($c->getCollectionFullPageCaching() == 1 || Config::get('concrete.cache.pages') === 'all') {
			// this cache page at the page level
			// this overrides any global settings
			return true;
		}

		if (Config::get('concrete.cache.pages') !== 'blocks') {
			// we are NOT specifically caching this page, and we don't
			return false;
		}

		$blocks = $c->getBlocks();
		$blocks = array_merge($c->getGlobalBlocks(), $blocks);

		foreach($blocks as $b) {
			if (!$b->cacheBlockOutput()) {
				return false;
			}
		}
		return true;
	}

	public function getCacheKey($mixed) {
		if ($mixed instanceof ConcretePage) {
			if ($mixed->getCollectionPath() != '') {
				return urlencode(trim($mixed->getCollectionPath(), '/'));
			} else if ($mixed->getCollectionID() == HOME_CID) {
				return '!' . HOME_CID;
			}
		} else if ($mixed instanceof \Concrete\Core\Http\Request) {
			if ($mixed->getPath() != '') {
				return urlencode(trim($mixed->getPath(), '/'));
			} else {
				return '!' . HOME_CID;
			}
		} else if ($mixed instanceof PageCacheRecord) {
			return $mixed->getCacheRecordKey();
		}
	}

	public function getVaryOnCacheKey($varySettings) {

        $varyOnKey = array();
        $req = \Request::getInstance();

        foreach($varySettings as $field => $settings) {

            if($req->query->has($field)) {

                if($settings['match']) {
                    if(preg_match($settings['match'],$req->query->get($field),$out)) { 
                        $varyOnKey[$field] = $out[0];
                    } else {
                        $varyOnKey = false;
                        break;
                    }
                } else {
                    $varyOnKey[$field] = $req->query->get($field);
                }

            } else if($settings['default']) {
                $varyOnKey[$field] = $settings['default'];
            }
        }

        if($varyOnKey !== false) {
            $varyOnKey = serialize($varyOnKey);
        } else {
            $varyOnKey = false;
        }

        return $varyOnKey;
    }

    public function getVaryOnCacheSettings($c) {

        $fullPageVaryOn = array();

        $blocks = $c->getBlocks();
        array_merge($c->getGlobalBlocks(), $blocks);

        foreach($blocks as $b) {
            foreach($b->cacheBlockOutputVaryOn() as $key => $val) {
                $fullPageVaryOn[$key] = $val; //TODO: Possible bug with collisions?
            }
        }

        return $fullPageVaryOn;
    }

	abstract public function getRecord($mixed);
	abstract protected function getVaryOnRecord($cacheKey);
	abstract public function set(ConcretePage $c, $content);
	abstract public function purgeByRecord(\Concrete\Core\Cache\Page\PageCacheRecord $rec);
	abstract public function purge(ConcretePage $c);
	abstract public function flush();

}
