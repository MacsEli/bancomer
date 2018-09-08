<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require APPPATH . '/libraries/REST_Controller.php';
require APPPATH . '/libraries/phpass-0.3/PasswordHash.php';

define('PHPASS_HASH_STRENGTH', 8);
define('PHPASS_HASH_PORTABLE', false);


class Api extends REST_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->database();
        $this->load->model('generico' , 'generico');
        
    }

    public function index_get($id = 0) {
      $data = $this->get();
      $where = "";
      if($id > 0){
        $where = "where id = $id";
      }
      $registros = (isset($data['registros']))?$data['registros']:10;
      $pagina = (isset($data['pagina']))?$data['pagina']:1;
      $filtros = (isset($data['filtros']))?$data['filtros']:"";
      $where = "";
      if(isset($this->search) && $this->search != "" ){
        $where .= $this->search;
      }
      $datos = $this->generico->obtenerRegistros([
                  'select' => 'COUNT(*) as count',
                  'from' => $this->table_name,
                  'where' => $where,
                ]);
      $pag_reg = validarPaginaRegistros($pagina,$registros,$datos[0]['count']);
      $limit = limitQuery($pag_reg['pagina']);
      //debug($limit);
      $registros =  $this->generico->obtenerRegistros([
                      'select' => '*',
                      'from' => $this->table_name,
                      'where' => $where,
                      'limit' => $limit
                    ]);
      
      $this->response([
                        'registros' => $registros,
                        'num_registros' => $pag_reg['registros'],
                        'pagina' => $pag_reg['pagina'],
                        'limite_paginas' => $pag_reg['limite_paginas'],
                      ], 200);
    }

    public function index_post(){
      $data = $this->post();
      
      if(isset($data['password'])){
        $hasher = new PasswordHash(PHPASS_HASH_STRENGTH, PHPASS_HASH_PORTABLE);
        $user_pass_hashed = $hasher->HashPassword($data['password']);
        $data['password'] = $user_pass_hashed; 
      }
      $datos = $this->generico->insertarRegistros([
        'tabla' => $this->table_name,
        'datos' => $data
      ]);
      if (is_null($datos) | $datos < 1) {
        $datos = 0;
      }
      $this->response($datos, 200);
    }

    public function index_put($id = 0){
      $data = $this->put();
      

      if(isset($data['password'])){
        $hasher = new PasswordHash(PHPASS_HASH_STRENGTH, PHPASS_HASH_PORTABLE);
        $user_pass_hashed = $hasher->HashPassword($data['password']);
        $data['password'] = $user_pass_hashed; 
      }
      if($id > 0){
        $datos = $this->generico->modificarRegistros([
                    'tabla' => $this->table_name,
                    'columna' => 'id',
                    'valor'  => $id,
                    'datos' => $data
                  ]);
        if($datos){
          $this->response((int)$id, 200);
        }else{
          $this->response(false, 401);
        }
      }
    }

    public function index_delete($id = 0){

      if ($id > 0) {
        $datos =  $this->generico->eliminarRegistros([
                    'tabla' => $this->table_name,
                    'columna' => $this->columna_delete,
                    'valor'  => $id,
                  ]);
        if($datos){
          $this->response(true, 200);
        }else{
          $this->response(false, 401);
        }
      }
    }
}