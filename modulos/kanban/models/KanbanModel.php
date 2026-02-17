<?php

/**
 * Model para o sistema Kanban
 */
class KanbanModel extends Model
{
    // ==================== QUADROS ====================

    public function listarQuadros($usuarioId = null)
    {
        $sql = "SELECT q.*, u.nome as criador_nome,
                       (SELECT COUNT(*) FROM kanban_colunas WHERE quadro_id = q.id) as total_colunas,
                       (SELECT COUNT(*) FROM kanban_cards c
                        INNER JOIN kanban_colunas col ON c.coluna_id = col.id
                        WHERE col.quadro_id = q.id) as total_cards
                FROM kanban_quadros q
                LEFT JOIN usuarios u ON q.criado_por = u.id
                WHERE q.status = 1
                ORDER BY q.updated_at DESC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function buscarQuadro($id)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM kanban_quadros WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function salvarQuadro($dados)
    {
        if (isset($dados['id']) && $dados['id']) {
            $stmt = $this->pdo->prepare("UPDATE kanban_quadros SET nome = ?, descricao = ?, cor = ? WHERE id = ?");
            $stmt->execute([$dados['nome'], $dados['descricao'] ?? '', $dados['cor'] ?? '#4e73df', $dados['id']]);
            return $dados['id'];
        } else {
            $stmt = $this->pdo->prepare("INSERT INTO kanban_quadros (nome, descricao, cor, criado_por) VALUES (?, ?, ?, ?)");
            $stmt->execute([$dados['nome'], $dados['descricao'] ?? '', $dados['cor'] ?? '#4e73df', $dados['criado_por'] ?? null]);
            return $this->pdo->lastInsertId();
        }
    }

    public function excluirQuadro($id)
    {
        $stmt = $this->pdo->prepare("UPDATE kanban_quadros SET status = 0 WHERE id = ?");
        return $stmt->execute([$id]);
    }

    // ==================== COLUNAS ====================

    public function listarColunas($quadroId)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM kanban_colunas WHERE quadro_id = ? AND status = 1 ORDER BY ordem ASC");
        $stmt->execute([$quadroId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function buscarColuna($id)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM kanban_colunas WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function salvarColuna($dados)
    {
        if (isset($dados['id']) && $dados['id']) {
            $stmt = $this->pdo->prepare("UPDATE kanban_colunas SET nome = ?, cor = ?, limite_cards = ? WHERE id = ?");
            $stmt->execute([$dados['nome'], $dados['cor'] ?? '#858796', $dados['limite_cards'] ?? null, $dados['id']]);
            return $dados['id'];
        } else {
            // Pegar proxima ordem
            $stmtOrdem = $this->pdo->prepare("SELECT COALESCE(MAX(ordem), 0) + 1 FROM kanban_colunas WHERE quadro_id = ?");
            $stmtOrdem->execute([$dados['quadro_id']]);
            $ordem = $stmtOrdem->fetchColumn();

            $stmt = $this->pdo->prepare("INSERT INTO kanban_colunas (quadro_id, nome, cor, ordem, limite_cards) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$dados['quadro_id'], $dados['nome'], $dados['cor'] ?? '#858796', $ordem, $dados['limite_cards'] ?? null]);
            return $this->pdo->lastInsertId();
        }
    }

    public function excluirColuna($id)
    {
        $stmt = $this->pdo->prepare("DELETE FROM kanban_colunas WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function reordenarColunas($quadroId, $ordens)
    {
        foreach ($ordens as $ordem => $colunaId) {
            $stmt = $this->pdo->prepare("UPDATE kanban_colunas SET ordem = ? WHERE id = ? AND quadro_id = ?");
            $stmt->execute([$ordem, $colunaId, $quadroId]);
        }
        return true;
    }

    // ==================== CARDS ====================

    public function listarCards($colunaId)
    {
        $sql = "SELECT c.*, u.nome as responsavel_nome
                FROM kanban_cards c
                LEFT JOIN usuarios u ON c.responsavel_id = u.id
                WHERE c.coluna_id = ? AND c.status = 1
                ORDER BY c.ordem ASC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$colunaId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function buscarCard($id)
    {
        $sql = "SELECT c.*, u.nome as responsavel_nome, col.nome as coluna_nome, q.nome as quadro_nome
                FROM kanban_cards c
                LEFT JOIN usuarios u ON c.responsavel_id = u.id
                LEFT JOIN kanban_colunas col ON c.coluna_id = col.id
                LEFT JOIN kanban_quadros q ON col.quadro_id = q.id
                WHERE c.id = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function salvarCard($dados)
    {
        if (isset($dados['id']) && $dados['id']) {
            $stmt = $this->pdo->prepare("UPDATE kanban_cards SET
                titulo = ?, descricao = ?, cor_etiqueta = ?, prioridade = ?,
                responsavel_id = ?, data_vencimento = ?
                WHERE id = ?");
            $stmt->execute([
                $dados['titulo'],
                $dados['descricao'] ?? '',
                $dados['cor_etiqueta'] ?? null,
                $dados['prioridade'] ?? 'media',
                $dados['responsavel_id'] ?: null,
                $dados['data_vencimento'] ?: null,
                $dados['id']
            ]);
            return $dados['id'];
        } else {
            // Pegar proxima ordem
            $stmtOrdem = $this->pdo->prepare("SELECT COALESCE(MAX(ordem), 0) + 1 FROM kanban_cards WHERE coluna_id = ?");
            $stmtOrdem->execute([$dados['coluna_id']]);
            $ordem = $stmtOrdem->fetchColumn();

            $stmt = $this->pdo->prepare("INSERT INTO kanban_cards
                (coluna_id, titulo, descricao, cor_etiqueta, prioridade, responsavel_id, data_vencimento, ordem, criado_por)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $dados['coluna_id'],
                $dados['titulo'],
                $dados['descricao'] ?? '',
                $dados['cor_etiqueta'] ?? null,
                $dados['prioridade'] ?? 'media',
                $dados['responsavel_id'] ?: null,
                $dados['data_vencimento'] ?: null,
                $ordem,
                $dados['criado_por'] ?? null
            ]);
            return $this->pdo->lastInsertId();
        }
    }

    public function moverCard($cardId, $colunaDestinoId, $novaOrdem, $usuarioId = null)
    {
        // Buscar coluna origem
        $stmt = $this->pdo->prepare("SELECT coluna_id FROM kanban_cards WHERE id = ?");
        $stmt->execute([$cardId]);
        $colunaOrigemId = $stmt->fetchColumn();

        // Atualizar card
        $stmt = $this->pdo->prepare("UPDATE kanban_cards SET coluna_id = ?, ordem = ? WHERE id = ?");
        $stmt->execute([$colunaDestinoId, $novaOrdem, $cardId]);

        // Registrar historico
        if ($colunaOrigemId != $colunaDestinoId) {
            $this->registrarHistorico($cardId, $usuarioId, 'movido', $colunaOrigemId, $colunaDestinoId);
        }

        return true;
    }

    public function excluirCard($id)
    {
        $stmt = $this->pdo->prepare("UPDATE kanban_cards SET status = 0 WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function reordenarCards($colunaId, $ordens)
    {
        foreach ($ordens as $ordem => $cardId) {
            $stmt = $this->pdo->prepare("UPDATE kanban_cards SET ordem = ?, coluna_id = ? WHERE id = ?");
            $stmt->execute([$ordem, $colunaId, $cardId]);
        }
        return true;
    }

    // ==================== COMENTARIOS ====================

    public function listarComentarios($cardId)
    {
        $sql = "SELECT c.*, u.nome as usuario_nome
                FROM kanban_comentarios c
                LEFT JOIN usuarios u ON c.usuario_id = u.id
                WHERE c.card_id = ?
                ORDER BY c.created_at DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$cardId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function adicionarComentario($cardId, $usuarioId, $comentario)
    {
        $stmt = $this->pdo->prepare("INSERT INTO kanban_comentarios (card_id, usuario_id, comentario) VALUES (?, ?, ?)");
        $stmt->execute([$cardId, $usuarioId, $comentario]);
        return $this->pdo->lastInsertId();
    }

    // ==================== HISTORICO ====================

    public function registrarHistorico($cardId, $usuarioId, $acao, $colunaOrigemId = null, $colunaDestinoId = null, $detalhes = null)
    {
        $stmt = $this->pdo->prepare("INSERT INTO kanban_historico
            (card_id, usuario_id, acao, coluna_origem_id, coluna_destino_id, detalhes)
            VALUES (?, ?, ?, ?, ?, ?)");
        return $stmt->execute([$cardId, $usuarioId, $acao, $colunaOrigemId, $colunaDestinoId, $detalhes]);
    }

    public function listarHistorico($cardId)
    {
        $sql = "SELECT h.*, u.nome as usuario_nome,
                       co.nome as coluna_origem_nome, cd.nome as coluna_destino_nome
                FROM kanban_historico h
                LEFT JOIN usuarios u ON h.usuario_id = u.id
                LEFT JOIN kanban_colunas co ON h.coluna_origem_id = co.id
                LEFT JOIN kanban_colunas cd ON h.coluna_destino_id = cd.id
                WHERE h.card_id = ?
                ORDER BY h.created_at DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$cardId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // ==================== USUARIOS ====================

    public function listarUsuarios()
    {
        $stmt = $this->pdo->prepare("SELECT id, nome FROM usuarios WHERE status = 1 ORDER BY nome");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // ==================== ESTATISTICAS ====================

    public function estatisticasQuadro($quadroId)
    {
        $stats = [];

        // Total por coluna
        $sql = "SELECT col.id, col.nome, col.cor, COUNT(c.id) as total
                FROM kanban_colunas col
                LEFT JOIN kanban_cards c ON c.coluna_id = col.id AND c.status = 1
                WHERE col.quadro_id = ? AND col.status = 1
                GROUP BY col.id
                ORDER BY col.ordem";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$quadroId]);
        $stats['por_coluna'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Total por prioridade
        $sql = "SELECT c.prioridade, COUNT(*) as total
                FROM kanban_cards c
                INNER JOIN kanban_colunas col ON c.coluna_id = col.id
                WHERE col.quadro_id = ? AND c.status = 1
                GROUP BY c.prioridade";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$quadroId]);
        $stats['por_prioridade'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Cards atrasados
        $sql = "SELECT COUNT(*) FROM kanban_cards c
                INNER JOIN kanban_colunas col ON c.coluna_id = col.id
                WHERE col.quadro_id = ? AND c.status = 1
                AND c.data_vencimento < CURDATE()";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$quadroId]);
        $stats['atrasados'] = $stmt->fetchColumn();

        return $stats;
    }
}
