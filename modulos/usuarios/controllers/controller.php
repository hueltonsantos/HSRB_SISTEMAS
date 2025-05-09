<?php
/**
 * Controlador para o módulo de usuários
 */
class UsuarioController {
    
    // Exibe a listagem de usuários
    public function listar() {
        // Incluir o modelo
        require_once 'modulos/usuarios/models/usuario_model.php';
        $model = new UsuarioModel();
        $usuarios = $model->listarTodos();
        
        // Incluir a view
        require_once 'modulos/usuarios/templates/listar.php';
    }
    
    // Exibe o formulário para novo usuário
    public function novo() {
        // Incluir a view
        require_once 'modulos/usuarios/templates/formulario.php';
    }
    
    // Exibe o formulário para edição
    public function editar() {
        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        
        if ($id <= 0) {
            $_SESSION['erro'] = 'ID de usuário inválido.';
            header('Location: index.php?modulo=usuarios&action=listar');
            exit;
        }
        
        // Incluir o modelo
        require_once 'modulos/usuarios/models/usuario_model.php';
        $model = new UsuarioModel();
        $usuario = $model->buscarPorId($id);
        
        if (!$usuario) {
            $_SESSION['erro'] = 'Usuário não encontrado.';
            header('Location: index.php?modulo=usuarios&action=listar');
            exit;
        }
        
        // Incluir a view
        require_once 'modulos/usuarios/templates/formulario.php';
    }
    
    // Salva os dados do usuário
    public function salvar() {
        // Verificar se é método POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?modulo=usuarios&action=listar');
            exit;
        }
        
        // Capturar dados do formulário
        $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
        $nome = isset($_POST['nome']) ? trim($_POST['nome']) : '';
        $email = isset($_POST['email']) ? trim($_POST['email']) : '';
        $senha = isset($_POST['senha']) ? trim($_POST['senha']) : '';
        $nivel_acesso = isset($_POST['nivel_acesso']) ? trim($_POST['nivel_acesso']) : '';
        $status = isset($_POST['status']) ? 1 : 0;
        
        // Validação básica
        if (empty($nome)) {
            $_SESSION['erro'] = 'O nome é obrigatório';
            $this->redirecionarParaFormulario($id);
        } elseif (empty($email)) {
            $_SESSION['erro'] = 'O e-mail é obrigatório';
            $this->redirecionarParaFormulario($id);
        } elseif (empty($nivel_acesso)) {
            $_SESSION['erro'] = 'O nível de acesso é obrigatório';
            $this->redirecionarParaFormulario($id);
        } elseif ($id == 0 && empty($senha)) {
            $_SESSION['erro'] = 'A senha é obrigatória para novos usuários';
            $this->redirecionarParaFormulario($id);
        }
        
        // Incluir o modelo
        require_once 'modulos/usuarios/models/usuario_model.php';
        $model = new UsuarioModel();
        
        // Verificar se o email já existe
        if ($model->emailExiste($email, $id)) {
            $_SESSION['erro'] = 'Este email já está sendo usado por outro usuário';
            $this->redirecionarParaFormulario($id);
        }
        
        // Dados para salvar
        $dados = [
            'nome' => $nome,
            'email' => $email,
            'nivel_acesso' => $nivel_acesso,
            'status' => $status
        ];
        
        // Adicionar senha se necessário
        if (!empty($senha)) {
            $dados['senha'] = password_hash($senha, PASSWORD_DEFAULT);
        }
        
        // Salvar
        $resultado = ($id > 0) 
            ? $model->atualizar($id, $dados) 
            : $model->inserir($dados);
        
        if ($resultado) {
            $_SESSION['sucesso'] = ($id > 0) ? 'Usuário atualizado com sucesso!' : 'Usuário cadastrado com sucesso!';
        } else {
            $_SESSION['erro'] = ($id > 0) ? 'Erro ao atualizar usuário' : 'Erro ao cadastrar usuário';
        }
        
        header('Location: index.php?modulo=usuarios&action=listar');
        exit;
    }
    
    // Exclui um usuário
    public function excluir() {
        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        
        if ($id <= 0) {
            $_SESSION['erro'] = 'ID de usuário inválido.';
            header('Location: index.php?modulo=usuarios&action=listar');
            exit;
        }
        
        // Verificar se não está excluindo o próprio usuário
        if ($id == $_SESSION['usuario_id']) {
            $_SESSION['erro'] = 'Você não pode excluir seu próprio usuário.';
            header('Location: index.php?modulo=usuarios&action=listar');
            exit;
        }
        
        // Incluir o modelo
        require_once 'modulos/usuarios/models/usuario_model.php';
        $model = new UsuarioModel();
        
        if ($model->excluir($id)) {
            $_SESSION['sucesso'] = 'Usuário excluído com sucesso!';
        } else {
            $_SESSION['erro'] = 'Erro ao excluir usuário.';
        }
        
        header('Location: index.php?modulo=usuarios&action=listar');
        exit;
    }
    
    // Função auxiliar para redirecionamento
    private function redirecionarParaFormulario($id) {
        if ($id > 0) {
            header('Location: index.php?modulo=usuarios&action=editar&id=' . $id);
        } else {
            header('Location: index.php?modulo=usuarios&action=novo');
        }
        exit;
    }
}