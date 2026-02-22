import 'dart:convert';
import 'package:flutter/material.dart';
import 'package:flutter_bloc/flutter_bloc.dart';
import 'package:intl/intl.dart';
import '../../bloc/log_bloc.dart';
import '../../../data/repositories/log_repository.dart';
import '../../../core/services/auth_service.dart';

/// Página de Logs do Sistema
class SystemLogsPage extends StatelessWidget {
  const SystemLogsPage({Key? key}) : super(key: key);

  @override
  Widget build(BuildContext context) {
    return BlocProvider(
      create: (context) => LogBloc(
        repository: LogRepository(authService: context.read<AuthService>()),
      )..add(LoadLogs()),
      child: const SystemLogsView(),
    );
  }
}

class SystemLogsView extends StatefulWidget {
  const SystemLogsView({Key? key}) : super(key: key);

  @override
  State<SystemLogsView> createState() => _SystemLogsViewState();
}

class _SystemLogsViewState extends State<SystemLogsView> {
  String? _selectedModulo;
  String? _selectedAcao;

  @override
  void initState() {
    super.initState();
    // Initial load handled by BlocProvider creation
  }

  void _loadLogs() {
    context.read<LogBloc>().add(LoadLogs(
      modulo: _selectedModulo,
      acao: _selectedAcao,
    ));
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('Logs do Sistema'),
        actions: [
          IconButton(
            icon: const Icon(Icons.filter_list),
            onPressed: () => _showFilterDialog(context),
          ),
          IconButton(
            icon: const Icon(Icons.refresh),
            onPressed: _loadLogs,
          ),
        ],
      ),
      body: Column(
        children: [
          if (_selectedModulo != null || _selectedAcao != null)
            Container(
              padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 8),
              color: Colors.blue.shade50,
              child: Row(
                children: [
                  const Icon(Icons.filter_alt, size: 16),
                  const SizedBox(width: 8),
                  Expanded(
                    child: Text(
                      'Filtros: ${_selectedModulo ?? 'Todos'} • ${_selectedAcao ?? 'Todas'}',
                      style: const TextStyle(fontWeight: FontWeight.w500),
                    ),
                  ),
                  TextButton(
                    onPressed: () {
                      setState(() {
                        _selectedModulo = null;
                        _selectedAcao = null;
                      });
                      _loadLogs();
                    },
                    child: const Text('Limpar'),
                  ),
                ],
              ),
            ),
          Expanded(
            child: BlocBuilder<LogBloc, LogState>(
              builder: (context, state) {
                if (state is LogLoading) {
                  return const Center(child: CircularProgressIndicator());
                } else if (state is LogError) {
                  return Center(
                    child: Column(
                      mainAxisAlignment: MainAxisAlignment.center,
                      children: [
                        const Icon(Icons.error_outline, color: Colors.red, size: 48),
                        const SizedBox(height: 16),
                        Text(
                          'Erro ao carregar logs:\n${state.message}',
                          textAlign: TextAlign.center,
                          style: const TextStyle(color: Colors.red),
                        ),
                        const SizedBox(height: 16),
                        ElevatedButton(
                          onPressed: _loadLogs,
                          child: const Text('Tentar Novamente'),
                        ),
                      ],
                    ),
                  );
                } else if (state is LogsLoaded) {
                  final logs = state.logs;
                  if (logs.isEmpty) {
                    return const Center(child: Text('Nenhum log encontrado.'));
                  }

                  return ListView.builder(
                    itemCount: logs.length,
                    padding: const EdgeInsets.all(16),
                    itemBuilder: (context, index) {
                      final log = logs[index];
                      // API might return strings or ints, handle safely
                      final acao = log['acao']?.toString() ?? 'unknown';
                      final modulo = log['modulo']?.toString() ?? 'unknown';
                      final usuarioNome = log['usuario_nome']?.toString() ?? 'Sistema';
                      final dataHora = log['data_hora']?.toString() ?? '';
                      
                      return Card(
                        margin: const EdgeInsets.only(bottom: 12),
                        child: ListTile(
                          leading: CircleAvatar(
                            backgroundColor: _getActionColor(acao),
                            child: Icon(
                              _getActionIcon(acao),
                              color: Colors.white,
                              size: 20,
                            ),
                          ),
                          title: Text('$usuarioNome - $acao'),
                          subtitle: Column(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: [
                              const SizedBox(height: 4),
                              Text('Módulo: $modulo'),
                              if (dataHora.isNotEmpty)
                                Text('Data: ${_formatDate(dataHora)}'),
                            ],
                          ),
                          isThreeLine: true,
                          onTap: () {
                            _showLogDetails(context, log);
                          },
                        ),
                      );
                    },
                  );
                }
                return const Center(child: Text('Iniciando...'));
              },
            ),
          ),
        ],
      ),
    );
  }

  String _formatDate(String dateStr) {
    try {
      final date = DateTime.parse(dateStr);
      return DateFormat('dd/MM/yyyy HH:mm').format(date);
    } catch (e) {
      return dateStr;
    }
  }

  Color _getActionColor(String acao) {
    final lowerAcao = acao.toLowerCase();
    if (lowerAcao.contains('criar') || lowerAcao.contains('adicionar') || lowerAcao.contains('insert')) {
      return Colors.green;
    } else if (lowerAcao.contains('editar') || lowerAcao.contains('atualizar') || lowerAcao.contains('update')) {
      return Colors.blue;
    } else if (lowerAcao.contains('excluir') || lowerAcao.contains('deletar') || lowerAcao.contains('delete')) {
      return Colors.red;
    } else if (lowerAcao.contains('login')) {
      return Colors.purple;
    }
    return Colors.grey;
  }

  IconData _getActionIcon(String acao) {
    final lowerAcao = acao.toLowerCase();
    if (lowerAcao.contains('criar') || lowerAcao.contains('adicionar')) {
      return Icons.add;
    } else if (lowerAcao.contains('editar') || lowerAcao.contains('atualizar')) {
      return Icons.edit;
    } else if (lowerAcao.contains('excluir') || lowerAcao.contains('deletar')) {
      return Icons.delete;
    } else if (lowerAcao.contains('login')) {
      return Icons.login;
    }
    return Icons.info_outline;
  }

  void _showFilterDialog(BuildContext context) {
    String? tempModulo = _selectedModulo;
    String? tempAcao = _selectedAcao;

    showDialog(
      context: context,
      builder: (ctx) => AlertDialog(
        title: const Text('Filtrar Logs'),
        content: Column(
          mainAxisSize: MainAxisSize.min,
          children: [
            DropdownButtonFormField<String>(
              value: tempModulo,
              decoration: const InputDecoration(labelText: 'Módulo'),
              items: const [
                DropdownMenuItem(value: null, child: Text('Todos')),
                DropdownMenuItem(value: 'pacientes', child: Text('Pacientes')),
                DropdownMenuItem(value: 'usuarios', child: Text('Usuários')),
                DropdownMenuItem(value: 'agendamentos', child: Text('Agendamentos')),
                DropdownMenuItem(value: 'financeiro', child: Text('Financeiro')),
                DropdownMenuItem(value: 'tabela_precos', child: Text('Preços')),
                DropdownMenuItem(value: 'auth', child: Text('Autenticação')),
              ],
              onChanged: (value) => tempModulo = value,
            ),
            const SizedBox(height: 16),
            DropdownButtonFormField<String>(
              value: tempAcao,
              decoration: const InputDecoration(labelText: 'Ação'),
              items: const [
                DropdownMenuItem(value: null, child: Text('Todas')),
                DropdownMenuItem(value: 'criar', child: Text('Criar')),
                DropdownMenuItem(value: 'editar', child: Text('Editar')),
                DropdownMenuItem(value: 'excluir', child: Text('Excluir')),
                DropdownMenuItem(value: 'login', child: Text('Login')),
              ],
              onChanged: (value) => tempAcao = value,
            ),
          ],
        ),
        actions: [
          TextButton(
            onPressed: () => Navigator.pop(ctx),
            child: const Text('Cancelar'),
          ),
          ElevatedButton(
            onPressed: () {
              setState(() {
                _selectedModulo = tempModulo;
                _selectedAcao = tempAcao;
              });
              Navigator.pop(ctx);
              _loadLogs();
            },
            child: const Text('Aplicar'),
          ),
        ],
      ),
    );
  }

  void _showLogDetails(BuildContext context, Map<String, dynamic> log) {
    showDialog(
      context: context,
      builder: (ctx) => AlertDialog(
        title: const Text('Detalhes do Log'),
        content: SingleChildScrollView(
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            mainAxisSize: MainAxisSize.min,
            children: [
              _buildDetailRow('Usuário', log['usuario_nome']?.toString() ?? 'N/A'),
              _buildDetailRow('Ação', log['acao']?.toString() ?? 'N/A'),
              _buildDetailRow('Módulo', log['modulo']?.toString() ?? 'N/A'),
              _buildDetailRow('ID Registro', log['registro_id']?.toString() ?? 'N/A'),
              _buildDetailRow('IP', log['ip']?.toString() ?? 'N/A'),
              _buildDetailRow('Data/Hora', _formatDate(log['data_hora']?.toString() ?? '')),
              const Divider(),
              const Text('Dados Anteriores:', style: TextStyle(fontWeight: FontWeight.bold)),
              const SizedBox(height: 4),
              _buildJsonViewer(log['dados_anteriores']),
              const SizedBox(height: 12),
              const Text('Dados Novos:', style: TextStyle(fontWeight: FontWeight.bold)),
              const SizedBox(height: 4),
              _buildJsonViewer(log['dados_novos']),
            ],
          ),
        ),
        actions: [
          TextButton(
            onPressed: () => Navigator.pop(ctx),
            child: const Text('Fechar'),
          ),
        ],
      ),
    );
  }

  Widget _buildJsonViewer(dynamic data) {
    if (data == null || data.toString().isEmpty) {
      return const Text('N/A', style: TextStyle(color: Colors.grey));
    }
    
    String prettyJson = data.toString();
    try {
       // Try to decode if it is a JSON string
       if (data is String) {
          final decoded = json.decode(data);
          const encoder = JsonEncoder.withIndent('  ');
          prettyJson = encoder.convert(decoded);
       }
    } catch (e) {
      // Not a valid JSON string, inspect original
    }

    return Container(
      width: double.maxFinite,
      padding: const EdgeInsets.all(8),
      decoration: BoxDecoration(
        color: Colors.grey.shade100,
        borderRadius: BorderRadius.circular(4),
        border: Border.all(color: Colors.grey.shade300),
      ),
      child: Text(
        prettyJson,
        style: const TextStyle(fontFamily: 'monospace', fontSize: 12),
      ),
    );
  }

  Widget _buildDetailRow(String label, String value) {
    return Padding(
      padding: const EdgeInsets.symmetric(vertical: 4),
      child: Row(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          SizedBox(
            width: 100,
            child: Text(
              '$label:',
              style: const TextStyle(fontWeight: FontWeight.w500),
            ),
          ),
          Expanded(child: Text(value)),
        ],
      ),
    );
  }
}
