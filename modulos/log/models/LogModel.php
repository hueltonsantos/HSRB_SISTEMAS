<?php
require_once ROOT_PATH . '/Model.php';

class LogModel extends Model {
    protected $table = 'logs_sistema';

    /**
     * Lista logs com filtros e paginação
     */
    public function listar($filtros = [], $limite = 50, $offset = 0) {
        $where = "WHERE 1=1";
        $params = [];

        if (!empty($filtros['usuario_id'])) {
            $where .= " AND usuario_id = ?";
            $params[] = $filtros['usuario_id'];
        }

        if (!empty($filtros['acao'])) {
            $where .= " AND acao = ?";
            $params[] = $filtros['acao'];
        }

        if (!empty($filtros['modulo'])) {
            $where .= " AND modulo = ?";
            $params[] = $filtros['modulo'];
        }

        if (!empty($filtros['data_inicio'])) {
            $where .= " AND DATE(data_hora) >= ?";
            $params[] = $filtros['data_inicio'];
        }

        if (!empty($filtros['data_fim'])) {
            $where .= " AND DATE(data_hora) <= ?";
            $params[] = $filtros['data_fim'];
        }

        if (!empty($filtros['busca'])) {
            $where .= " AND (descricao LIKE ? OR usuario_nome LIKE ?)";
            $params[] = "%{$filtros['busca']}%";
            $params[] = "%{$filtros['busca']}%";
        }

        $sql = "SELECT * FROM {$this->table} {$where} ORDER BY data_hora DESC LIMIT {$limite} OFFSET {$offset}";
        return $this->db->fetchAll($sql, $params);
    }

    /**
     * Conta total de logs com filtros
     */
    public function contarTotal($filtros = []) {
        $where = "WHERE 1=1";
        $params = [];

        if (!empty($filtros['usuario_id'])) {
            $where .= " AND usuario_id = ?";
            $params[] = $filtros['usuario_id'];
        }

        if (!empty($filtros['acao'])) {
            $where .= " AND acao = ?";
            $params[] = $filtros['acao'];
        }

        if (!empty($filtros['modulo'])) {
            $where .= " AND modulo = ?";
            $params[] = $filtros['modulo'];
        }

        if (!empty($filtros['data_inicio'])) {
            $where .= " AND DATE(data_hora) >= ?";
            $params[] = $filtros['data_inicio'];
        }

        if (!empty($filtros['data_fim'])) {
            $where .= " AND DATE(data_hora) <= ?";
            $params[] = $filtros['data_fim'];
        }

        $sql = "SELECT COUNT(*) as total FROM {$this->table} {$where}";
        $result = $this->db->fetchOne($sql, $params);
        return (int) $result['total'];
    }

    /**
     * Lista ações distintas para o filtro
     */
    public function getAcoesDistintas() {
        $sql = "SELECT DISTINCT acao FROM {$this->table} ORDER BY acao";
        return $this->db->fetchAll($sql);
    }

    /**
     * Lista módulos distintos para o filtro
     */
    public function getModulosDistintos() {
        $sql = "SELECT DISTINCT modulo FROM {$this->table} ORDER BY modulo";
        return $this->db->fetchAll($sql);
    }

    /**
     * Busca um log específico pelo ID
     */
    public function buscarPorId($id) {
        $sql = "SELECT * FROM {$this->table} WHERE id = ?";
        return $this->db->fetchOne($sql, [$id]);
    }

    /**
     * Registra uma nova entrada de log
     */
    public static function registrar($acao, $modulo, $descricao, $registroId = null, $dadosAnteriores = null, $dadosNovos = null) {
        try {
            $db = Database::getInstance();

            $usuarioId = isset($_SESSION['usuario_id']) ? $_SESSION['usuario_id'] : null;
            $usuarioNome = isset($_SESSION['usuario_nome']) ? $_SESSION['usuario_nome'] : 'Sistema';
            $ip = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : null;

            $sql = "INSERT INTO logs_sistema
                    (usuario_id, usuario_nome, acao, modulo, descricao, registro_id, dados_anteriores, dados_novos, ip, data_hora)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";

            $params = [
                $usuarioId,
                $usuarioNome,
                $acao,
                $modulo,
                $descricao,
                $registroId,
                $dadosAnteriores ? json_encode($dadosAnteriores) : null,
                $dadosNovos ? json_encode($dadosNovos) : null,
                $ip
            ];

            $db->query($sql, $params);
            return true;
        } catch (Exception $e) {
            return false;
        }
    }
}
