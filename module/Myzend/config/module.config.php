<?php
return array(
	'controllers' => array(
		'invokables' => array(
			'Myzend\Controller\Myzend' => 'Myzend\Controller\MyzendController',
		),
	),
	'router' => array(
		'routes' => array(
			'myzend' => array(
				'type'    => 'segment',
				'options' => array(
					'route'    => '/[:action][/:id]',
					'constraints' => array(
						'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
						'id'     => '[0-9]+',
					),
					'defaults' => array(
						'controller' => 'Myzend\Controller\Myzend',
						'action'     => 'index',
					),
				),
			),
			'home' => array(
				'type' => 'Zend\Mvc\Router\Http\Literal',
				'options' => array(
					'route'    => '/',
					'defaults' => array(
						'controller' => 'Myzend\Controller\Myzend',
						'action'     => 'index',
					),
				),
			),
		),
	),
	'view_manager' => array(
		'template_path_stack' => array(
			'myzend' => __DIR__ . '/../view',
		),
	),
);