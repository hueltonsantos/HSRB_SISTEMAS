import 'package:flutter/material.dart';
import 'financial_report_page.dart';
import 'appointments_report_page.dart';

/// Página de Relatórios
class ReportsPage extends StatefulWidget {
  const ReportsPage({Key? key}) : super(key: key);

  @override
  State<ReportsPage> createState() => _ReportsPageState();
}

class _ReportsPageState extends State<ReportsPage> {
  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('Relatórios'),
      ),
      body: ListView(
        padding: const EdgeInsets.all(16),
        children: [
          _buildReportCard(
            'Relatório Financeiro',
            'Visualize receitas, despesas e lucros por período',
            Icons.attach_money,
            Colors.green,
            () {
              Navigator.push(
                context,
                MaterialPageRoute(builder: (context) => const FinancialReportPage()),
              );
            },
          ),
          const SizedBox(height: 16),
          _buildReportCard(
            'Relatório de Agendamentos',
            'Análise de agendamentos por status e período',
            Icons.calendar_today,
            Colors.blue,
            () {
              Navigator.push(
                context,
                MaterialPageRoute(builder: (context) => const AppointmentsReportPage()),
              );
            },
          ),
          const SizedBox(height: 16),
          _buildReportCard(
            'Relatório de Pacientes',
            'Estatísticas e dados dos pacientes cadastrados',
            Icons.people,
            Colors.orange,
            () {
              // TODO: Implementar relatório de pacientes
            },
          ),
        ],
      ),
    );
  }

  Widget _buildReportCard(
    String title,
    String description,
    IconData icon,
    Color color,
    VoidCallback onTap,
  ) {
    return Card(
      child: InkWell(
        onTap: onTap,
        borderRadius: BorderRadius.circular(12),
        child: Padding(
          padding: const EdgeInsets.all(16),
          child: Row(
            children: [
              Container(
                padding: const EdgeInsets.all(12),
                decoration: BoxDecoration(
                  color: color.withOpacity(0.1),
                  borderRadius: BorderRadius.circular(12),
                ),
                child: Icon(icon, color: color, size: 32),
              ),
              const SizedBox(width: 16),
              Expanded(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(
                      title,
                      style: const TextStyle(
                        fontSize: 16,
                        fontWeight: FontWeight.bold,
                      ),
                    ),
                    const SizedBox(height: 4),
                    Text(
                      description,
                      style: TextStyle(
                        fontSize: 14,
                        color: Colors.grey[600],
                      ),
                    ),
                  ],
                ),
              ),
              const Icon(Icons.chevron_right),
            ],
          ),
        ),
      ),
    );
  }
}
