<?php
$model = new PerfilModel();
$perfis = $model->getAll([], 'nome', 'ASC');

require PERFIS_TEMPLATE_PATH . 'list.php';
