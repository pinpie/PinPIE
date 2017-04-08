<?php

namespace pinpie\pinpie\tests\units;

use atoum;
use pinpie\pinpie\Tags\Constant;

class PP extends atoum {

	public function test_pages() {
		if (false) {
			$this->testedInstance = new \pinpie\pinpie\PP();
		}

		$settings = [
			'root' => realpath(__DIR__ . '/../../filetests/pages'),
			'file' => false,
			'pinpie' => [
				'route to parent' => 100,
				'cache class' => '\pinpie\pinpie\Cachers\Disabled',
			],
		];

		$this
			->assert('index page')
			->if($_SERVER['REQUEST_URI'] = '/');
		$this->newTestedInstance($settings);
		$this->string($this->testedInstance->render())->isEqualTo('index page');

		$this
			->assert('some page')
			->if($_SERVER['REQUEST_URI'] = '/somepage');
		$this->newTestedInstance($settings);
		$this->string($this->testedInstance->render())->isEqualTo('some page');

		$this
			->assert('index page in folder')
			->if($_SERVER['REQUEST_URI'] = '/folder/');
		$this->newTestedInstance($settings);
		$this->string($this->testedInstance->render())->isEqualTo('index in folder');

		$this
			->assert('page in folder')
			->if($_SERVER['REQUEST_URI'] = '/folder/pageinfolder');
		$this->newTestedInstance($settings);
		$this->string($this->testedInstance->render())->isEqualTo('page in folder');

		$this
			->assert('long url in folder')
			->if($_SERVER['REQUEST_URI'] = '/somepage/folder/some/long/url');
		$this->newTestedInstance($settings);
		$this->string($this->testedInstance->render())->isEqualTo('some page');
	}

	public function test_chunks() {
		if (false) {
			$this->testedInstance = new \pinpie\pinpie\PP();
		}

		$settings = [
			'root' => realpath(__DIR__ . '/../../filetests/chunks'),
			'file' => false,
			'pinpie' => [
				'route to parent' => 100,
				'cache class' => '\pinpie\pinpie\Cachers\Disabled',
			],
		];

		$this
			->assert('chunk')
			->given($_SERVER['REQUEST_URI'] = '/one');
		$this->newTestedInstance($settings);
		$this
			->string($this->testedInstance->render())
			->isEqualTo('a chunk');

		$this
			->assert('two chunks')
			->given($_SERVER['REQUEST_URI'] = '/two');
		$this->newTestedInstance($settings);
		$this
			->string($this->testedInstance->render())
			->isEqualTo('a chunk another chunk');

		$this
			->assert('folder chunk')
			->given($_SERVER['REQUEST_URI'] = '/folder');
		$this->newTestedInstance($settings);
		$this
			->string($this->testedInstance->render())
			->isEqualTo('a folder chunk');

		$this
			->assert('chunk in chunk')
			->given($_SERVER['REQUEST_URI'] = '/ch2ch');
		$this->newTestedInstance($settings);
		$this
			->string($this->testedInstance->render())
			->isEqualTo('a chunk in another chunk');
	}

	public function test_snippets() {
		if (false) {
			$this->testedInstance = new \pinpie\pinpie\PP();
		}


		$settings = [
			'root' => realpath(__DIR__ . '/../../filetests/snippets'),
			'file' => false,
			'pinpie' => [
				'route to parent' => 100,
				'cache class' => '\pinpie\pinpie\Cachers\Disabled',
			],
		];

		$this
			->assert('snippet')
			->given($_SERVER['REQUEST_URI'] = '/one');
		$this->newTestedInstance($settings);
		$this
			->string($this->testedInstance->render())
			->isEqualTo('a snippet');

		$this
			->assert('two snippets')
			->given($_SERVER['REQUEST_URI'] = '/two');
		$this->newTestedInstance($settings);
		$this
			->string($this->testedInstance->render())
			->isEqualTo('a snippet another snippet');

		$this
			->assert('folder snippet')
			->given($_SERVER['REQUEST_URI'] = '/folder');
		$this->newTestedInstance($settings);
		$this
			->string($this->testedInstance->render())
			->isEqualTo('a folder snippet');

		$this
			->assert('snippet in snippet')
			->given($_SERVER['REQUEST_URI'] = '/sn2sn');
		$this->newTestedInstance($settings);
		$this
			->string($this->testedInstance->render())
			->isEqualTo('a snippet in another snippet');
	}

	public function test_tagtemplates() {
		if (false) {
			$this->testedInstance = new \pinpie\pinpie\PP();
		}


		$settings = [
			'root' => realpath(__DIR__ . '/../../filetests/tagtemplates'),
			'file' => false,
			'pinpie' => [
				'route to parent' => 100,
				'cache class' => '\pinpie\pinpie\Cachers\Disabled',
			],
		];

		$this
			->assert('chunk')
			->given($_SERVER['REQUEST_URI'] = '/chunk');
		$this->newTestedInstance($settings);
		$this
			->string($this->testedInstance->render())
			->isEqualTo('a chunk with template');

		$this
			->assert('snippet')
			->given($_SERVER['REQUEST_URI'] = '/snippet');
		$this->newTestedInstance($settings);
		$this
			->string($this->testedInstance->render())
			->isEqualTo('a snippet with template');

		$this
			->assert('placeholder default value')
			->given($_SERVER['REQUEST_URI'] = '/placeholder-default-value');
		$this->newTestedInstance($settings);
		$this
			->string($this->testedInstance->render())
			->isEqualTo('some default value');

		$this
			->assert('placeholder default value not so default')
			->given($_SERVER['REQUEST_URI'] = '/placeholder-default-value-not');
		$this->newTestedInstance($settings);
		$this
			->string($this->testedInstance->render())
			->isEqualTo('this is not default');
	}

	public function test_raw() {
		if (false) {
			$this->testedInstance = new \pinpie\pinpie\PP();
		}

		$settings = [
			'root' => realpath(__DIR__ . '/../../filetests/raw'),
			'file' => false,
			'pinpie' => [
				'route to parent' => 100,
				'cache class' => '\pinpie\pinpie\Cachers\Disabled',
			],
		];

		$this
			->assert('raw')
			->if($_SERVER['REQUEST_URI'] = '/');
		$this->newTestedInstance($settings);
		$this->testedInstance->template = false;
		$this->string($this->testedInstance->render())->isEqualTo('[[chunk]]');
	}

	public function test_preinclude() {
		if (false) {
			$this->testedInstance = new \pinpie\pinpie\PP();
		}

		$settings = [
			'root' => realpath(__DIR__ . '/../../filetests/preinclude'),
			'file' => false,
			'pinpie' => [
				'route to parent' => 100,
				'cache class' => '\pinpie\pinpie\Cachers\Disabled',
			],
		];

		$this
			->assert('auto preinclude')
			->if($_SERVER['REQUEST_URI'] = '/');
		$this->newTestedInstance($settings);
		$this->string($this->testedInstance->render())->isEqualTo('preinclude' . 'page' . 'postinclude');


		$settings = [
			'root' => realpath(__DIR__ . '/../../filetests/preinclude'),
			'file' => false,
			'pinpie' => [
				'route to parent' => 100,
				'cache class' => '\pinpie\pinpie\Cachers\Disabled',
				'preinclude' => false,
				'postinclude' => false,
			],
		];

		$this
			->assert('skip preinclude')
			->if($_SERVER['REQUEST_URI'] = '/');
		$this->newTestedInstance($settings);
		$this->string($this->testedInstance->render())->isEqualTo('page');


		$settings = [
			'root' => realpath(__DIR__ . '/../../filetests/preinclude'),
			'file' => false,
			'pinpie' => [
				'route to parent' => 100,
				'cache class' => '\pinpie\pinpie\Cachers\Disabled',
			],
		];
		$settings['pinpie']['preinclude'] = $settings['root'] . '/anotherpreinc.php';
		$settings['pinpie']['postinclude'] = $settings['root'] . '/anotherpostinc.php';

		$this
			->assert('skip preinclude')
			->if($_SERVER['REQUEST_URI'] = '/');
		$this->newTestedInstance($settings);
		$this->string($this->testedInstance->render())->isEqualTo('anotherpreinc' . 'page' . 'anotherpostinc');

	}

	public function test_report() {
		if (false) {
			$this->testedInstance = new \pinpie\pinpie\PP();
		}

		$defaults = [
			'root' => '',
			'file' => false,
			'pinpie' => [
				'cache class' => '\pinpie\pinpie\Cachers\Disabled',
			],
		];

		$this
			->assert('report: debug disabled')
			->if($settings = $defaults);
		$this->newTestedInstance($settings);
		$this->testedInstance->template = false;
		$this->boolean($this->testedInstance->report())->isFalse();

		$this
			->assert('report: debug enabled')
			->if($settings = $defaults)
			->and($settings['debug'] = true);
		$this->newTestedInstance($settings);
		$this->testedInstance->template = false;
		$this->string($this->testedInstance->report())->contains('NO ERRORS');

		$this
			->assert('report: with errors')
			->if($settings = $defaults)
			->and($settings['debug'] = true);
		$this->newTestedInstance($settings);
		$this->testedInstance->template = false;
		$this->testedInstance->errors[] = 'abcdefgh';
		$this->string($this->testedInstance->report())->contains('Errors:');
	}

	public function test_reportTags() {
		if (false) {
			$this->testedInstance = new \pinpie\pinpie\PP();
		}

		$defaults = [
			'root' => '',
			'file' => false,
			'pinpie' => [
				'cache class' => '\pinpie\pinpie\Cachers\Disabled',
			],
		];

		$this
			->assert('reportTags: debug disabled')
			->if($settings = $defaults);
		$this->newTestedInstance($settings);
		$this->testedInstance->template = false;
		$this->boolean($this->testedInstance->reportTags())->isFalse();

		$this
			->assert('reportTags: debug enabled')
			->if($settings = $defaults)
			->and($settings['debug'] = true);
		$this->newTestedInstance($settings);
		$this->testedInstance->template = false;
		$this->string($this->testedInstance->reportTags())->contains('<tr><td>fulltag</td><td>PAGE index.php</td></tr>');

	}

	public function test_getHashURL() {

		if (false) {
			$this->testedInstance = new \pinpie\pinpie\PP();
		}


		$defaults = [
			'root' => '',
			'file' => false,
			'pinpie' => [
				'cache class' => '\pinpie\pinpie\Cachers\Disabled',
				'cache rules' => [
					200 => [
						'ignore url' => false,
						'ignore query params' => false,
					]
				],
			],
		];


		$this
			->assert('empty url')
			->if($settings = $defaults)
			->if($this->newTestedInstance($settings))
			->and(http_response_code(200))
			->and($this->testedInstance->url->path = '')
			->and($this->testedInstance->url->query = '')
			->array($this->testedInstance->getHashURL())
			->isEqualTo(['url path' => '', 'url query' => ''])
			->then;
		$this
			->assert('empty url')
			->if($settings = $defaults)
			->and($settings['pinpie']['cache rules'][200]['ignore url'] = true)
			->if($this->newTestedInstance($settings))
			->and(http_response_code(200))
			->and($this->testedInstance->url->path = '')
			->and($this->testedInstance->url->query = '')
			->array($this->testedInstance->getHashURL())
			->isEqualTo(['url path' => '', 'url query' => ''])
			->then;

		$this
			->assert('/')
			->if($settings = $defaults)
			->if($this->newTestedInstance($settings))
			->and(http_response_code(200))
			->and($this->testedInstance->url->path = '/')
			->and($this->testedInstance->url->query = '')
			->array($this->testedInstance->getHashURL())
			->isEqualTo(['url path' => '/', 'url query' => ''])
			->then;

		$this
			->assert('/someurl')
			->if($settings = $defaults)
			->if($this->newTestedInstance($settings))
			->and(http_response_code(200))
			->and($this->testedInstance->url->path = '/someurl')
			->and($this->testedInstance->url->query = 'param1=val1')
			->array($this->testedInstance->getHashURL())
			->isEqualTo(['url path' => '/someurl', 'url query' => 'param1=val1'])
			->then;

		$this
			->assert('/some/long/url')
			->if($settings = $defaults)
			->if($this->newTestedInstance($settings))
			->and(http_response_code(200))
			->and($this->testedInstance->url->path = '/some/long/url')
			->and($this->testedInstance->url->query = '')
			->array($this->testedInstance->getHashURL())
			->isEqualTo(['url path' => '/some/long/url', 'url query' => ''])
			->then;

		$this
			->assert('/some/long/url with param1')
			->if($settings = $defaults)
			->if($this->newTestedInstance($settings))
			->and(http_response_code(200))
			->and($this->testedInstance->url->path = '/some/long/url')
			->and($this->testedInstance->url->query = 'param1=val1')
			->array($this->testedInstance->getHashURL())
			->isEqualTo(['url path' => '/some/long/url', 'url query' => 'param1=val1'])
			->then;

		$this
			->assert('/some/long/url with param1 and param2')
			->if($settings = $defaults)
			->if($this->newTestedInstance($settings))
			->and(http_response_code(200))
			->and($this->testedInstance->url->path = '/some/long/url')
			->and($this->testedInstance->url->query = 'param1=val1&param2=val2')
			->array($this->testedInstance->getHashURL())
			->isEqualTo(['url path' => '/some/long/url', 'url query' => 'param1=val1&param2=val2'])
			->then;


		$this
			->assert('/some/long/url with param1 and param2, ignoring param1')
			->if($settings = $defaults)
			->and($settings['pinpie']['cache rules'][200]['ignore query params'] = ['param1'])
			->if($this->newTestedInstance($settings))
			->and(http_response_code(200))
			->and($this->testedInstance->url->path = '/some/long/url')
			->and($this->testedInstance->url->query = 'param1=val1&param2=val2')
			->array($this->testedInstance->getHashURL())
			->isEqualTo(['url path' => '/some/long/url', 'url query' => 'param2=val2'])
			->then;


	}
}