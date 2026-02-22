<?php

/**
 * Classe NotificacaoModel - Gerencia operações relacionadas às notificações
 */
class NotificacaoModel extends Model
{

    /**
     * Construtor
     */
    public function __construct()
    {
        parent::__construct();
        $this->table = 'notificacoes';
    }

    /**
     * Cria uma nova notificação
     * @param array $data
     * @return int ID da notificação criada
     */
    public function criarNotificacao($data)
    {
        return $this->save($data);
    }

    /**
     * Obtém as notificações não lidas mais recentes
     * @param int $limit Número máximo de notificações a retornar
     * @param int $usuario_id ID do usuário (opcional)
     * @return array Lista de notificações
     */
    public function getNotificacoesRecentes($limit = 5, $usuario_id = null)
    {
        $sql = "
            SELECT * FROM {$this->table}
            WHERE lida = 0
        ";

        $params = [];

        if ($usuario_id) {
            $sql .= " AND (usuario_id = ? OR usuario_id IS NULL)";
            $params[] = $usuario_id;
        }

        $sql .= " ORDER BY data_criacao DESC LIMIT {$limit}";

        return $this->db->fetchAll($sql, $params);
    }

    /**
     * Conta o número de notificações não lidas
     * @param int $usuario_id ID do usuário (opcional)
     * @return int Número de notificações
     */
    public function contarNotificacoesNaoLidas($usuario_id = null)
    {
        $sql = "
            SELECT COUNT(*) as total FROM {$this->table}
            WHERE lida = 0
        ";

        $params = [];

        if ($usuario_id) {
            $sql .= " AND (usuario_id = ? OR usuario_id IS NULL)";
            $params[] = $usuario_id;
        }

        $result = $this->db->fetchOne($sql, $params);
        return (int) $result['total'];
    }

    /**
     * Marca uma notificação como lida
     * @param int $id ID da notificação
     * @return bool Sucesso da operação
     */
    public function marcarComoLida($id)
    {
        return $this->update($id, ['lida' => 1]);
    }

    /**
     * Marca todas as notificações como lidas
     * @param int $usuario_id ID do usuário (opcional)
     * @return bool Sucesso da operação
     */
    public function marcarTodasComoLidas($usuario_id = null)
    {
        $sql = "UPDATE {$this->table} SET lida = 1 WHERE lida = 0";
        $params = [];

        if ($usuario_id) {
            $sql .= " AND (usuario_id = ? OR usuario_id IS NULL)";
            $params[] = $usuario_id;
        }

        $stmt = $this->db->query($sql, $params);
        return $stmt->rowCount() > 0;
    }

    /**
     * Obtém notificações com paginação
     * @param int $pagina Página atual
     * @param int $porPagina Itens por página
     * @param string $filtro 'todas' ou 'nao_lidas'
     * @param int $usuario_id ID do usuário (opcional)
     * @return array [notificacoes, total, totalPaginas]
     */
    public function getNotificacoesPaginadas($pagina = 1, $porPagina = 15, $filtro = 'todas', $usuario_id = null)
    {
        $where = "WHERE 1=1";
        $params = [];

        if ($filtro === 'nao_lidas') {
            $where .= " AND lida = 0";
        }

        if ($usuario_id) {
            $where .= " AND (usuario_id = ? OR usuario_id IS NULL)";
            $params[] = $usuario_id;
        }

        // Total de registros
        $sqlCount = "SELECT COUNT(*) as total FROM {$this->table} {$where}";
        $resultCount = $this->db->fetchOne($sqlCount, $params);
        $total = (int) $resultCount['total'];
        $totalPaginas = ceil($total / $porPagina);

        // Busca paginada
        $offset = ($pagina - 1) * $porPagina;
        $sql = "SELECT * FROM {$this->table} {$where} ORDER BY data_criacao DESC LIMIT {$porPagina} OFFSET {$offset}";
        $notificacoes = $this->db->fetchAll($sql, $params);

        return [
            'notificacoes' => $notificacoes,
            'total' => $total,
            'totalPaginas' => $totalPaginas,
            'paginaAtual' => $pagina
        ];
    }

    /**
     * Formata a data para exibição amigável
     * @param string $data
     * @return string
     */
    public function formatarDataNotificacao($data)
    {
        $data_obj = new DateTime($data);
        $hoje = new DateTime('today');
        $ontem = new DateTime('yesterday');

        if ($data_obj->format('Y-m-d') === $hoje->format('Y-m-d')) {
            return 'Hoje, ' . $data_obj->format('H:i');
        } else if ($data_obj->format('Y-m-d') === $ontem->format('Y-m-d')) {
            return 'Ontem, ' . $data_obj->format('H:i');
        } else {
            return $data_obj->format('d/m/Y H:i');
        }
    }
}
