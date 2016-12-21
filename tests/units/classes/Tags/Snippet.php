<?php

namespace pinpie\pinpie\Tags\tests\units;

use atoum;
use pinpie\pinpie\PP;

class Snippet extends atoum {

	public function test() {
		if (false) {
			$this->testedInstance = new \pinpie\pinpie\Tags\Snippet(null, [], 'fulltag', 'type', 'placeholder', 'template', 'cachetime', 'fullname');
		}

		$settings = [
			'root' => realpath(__DIR__ . '/../../../filetests/snippets'),
			'file' => false,
			'pinpie' => [
				'cache class' => '\pinpie\pinpie\Cachers\Disabled',
			],
		];

		$this
			->assert('Snippet')
			->if($pp = new PP($settings))
			->and($type = '$')
			->and($fullname = 'snippet')
			->and($placeholder = '')
			->and($template = '')
			->and($cachetime = '')
			->and($fulltag = '[' . $placeholder . '[' . $cachetime . $type . $fullname . ']' . $template . ']')
			->given($this->newTestedInstance($pp, $pp->conf->tags[$type], $fulltag, $type, $placeholder, $template, $cachetime, $fullname))
			->then
			->string($this->testedInstance->getOutput())
			->isEqualTo('a snippet')
			->then;

		$this
			->assert('Snippet with template')
			->if($pp = new PP($settings))
			->and($type = '$')
			->and($fullname = 'snippet')
			->and($placeholder = '')
			->and($template = 'tagtemplate')
			->and($cachetime = '')
			->and($fulltag = '[' . $placeholder . '[' . $cachetime . $type . $fullname . ']' . $template . ']')
			->given($this->newTestedInstance($pp, $pp->conf->tags[$type], $fulltag, $type, $placeholder, $template, $cachetime, $fullname))
			->then
			->string($this->testedInstance->getOutput())
			->isEqualTo('a snippet with template')
			->then;


		$this
			->assert('Snippet with placeholder')
			->if($pp = new PP($settings))
			->and($type = '$')
			->and($fullname = 'snippet')
			->and($placeholder = 'plaho')
			->and($template = '')
			->and($cachetime = '')
			->and($fulltag = '[' . $placeholder . '[' . $cachetime . $type . $fullname . ']' . $template . ']')
			->and($parent = null)
			->and($priority = 100)
			->given($this->newTestedInstance($pp, $pp->conf->tags[$type], $fulltag, $type, $placeholder, $template, $cachetime, $fullname, $parent, $priority))
			->then
			->string($this->testedInstance->getOutput())
			->isEqualTo('')
			->string($pp->vars[$placeholder][$priority][0])
			->isEqualTo('a snippet')
			->then;

		$this
			->assert('Snippet with placeholder with template')
			->if($pp = new PP($settings))
			->and($type = '$')
			->and($fullname = 'snippet')
			->and($placeholder = 'plaho')
			->and($template = 'tagtemplate')
			->and($cachetime = '')
			->and($fulltag = '[' . $placeholder . '[' . $cachetime . $type . $fullname . ']' . $template . ']')
			->and($parent = null)
			->and($priority = 100)
			->given($this->newTestedInstance($pp, $pp->conf->tags[$type], $fulltag, $type, $placeholder, $template, $cachetime, $fullname, $parent, $priority))
			->then
			->string($this->testedInstance->getOutput())
			->isEqualTo('')
			->string($pp->vars[$placeholder][$priority][0])
			->isEqualTo('a snippet with template')
			->then;

		$this
			->assert('Snippet with params')
			->if($pp = new PP($settings))
			->and($type = '$')
			->and($fullname = 'params?foo=fu&bar=bur')
			->and($placeholder = '')
			->and($template = '')
			->and($cachetime = '')
			->and($fulltag = '[' . $placeholder . '[' . $cachetime . $type . $fullname . ']' . $template . ']')
			->given($this->newTestedInstance($pp, $pp->conf->tags[$type], $fulltag, $type, $placeholder, $template, $cachetime, $fullname))
			->then
			->string($this->testedInstance->getOutput())
			->isEqualTo('fubur')
			->then;

		$this
			->assert('Snippet with template with params')
			->if($pp = new PP($settings))
			->and($type = '$')
			->and($fullname = 'snippet')
			->and($placeholder = '')
			->and($template = 'tagtemplateparams?foo=fu&bar=bur')
			->and($cachetime = '')
			->and($fulltag = '[' . $placeholder . '[' . $cachetime . $type . $fullname . ']' . $template . ']')
			->given($this->newTestedInstance($pp, $pp->conf->tags[$type], $fulltag, $type, $placeholder, $template, $cachetime, $fullname))
			->then
			->string($this->testedInstance->getOutput())
			->isEqualTo('a snippet fubur')
			->then;
	}

	public function test_cached() {
		if (false) {
			$this->testedInstance = new \pinpie\pinpie\Tags\Snippet(null, [], 'fulltag', 'type', 'placeholder', 'template', 'cachetime', 'fullname');
		}

		$settings = [
			'root' => realpath(__DIR__ . '/../../../filetests/snippets'),
			'file' => false,
			'pinpie' => [
				'cache class' => '\pinpie\pinpie\Cachers\APCu',
			],
		];

		$this
			->assert('Snippet cached with params')
			->and($type = '$')
			->and($fullname = 'uniqid')
			->and($placeholder = '')
			->and($template = '')
			->and($cachetime = 1)
			->and($fulltag = '[' . $placeholder . '[' . $cachetime . $type . $fullname . ']' . $template . ']')
			->then
			->and($pp = new PP($settings))
			->and($this->newTestedInstance($pp, $pp->conf->tags[$type], $fulltag, $type, $placeholder, $template, $cachetime, $fullname))
			->and($out1 = $this->testedInstance->getOutput())
			->then
			->and($pp = new PP($settings))
			->and($this->newTestedInstance($pp, $pp->conf->tags[$type], $fulltag, $type, $placeholder, $template, $cachetime, $fullname))
			->and($out2 = $this->testedInstance->getOutput())
			->string($out1)->isEqualTo($out2)
			->then
			->given(sleep(2))
			->and($pp = new PP($settings))
			->and($this->newTestedInstance($pp, $pp->conf->tags[$type], $fulltag, $type, $placeholder, $template, $cachetime, $fullname))
			->and($out3 = $this->testedInstance->getOutput())
			->string($out1)->isNotEqualTo($out3)
			->then;
	}
}