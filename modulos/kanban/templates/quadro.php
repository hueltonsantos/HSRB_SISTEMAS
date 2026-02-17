<style>
    .kanban-board {
        display: flex;
        gap: 15px;
        overflow-x: auto;
        padding-bottom: 20px;
        min-height: 70vh;
    }
    .kanban-column {
        min-width: 300px;
        max-width: 300px;
        background: #f8f9fc;
        border-radius: 8px;
        display: flex;
        flex-direction: column;
    }
    .kanban-column-header {
        padding: 12px 15px;
        border-radius: 8px 8px 0 0;
        display: flex;
        justify-content: space-between;
        align-items: center;
        cursor: grab;
    }
    .kanban-column-title {
        font-weight: 600;
        font-size: 14px;
        color: #fff;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .kanban-column-count {
        background: rgba(255,255,255,0.3);
        padding: 2px 8px;
        border-radius: 10px;
        font-size: 12px;
    }
    .kanban-column-body {
        flex: 1;
        padding: 10px;
        overflow-y: auto;
        min-height: 200px;
    }
    .kanban-card {
        background: #fff;
        border-radius: 6px;
        padding: 12px;
        margin-bottom: 10px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        cursor: grab;
        transition: box-shadow 0.2s, transform 0.2s;
        border-left: 3px solid transparent;
    }
    .kanban-card:hover {
        box-shadow: 0 4px 8px rgba(0,0,0,0.15);
    }
    .kanban-card.dragging {
        opacity: 0.5;
        transform: rotate(3deg);
    }
    .kanban-card-title {
        font-weight: 600;
        font-size: 14px;
        color: #333;
        margin-bottom: 8px;
    }
    .kanban-card-desc {
        font-size: 12px;
        color: #666;
        margin-bottom: 10px;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    .kanban-card-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 11px;
        color: #888;
    }
    .kanban-card-priority {
        padding: 2px 6px;
        border-radius: 3px;
        font-size: 10px;
        font-weight: 600;
        text-transform: uppercase;
    }
    .priority-baixa { background: #d4edda; color: #155724; }
    .priority-media { background: #fff3cd; color: #856404; }
    .priority-alta { background: #f8d7da; color: #721c24; }
    .priority-urgente { background: #721c24; color: #fff; }
    .kanban-add-card {
        padding: 10px;
        text-align: center;
    }
    .kanban-add-card button {
        width: 100%;
        border: 2px dashed #ccc;
        background: transparent;
        padding: 10px;
        border-radius: 6px;
        color: #666;
        cursor: pointer;
        transition: all 0.2s;
    }
    .kanban-add-card button:hover {
        border-color: #4e73df;
        color: #4e73df;
        background: rgba(78,115,223,0.05);
    }
    .column-actions .btn {
        padding: 2px 6px;
        font-size: 11px;
    }
    .sortable-ghost {
        opacity: 0.4;
        background: #c8ebfb;
    }
    .kanban-column.sortable-chosen {
        opacity: 0.8;
    }
    @media (max-width: 768px) {
        .kanban-column {
            min-width: 260px;
            max-width: 260px;
        }
        .kanban-board {
            padding-bottom: 10px;
        }
    }
    @media (max-width: 576px) {
        .kanban-column {
            min-width: 240px;
            max-width: 240px;
        }
    }
</style>

<!-- SortableJS CDN -->
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>

<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <a href="index.php?module=kanban" class="btn btn-sm btn-outline-secondary mr-2">
                <i class="fas fa-arrow-left"></i>
            </a>
            <span class="h3 mb-0 text-gray-800" style="border-left: 4px solid <?php echo htmlspecialchars($quadro['cor']); ?>; padding-left: 10px;">
                <?php echo htmlspecialchars($quadro['nome']); ?>
            </span>
        </div>
        <?php if (hasPermission('kanban_manage')): ?>
        <div>
            <button type="button" class="btn btn-success btn-sm shadow-sm" onclick="abrirModalColuna()">
                <i class="fas fa-plus fa-sm mr-1"></i> Nova Coluna
            </button>
            <a href="index.php?module=kanban&action=editar_quadro&id=<?php echo $quadro['id']; ?>" class="btn btn-secondary btn-sm shadow-sm">
                <i class="fas fa-cog fa-sm mr-1"></i> Configurar
            </a>
        </div>
        <?php endif; ?>
    </div>

    <?php if (isset($_SESSION['mensagem'])): ?>
    <div class="alert alert-<?php echo $_SESSION['mensagem']['tipo']; ?> alert-dismissible fade show">
        <?php echo $_SESSION['mensagem']['texto']; ?>
        <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
    <?php unset($_SESSION['mensagem']); endif; ?>

    <!-- Kanban Board -->
    <div class="kanban-board" id="kanbanBoard">
        <?php foreach ($colunas as $coluna): ?>
        <div class="kanban-column" data-coluna-id="<?php echo $coluna['id']; ?>">
            <div class="kanban-column-header" style="background-color: <?php echo htmlspecialchars($coluna['cor']); ?>;">
                <span class="kanban-column-title">
                    <?php echo htmlspecialchars($coluna['nome']); ?>
                    <span class="kanban-column-count"><?php echo count($coluna['cards']); ?></span>
                </span>
                <?php if (hasPermission('kanban_manage')): ?>
                <div class="dropdown no-arrow column-actions">
                    <a class="dropdown-toggle text-white" href="#" data-toggle="dropdown">
                        <i class="fas fa-ellipsis-h"></i>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right">
                        <a class="dropdown-item" href="#" onclick="editarColuna(<?php echo $coluna['id']; ?>, '<?php echo addslashes($coluna['nome']); ?>', '<?php echo $coluna['cor']; ?>')">
                            <i class="fas fa-edit fa-sm mr-2"></i>Editar
                        </a>
                        <a class="dropdown-item text-danger" href="#" onclick="excluirColuna(<?php echo $coluna['id']; ?>)">
                            <i class="fas fa-trash fa-sm mr-2"></i>Excluir
                        </a>
                    </div>
                </div>
                <?php endif; ?>
            </div>
            <div class="kanban-column-body" data-coluna-id="<?php echo $coluna['id']; ?>">
                <?php foreach ($coluna['cards'] as $card): ?>
                <div class="kanban-card" data-card-id="<?php echo $card['id']; ?>"
                     style="border-left-color: <?php echo $card['cor_etiqueta'] ?: 'transparent'; ?>;"
                     onclick="abrirModalCard(<?php echo $card['id']; ?>)">
                    <div class="kanban-card-title"><?php echo htmlspecialchars($card['titulo']); ?></div>
                    <?php if (!empty($card['descricao'])): ?>
                    <div class="kanban-card-desc"><?php echo htmlspecialchars($card['descricao']); ?></div>
                    <?php endif; ?>
                    <div class="kanban-card-footer">
                        <span class="kanban-card-priority priority-<?php echo $card['prioridade']; ?>">
                            <?php echo ucfirst($card['prioridade']); ?>
                        </span>
                        <div>
                            <?php if (!empty($card['data_vencimento'])): ?>
                            <span class="<?php echo strtotime($card['data_vencimento']) < time() ? 'text-danger' : ''; ?>">
                                <i class="fas fa-calendar-alt mr-1"></i><?php echo date('d/m', strtotime($card['data_vencimento'])); ?>
                            </span>
                            <?php endif; ?>
                            <?php if (!empty($card['responsavel_nome'])): ?>
                            <span class="ml-2" title="<?php echo htmlspecialchars($card['responsavel_nome']); ?>">
                                <i class="fas fa-user"></i>
                            </span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php if (hasPermission('kanban_manage')): ?>
            <div class="kanban-add-card">
                <button type="button" onclick="abrirModalNovoCard(<?php echo $coluna['id']; ?>)">
                    <i class="fas fa-plus mr-1"></i> Adicionar Card
                </button>
            </div>
            <?php endif; ?>
        </div>
        <?php endforeach; ?>

        <?php if (empty($colunas) && hasPermission('kanban_manage')): ?>
        <div class="text-center py-5 w-100">
            <i class="fas fa-columns text-gray-300" style="font-size: 48px;"></i>
            <p class="text-muted mt-3">Nenhuma coluna criada. Comece adicionando uma!</p>
            <button type="button" class="btn btn-primary" onclick="abrirModalColuna()">
                <i class="fas fa-plus mr-1"></i> Nova Coluna
            </button>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Modal Nova/Editar Coluna -->
<div class="modal fade" id="modalColuna" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-columns mr-2"></i>Coluna</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="coluna_id">
                <div class="form-group">
                    <label>Nome da Coluna *</label>
                    <input type="text" class="form-control" id="coluna_nome" maxlength="50" required>
                </div>
                <div class="form-group">
                    <label>Cor</label>
                    <input type="color" class="form-control" id="coluna_cor" value="#858796" style="width: 60px; height: 40px;">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" onclick="salvarColuna()">Salvar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Card -->
<div class="modal fade" id="modalCard" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-sticky-note mr-2"></i>Card</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="card_id">
                <input type="hidden" id="card_coluna_id">
                <div class="row">
                    <div class="col-sm-12 col-md-8">
                        <div class="form-group">
                            <label>Titulo *</label>
                            <input type="text" class="form-control" id="card_titulo" maxlength="200" required>
                        </div>
                        <div class="form-group">
                            <label>Descricao</label>
                            <textarea class="form-control" id="card_descricao" rows="4"></textarea>
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-4">
                        <div class="form-group">
                            <label>Prioridade</label>
                            <select class="form-control" id="card_prioridade">
                                <option value="baixa">Baixa</option>
                                <option value="media" selected>Media</option>
                                <option value="alta">Alta</option>
                                <option value="urgente">Urgente</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Responsavel</label>
                            <select class="form-control" id="card_responsavel">
                                <option value="">Nenhum</option>
                                <?php foreach ($usuarios as $u): ?>
                                <option value="<?php echo $u['id']; ?>"><?php echo htmlspecialchars($u['nome']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Data Vencimento</label>
                            <input type="date" class="form-control" id="card_data_vencimento">
                        </div>
                        <div class="form-group">
                            <label>Cor Etiqueta</label>
                            <input type="color" class="form-control" id="card_cor_etiqueta" value="#4e73df" style="width: 60px; height: 35px;">
                        </div>
                    </div>
                </div>

                <!-- Area de Comentarios (apenas ao editar) -->
                <div id="areaComentarios" style="display: none;">
                    <hr>
                    <h6><i class="fas fa-comments mr-1"></i> Comentarios</h6>
                    <div id="listaComentarios" class="mb-3" style="max-height: 200px; overflow-y: auto;"></div>
                    <div class="input-group">
                        <input type="text" class="form-control" id="novoComentario" placeholder="Adicionar comentario...">
                        <div class="input-group-append">
                            <button class="btn btn-outline-primary" type="button" onclick="adicionarComentario()">
                                <i class="fas fa-paper-plane"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer justify-content-between">
                <div>
                    <button type="button" class="btn btn-danger" id="btnExcluirCard" onclick="excluirCard()" style="display: none;">
                        <i class="fas fa-trash mr-1"></i> Excluir
                    </button>
                </div>
                <div>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" onclick="salvarCard()">Salvar</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
const quadroId = <?php echo $quadro['id']; ?>;
const apiUrl = 'index.php?module=kanban&action=api';

// Inicializar Sortable nas colunas
document.querySelectorAll('.kanban-column-body').forEach(coluna => {
    new Sortable(coluna, {
        group: 'cards',
        animation: 150,
        ghostClass: 'sortable-ghost',
        onEnd: function(evt) {
            const cardId = evt.item.dataset.cardId;
            const colunaDestinoId = evt.to.dataset.colunaId;
            const novaOrdem = evt.newIndex;

            fetch(apiUrl, {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: `api_action=move_card&card_id=${cardId}&coluna_destino_id=${colunaDestinoId}&ordem=${novaOrdem}`
            });

            atualizarContadores();
        }
    });
});

// Sortable para reordenar colunas
new Sortable(document.getElementById('kanbanBoard'), {
    animation: 150,
    handle: '.kanban-column-header',
    ghostClass: 'sortable-ghost',
    onEnd: function() {
        const ordens = {};
        document.querySelectorAll('.kanban-column').forEach((col, idx) => {
            ordens[idx] = col.dataset.colunaId;
        });
        fetch(apiUrl, {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: `api_action=reorder_colunas&quadro_id=${quadroId}&ordens=${JSON.stringify(ordens)}`
        });
    }
});

function atualizarContadores() {
    document.querySelectorAll('.kanban-column').forEach(col => {
        const count = col.querySelectorAll('.kanban-card').length;
        col.querySelector('.kanban-column-count').textContent = count;
    });
}

// ==================== COLUNAS ====================
function abrirModalColuna() {
    document.getElementById('coluna_id').value = '';
    document.getElementById('coluna_nome').value = '';
    document.getElementById('coluna_cor').value = '#858796';
    $('#modalColuna').modal('show');
}

function editarColuna(id, nome, cor) {
    document.getElementById('coluna_id').value = id;
    document.getElementById('coluna_nome').value = nome;
    document.getElementById('coluna_cor').value = cor;
    $('#modalColuna').modal('show');
}

function salvarColuna() {
    const id = document.getElementById('coluna_id').value;
    const nome = document.getElementById('coluna_nome').value.trim();
    const cor = document.getElementById('coluna_cor').value;

    if (!nome) {
        alert('Nome e obrigatorio');
        return;
    }

    const action = id ? 'update_coluna' : 'add_coluna';
    let body = `api_action=${action}&nome=${encodeURIComponent(nome)}&cor=${encodeURIComponent(cor)}`;
    body += id ? `&id=${id}` : `&quadro_id=${quadroId}`;

    fetch(apiUrl, {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: body
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert(data.message || 'Erro ao salvar');
        }
    });
}

function excluirColuna(id) {
    if (!confirm('Excluir esta coluna e todos os cards?')) return;

    fetch(apiUrl, {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: `api_action=delete_coluna&id=${id}`
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) location.reload();
        else alert(data.message || 'Erro ao excluir');
    });
}

// ==================== CARDS ====================
function abrirModalNovoCard(colunaId) {
    document.getElementById('card_id').value = '';
    document.getElementById('card_coluna_id').value = colunaId;
    document.getElementById('card_titulo').value = '';
    document.getElementById('card_descricao').value = '';
    document.getElementById('card_prioridade').value = 'media';
    document.getElementById('card_responsavel').value = '';
    document.getElementById('card_data_vencimento').value = '';
    document.getElementById('card_cor_etiqueta').value = '#4e73df';
    document.getElementById('areaComentarios').style.display = 'none';
    document.getElementById('btnExcluirCard').style.display = 'none';
    $('#modalCard').modal('show');
}

function abrirModalCard(cardId) {
    fetch(`${apiUrl}&api_action=get_card&id=${cardId}`)
    .then(r => r.json())
    .then(data => {
        if (!data.success) {
            alert('Erro ao carregar card');
            return;
        }
        const card = data.card;
        document.getElementById('card_id').value = card.id;
        document.getElementById('card_coluna_id').value = card.coluna_id;
        document.getElementById('card_titulo').value = card.titulo;
        document.getElementById('card_descricao').value = card.descricao || '';
        document.getElementById('card_prioridade').value = card.prioridade;
        document.getElementById('card_responsavel').value = card.responsavel_id || '';
        document.getElementById('card_data_vencimento').value = card.data_vencimento || '';
        document.getElementById('card_cor_etiqueta').value = card.cor_etiqueta || '#4e73df';

        // Mostrar comentarios
        document.getElementById('areaComentarios').style.display = 'block';
        document.getElementById('btnExcluirCard').style.display = 'inline-block';

        let html = '';
        if (card.comentarios && card.comentarios.length) {
            card.comentarios.forEach(c => {
                html += `<div class="border-bottom py-2">
                    <small class="text-muted">${c.usuario_nome} - ${new Date(c.created_at).toLocaleString('pt-BR')}</small>
                    <p class="mb-0">${c.comentario}</p>
                </div>`;
            });
        } else {
            html = '<p class="text-muted small">Nenhum comentario ainda.</p>';
        }
        document.getElementById('listaComentarios').innerHTML = html;

        $('#modalCard').modal('show');
    });
}

function salvarCard() {
    const id = document.getElementById('card_id').value;
    const colunaId = document.getElementById('card_coluna_id').value;
    const titulo = document.getElementById('card_titulo').value.trim();
    const descricao = document.getElementById('card_descricao').value;
    const prioridade = document.getElementById('card_prioridade').value;
    const responsavel = document.getElementById('card_responsavel').value;
    const dataVencimento = document.getElementById('card_data_vencimento').value;
    const corEtiqueta = document.getElementById('card_cor_etiqueta').value;

    if (!titulo) {
        alert('Titulo e obrigatorio');
        return;
    }

    const action = id ? 'update_card' : 'add_card';
    let body = `api_action=${action}&titulo=${encodeURIComponent(titulo)}&descricao=${encodeURIComponent(descricao)}`;
    body += `&prioridade=${prioridade}&responsavel_id=${responsavel}&data_vencimento=${dataVencimento}`;
    body += `&cor_etiqueta=${encodeURIComponent(corEtiqueta)}`;
    body += id ? `&id=${id}` : `&coluna_id=${colunaId}`;

    fetch(apiUrl, {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: body
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) location.reload();
        else alert(data.message || 'Erro ao salvar');
    });
}

function excluirCard() {
    const id = document.getElementById('card_id').value;
    if (!confirm('Excluir este card?')) return;

    fetch(apiUrl, {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: `api_action=delete_card&id=${id}`
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) location.reload();
        else alert(data.message || 'Erro ao excluir');
    });
}

function adicionarComentario() {
    const cardId = document.getElementById('card_id').value;
    const comentario = document.getElementById('novoComentario').value.trim();
    if (!comentario) return;

    fetch(apiUrl, {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: `api_action=add_comentario&card_id=${cardId}&comentario=${encodeURIComponent(comentario)}`
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            document.getElementById('novoComentario').value = '';
            abrirModalCard(cardId); // Recarrega os comentarios
        }
    });
}
</script>
