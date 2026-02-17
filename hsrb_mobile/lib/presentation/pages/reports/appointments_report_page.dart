import 'package:flutter/material.dart';
import 'package:flutter_bloc/flutter_bloc.dart';
import 'package:intl/intl.dart';
import '../../bloc/report_bloc.dart';
import '../../../data/repositories/report_repository.dart';
import '../../../core/services/auth_service.dart';

/// Página de Relatório de Agendamentos
class AppointmentsReportPage extends StatelessWidget {
  const AppointmentsReportPage({Key? key}) : super(key: key);

  @override
  Widget build(BuildContext context) {
    return BlocProvider(
      create: (context) => ReportBloc(
        repository: ReportRepository(authService: context.read<AuthService>()),
      ),
      child: const AppointmentsReportView(),
    );
  }
}

class AppointmentsReportView extends StatefulWidget {
  const AppointmentsReportView({Key? key}) : super(key: key);

  @override
  State<AppointmentsReportView> createState() => _AppointmentsReportViewState();
}

class _AppointmentsReportViewState extends State<AppointmentsReportView> {
  DateTime _dataInicio = DateTime.now().subtract(const Duration(days: 30));
  DateTime _dataFim = DateTime.now();

  @override
  void initState() {
    super.initState();
    _loadReport();
  }

  void _loadReport() {
    final dateFormat = DateFormat('yyyy-MM-dd');
    context.read<ReportBloc>().add(GenerateAppointmentsReport(
      dataInicio: dateFormat.format(_dataInicio),
      dataFim: dateFormat.format(_dataFim),
    ));
  }

  @override
  Widget build(BuildContext context) {
    final dateFormat = DateFormat('dd/MM/yyyy');
    
    return Scaffold(
      appBar: AppBar(
        title: const Text('Relatório de Agendamentos'),
        actions: [
          IconButton(
            icon: const Icon(Icons.download),
            onPressed: () {
               ScaffoldMessenger.of(context).showSnackBar(
                const SnackBar(content: Text('Exportação para PDF em breve')),
              );
            },
          ),
        ],
      ),
      body: Column(
        children: [
          Container(
            padding: const EdgeInsets.all(16),
            color: Colors.blue.shade50,
            child: Row(
              children: [
                Expanded(
                  child: _buildDateButton(
                    context,
                    'Início',
                    dateFormat.format(_dataInicio),
                    () => _selectDate(true),
                  ),
                ),
                const SizedBox(width: 16),
                Expanded(
                  child: _buildDateButton(
                    context,
                    'Fim',
                    dateFormat.format(_dataFim),
                    () => _selectDate(false),
                  ),
                ),
              ],
            ),
          ),
          Expanded(
            child: BlocBuilder<ReportBloc, ReportState>(
              builder: (context, state) {
                if (state is ReportLoading) {
                  return const Center(child: CircularProgressIndicator());
                } else if (state is ReportError) {
                   return Center(child: Text('Erro: ${state.message}', style: const TextStyle(color: Colors.red)));
                } else if (state is AppointmentsReportLoaded) {
                  return _buildReportContent(state.reportData);
                }
                return const Center(child: Text('Selecione o período'));
              },
            ),
          ),
        ],
      ),
    );
  }

   Widget _buildReportContent(Map<String, dynamic> data) {
    // Adapter
    final total = data['total'] ?? 0;
    final byStatus = data['por_status'] as Map<String, dynamic>? ?? {};
    
    return ListView(
      padding: const EdgeInsets.all(16),
      children: [
        Card(
          child: Padding(
            padding: const EdgeInsets.all(16),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                const Text('Resumo', style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold)),
                const Divider(),
                ListTile(
                  title: const Text('Total de Agendamentos'),
                  trailing: Text(total.toString(), style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 18)),
                ),
                const Divider(),
                const Text('Por Status', style: TextStyle(fontWeight: FontWeight.bold)),
                ...byStatus.entries.map((e) => ListTile(
                   title: Text(_formatStatus(e.key)),
                   trailing: Text(e.value.toString()),
                   leading: Icon(Icons.circle, size: 12, color: _getStatusColor(e.key)),
                   dense: true,
                )),
              ],
            ),
          ),
        ),
      ],
    );
  }

  String _formatStatus(String status) {
    switch(status) {
      case 'agendado': return 'Agendados';
      case 'confirmado': return 'Confirmados';
      case 'finalizado': return 'Finalizados';
      case 'cancelado': return 'Cancelados';
      default: return status;
    }
  }

  Color _getStatusColor(String status) {
     switch(status) {
      case 'agendado': return Colors.blue;
      case 'confirmado': return Colors.orange;
      case 'finalizado': return Colors.green;
      case 'cancelado': return Colors.red;
      default: return Colors.grey;
    }
  }

  Widget _buildDateButton(BuildContext context, String label, String date, VoidCallback onTap) {
    return InkWell(
      onTap: onTap,
       child: Container(
        padding: const EdgeInsets.all(12),
        decoration: BoxDecoration(
          color: Colors.white,
          borderRadius: BorderRadius.circular(8),
          border: Border.all(color: Colors.grey.shade300),
        ),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Text(label, style: TextStyle(fontSize: 12, color: Colors.grey[600])),
             const SizedBox(height: 4),
            Row(
              children: [
                 const Icon(Icons.calendar_today, size: 16, color: Colors.blue),
                 const SizedBox(width: 8),
                 Text(date, style: const TextStyle(fontSize: 16, fontWeight: FontWeight.bold)),
              ],
            ),
          ],
        ),
      ),
    );
  }

   Future<void> _selectDate(bool isStart) async {
    final date = await showDatePicker(
      context: context,
      initialDate: isStart ? _dataInicio : _dataFim,
      firstDate: DateTime(2020),
      lastDate: DateTime.now(),
    );

    if (date != null) {
      setState(() {
        if (isStart) _dataInicio = date; else _dataFim = date;
      });
      _loadReport();
    }
  }
}
