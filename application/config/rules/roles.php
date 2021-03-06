<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
//adding config items.
$config['post'] = [
        'nombre' => [
                'field' => 'nombre',
                'rules' => 'required',
                'errors' => [
                    'required' => 'nombre es requerido',
                ],
        ],
    ];


$config['update'] = [
	'nombre' => [
                'field' => 'nombre',
                'rules' => 'min_length[2]',
                'errors' => [
                    'min_length' => 'Mínimo 2 caracteres',
                ],
        ],
    ];