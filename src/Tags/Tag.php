<?php

namespace pinpie\pinpie\Tags;

use \pinpie\pinpie;
use \pinpie\pinpie\PP as PP;

class Tag {
	public
		$action = '',
		$cachebleParents = false,
		$cachetime = 0,
		$childIndex = 0,
		$children = [],
		$content = '',
		$depth = 0,
		$errors = [],
		$files = [],
		$filename = '',
		$filetime = 0,
		$fullname = '',
		$fulltag = '',
		$hash = false,
		$hashBase = [],
		$index = 0,
		$name = '',
		$output = '',
		$params = [],
		/** @var Tag|null */
		$parent = null,
		$parents = [],
		$placeholder = '',
		$priority = 0,
		$settings = [],
		$tagClassName = '',
		$tagpath = '/',
		$template = false,
		$templateParams = [],
		$templateFilename = '',
		$time = ['start' => 0, 'end' => 0, 'total' => 0, 'processing' => 0],
		$type = '',
		$value = '',
		$vars = [],
		$varsLocal = [];

	/** @var PP|null */
	protected $pinpie = null;


	public function __construct(PP $pinpie, $settings, $fulltag, $type, $placeholder, $template, $cachetime, $fullname, Tag $parentTag = null, $priority = 10000, $depth = 0) {
		$this->time = [
			'start' => microtime(true),
			'end' => (float)0,
			'total' => (float)0,
			'processing' => (float)0
		];
		$this->pinpie = $pinpie;
		$this->settings = $settings;
		$this->tagClassName = __CLASS__;
		$this->fulltag = $fulltag;
		$this->depth = $depth;
		$this->type = $type;
		$this->placeholder = $placeholder;
		$template = explode('?', $template, 2);
		// extracting params [[tag?params]]
		if (isset($template[1])) {
			parse_str($template[1], $this->templateParams);
		}
		$this->template = $template[0];
		$this->priority = $priority;
		$this->parent = $parentTag;
		if ($this->parent) {
			/* set parent, become a child, count self index */
			$this->childIndex = count($this->parent->children);
			$this->parent->children[] = $this;
			$this->parents = $this->parent->parents;
			$this->parents[] = $this->parent;
			/* if parent is cacheble or its parents are cacheble - no need to cache this tag */
			if ($this->parent->cachebleParents OR $this->parent->cachetime) {
				$this->cachebleParents = true;
			}
		}
		$this->cachetime = $cachetime;
		$value = null;
		$this->fullname = $fullname;
		$name = explode('?', $fullname, 2);
		// extracting params [[tag?params=foobar]]
		if (isset($name[1])) {
			parse_str($name[1], $this->params);
		}
		$name = $name[0];
		//extracting direct value [[tag=value]]
		$name = explode('=', $name, 2);
		if (isset($name[1])) {
			$value = $name[1];
		}
		$name = $name[0];
		$this->name = $name;
		$this->value = $value;
		$path = [];
		foreach ($this->parents as $parent) {
			$path[] = $parent->type . $parent->name;
		}
		$path[] = $this->type . $this->name;
		$this->tagpath = implode('/', $path);
	}


	public function getOutput() {
		$this->error('Unknown tag found. tag:' . $this->fulltag . ' in ' . $this->tagpath);
		return '';
	}

	protected function varsInject(&$vars) {
		$this->vars = $vars;
		$this->varsInjectMerge($vars, $this->pinpie->vars);
		foreach ($this->parents as $parent) {
			$this->varsInjectMerge($vars, $parent->vars);
		}
	}

	protected function varsInjectMerge(&$vars, &$storage) {
		foreach ($vars as $varname => $var) {
			foreach ($var as $priority => $value) {
				if (!isset($storage[$varname][$priority])) {
					$storage[$varname][$priority] = [];
				}
				$storage[$varname][$priority] = array_merge($storage[$varname][$priority], $value);
			}
		}
	}

	protected function varPut() {
		$this->pinpie->vars[$this->placeholder][$this->priority][] = $this->output;
		$this->vars[$this->placeholder][$this->priority][] = $this->output;
		foreach ($this->parents as $parent) {
			$parent->vars[$this->placeholder][$this->priority][] = $this->output;
		}
		$this->output = '';
	}

	protected function render() {
		$this->action = 'processed nocache';
		$this->content = $this->getContent();
		if ($this->template === false) {
			// skipping parsing
		} else {
			$this->content = $this->pinpie->parseString($this->content, $this);
		}
		//Apply template to tag content
		if (!empty($this->template)) {
			$this->output = $this->applyTemplate();
		} else {
			$this->output = $this->content;
		}
		if ($this->placeholder) {
			$this->varPut();
		}
		return $this->output;
	}

	protected function applyTemplate() {
		$output = $this->content;
		if (!empty($this->template)) {
			if ($this->pinpie->conf->pinpie['templates function']) {
				$this->pinpie->times[] = [microtime(true), 'Tag #' . $this->index . ' calling external templates function'];
				$output = $this->pinpie->conf->pinpie['templates function']($this);
				$this->pinpie->times[] = [microtime(true), 'Tag #' . $this->index . ' finished external templates function'];
			} else {
				$this->pinpie->times[] = [microtime(true), 'Tag #' . $this->index . ' ' . $this->tagpath . ' begin parsing template'];
				$this->getTemplateFilename();
				$templateContent = $this->getTemplateContent();
				$templateContent = $this->pinpie->parseString($templateContent, $this);
				$this->varsLocal['content'][0][] = $this->content;
				$output = $this->expandVars($templateContent);
			}
		}
		return $output;
	}

	protected function getTemplateFilename() {
		$this->templateFilename = rtrim($this->pinpie->conf->pinpie['templates folder'], '\\/') . DIRECTORY_SEPARATOR . trim($this->template, '\\/') . '.php';
		if ($this->pinpie->conf->pinpie['templates realpath check']) {
			$this->templateFilename = $this->pinpie->checkPathIsInFolder($this->templateFilename, $this->pinpie->conf->pinpie['templates folder']);
		} else {
			$this->error('Template path is not in the folder it have to be. tag:' . $this->fulltag . ' in ' . $this->tagpath);
		}
		if ($this->templateFilename !== false AND !$this->pinpie->is_file($this->templateFilename)) {
			$this->templateFilename = false;
			$this->error('Template file not found. tag:' . $this->fulltag . ' in ' . $this->tagpath);
		}
	}

	protected function getTemplateContent() {
		if (!$this->template OR !$this->templateFilename) {
			return '[[*content]]';
		}
		return $this->fileExecute($this->templateFilename, $this->templateParams);
	}

	protected function error($text) {
		$this->pinpie->errors[$this->index][] = $this->errors[] = $text;
		if (empty($_SERVER['REQUEST_URI'])) {
			$_SERVER['REQUEST_URI'] = '';
		}
		$this->pinpie->logit("Tag processing failed
    url: {$_SERVER['REQUEST_URI']}
    tag: {$this->fulltag} in {$this->tagpath}
    error: " . $text);
	}

	protected function fileExecute($filename, $params = []) {
		static $i = 0;
		$i++;
		$this->pinpie->times[] = [microtime(true), 'executing ' . $filename . ' ' . $i];
		if (is_string($params)) {
			parse_str($params, $params);
		}
		if (!empty($params)) {
			extract($params);
		}
		ob_start();
		include $filename;
		$this->pinpie->times[] = [microtime(true), 'executed ' . $filename . ' ' . $i];
		return ob_get_clean();
	}


	protected $counterExpandVars = 0;

	protected function expandVars($content) {
		$this->counterExpandVars++;
		$this->pinpie->times[] = [microtime(true), 'Tag #' . $this->index . ' expanding vars ' . $this->counterExpandVars];
		//have to do this to use vars on the same tag it was created
		/*    if (isset($this->vars'][0]) AND isset($this->vars'][0]['content']) AND strpos($content, '[[*content]]') !== false) {
					$content = str_replace('[[*content]]', implode('', $this->vars'][0]['content']), $content);
					unset($this->vars'][0]['content']);
				}*/
		$depth = 100;
		$content = static::replacePlaceholdersRecursive($content, $depth);
		$this->pinpie->times[] = [microtime(true), 'Tag #' . $this->index . ' finished expanding vars ' . $this->counterExpandVars];
		return $content;
	}


	protected function replacePlaceholdersRecursive($content, &$depth) {
		$depth--;
		if (!$depth) {
			$this->error('Max depth reached while calling replacePlaceholdersRecursive()');
			return '';
		}
		$content = preg_replace_callback('/\[\[\*([^\[\]]+)\]\]/',
			function ($matches) use (&$depth) {
				$r = '';
				$defaultValue = '';
				if (empty($matches[1])) {
					return $r;
				}
				$placeholder = explode('=', $matches[1], 2);
				if (!empty($placeholder[1])) {
					$defaultValue = $placeholder[1];
				}
				$placeholder = $placeholder[0];
				if ($this->pinpie->conf->debug) {
					$r = '&#91;&#91;&#42;' . $placeholder . '&#93;&#93;' . $r;
				}
				$var = [];
				if (isset($this->pinpie->vars[$placeholder])) {
					$var = $this->pinpie->vars[$placeholder];
					if ($this->pinpie->conf->pinpie['templates clear vars after use']) {
						unset($this->pinpie->vars[$placeholder]);
					}
				}

				if (isset($this->varsLocal[$placeholder])) {
					foreach ($this->varsLocal[$placeholder] as $priority => $values) {
						if (!isset($var[$priority])) {
							$var[$priority] = [];
						}
						$var[$priority] += $values;
					}
				}

				$varcontent = '';
				if (empty($var)) {
					$r .= $defaultValue;
				} else {
					ksort($var);
					foreach ($var as $pri => $item) {
						$varcontent .= implode($item);
					}
				}
				$r .= $this->replacePlaceholdersRecursive($varcontent, $depth);


				return $r;
			}, $content);
		return $content;
	}


}