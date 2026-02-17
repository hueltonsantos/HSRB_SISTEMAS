import 'package:flutter/material.dart';
import 'package:flutter_bloc/flutter_bloc.dart';
import 'package:intl/intl.dart';
import '../../bloc/report_bloc.dart';
import '../../../data/repositories/report_repository.dart';
import '../../../core/services/auth_service.dart';

/// Página de Relatório Financeiro
class FinancialReportPage extends StatelessWidget {
  const FinancialReportPage({Key? key}) : super(key: key);

  @override
  Widget build(BuildContext context) {
    return BlocProvider(
      create: (context) => ReportBloc(
        repository: ReportRepository(authService: context.read<AuthService>()),
      ), // Initial load will be triggered by the stateful widget or initially here
      child: const FinancialReportView(),
    );
  }
}

class FinancialReportView extends StatefulWidget {
  const FinancialReportView({Key? key}) : super(key: key);

  @override
  State<FinancialReportView> createState() => _FinancialReportViewState();
}

class _FinancialReportViewState extends State<FinancialReportView> {
  DateTime _dataInicio = DateTime.now().subtract(const Duration(days: 30));
  DateTime _dataFim = DateTime.now();

  @override
  void initState() {
    super.initState();
    _loadReport();
  }

  void _loadReport() {
    final dateFormat = DateFormat('yyyy-MM-dd');
    context.read<ReportBloc>().add(GenerateFinancialReport(
      dataInicio: dateFormat.format(_dataInicio),
      dataFim: dateFormat.format(_dataFim),
    ));
  }

  @override
  Widget build(BuildContext context) {
    final dateFormat = DateFormat('dd/MM/yyyy');
    
    return Scaffold(
      appBar: AppBar(
        title: const Text('Relatório Financeiro'),
        actions: [
          IconButton(
            icon: const Icon(Icons.download),
            onPressed: () {
              // TODO: Exportar PDF
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
                  return Center(
                    child: Padding(
                      padding: const EdgeInsets.all(16),
                      child: Text(
                        'Erro: ${state.message}',
                        style: const TextStyle(color: Colors.red),
                        textAlign: TextAlign.center,
                      ),
                    ),
                  );
                } else if (state is FinancialReportLoaded) {
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
    // Adapter for API response structure
    // Assuming structure: { 'resumo': { 'receita': ..., 'despesa': ... }, 'detalhes': ... }
    final resumo = data['resumo'] ?? {};
    final totalReceita = resumo['total_receita'] ?? 0.0;
    final totalRepasse = resumo['total_repasse'] ?? 0.0;
    final totalLiquido = resumo['total_liquido'] ?? 0.0;
    final totalAgendamentos = resumo['total_agendamentos'] ?? 0;
    
    // Formatting currency
    final currencyFormat = NumberFormat.currency(locale: 'pt_BR', symbol: 'R\$');

    return ListView(
      padding: const EdgeInsets.all(16),
      children: [
        Card(
          child: Padding(
            padding: const EdgeInsets.all(16),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                const Text(
                  'Resumo Financeiro',
                  style: TextStyle(
                    fontSize: 18,
                    fontWeight: FontWeight.bold,
                  ),
                ),
                const Divider(),
                _buildSummaryRow('Total de Agendamentos', totalAgendamentos.toString(), Colors.blue),
                _buildSummaryRow('Receita Total', currencyFormat.format(totalReceita), Colors.green),
                _buildSummaryRow('Repasses', currencyFormat.format(totalRepasse), Colors.orange),
                const Divider(),
                _buildSummaryRow('Lucro Líquido', currencyFormat.format(totalLiquido), Colors.teal, isBold: true),
              ],
            ),
          ),
        ),
        const SizedBox(height: 16),
        // Add charts or more details here if available in API response
      ],
    );
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
            Text(
              label,
              style: TextStyle(
                fontSize: 12,
                color: Colors.grey[600],
              ),
            ),
            const SizedBox(height: 4),
            Row(
              children: [
                const Icon(Icons.calendar_today, size: 16, color: Colors.blue),
                const SizedBox(width: 8),
                Text(
                  date,
                  style: const TextStyle(
                    fontSize: 16,
                    fontWeight: FontWeight.bold,
                  ),
                ),
              ],
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildSummaryRow(String label, String value, Color color, {bool isBold = false}) {
    return Padding(
      padding: const EdgeInsets.symmetric(vertical: 8),
      child: Row(
        mainAxisAlignment: MainAxisAlignment.spaceBetween,
        children: [
          Text(label, style: isBold ? const TextStyle(fontWeight: FontWeight.bold) : null),
          Text(
            value,
            style: TextStyle(
              fontWeight: FontWeight.bold,
              color: color,
              fontSize: isBold ? 18 : 16,
            ),
          ),
        ],
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
      if (!mounted) return;
      setState(() {
        if (isStart) {
          _dataInicio = date;
        } else {
          _dataFim = date;
        }
      });
      _loadReport();
    }
  }
}
