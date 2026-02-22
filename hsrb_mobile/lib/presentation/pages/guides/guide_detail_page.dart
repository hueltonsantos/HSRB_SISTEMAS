import 'package:flutter/material.dart';
import 'package:flutter_bloc/flutter_bloc.dart';
import '../../../core/constants/app_colors.dart';
import '../../../core/constants/app_strings.dart';
import '../../../core/services/guide_service.dart';
import '../../../data/models/guide_model.dart';

class GuideDetailPage extends StatefulWidget {
  final int guideId;

  const GuideDetailPage({super.key, required this.guideId});

  @override
  State<GuideDetailPage> createState() => _GuideDetailPageState();
}

class _GuideDetailPageState extends State<GuideDetailPage> {
  GuideModel? _guide;
  bool _isLoading = true;
  String? _error;

  @override
  void initState() {
    super.initState();
    _loadGuide();
  }

  Future<void> _loadGuide() async {
    setState(() {
      _isLoading = true;
      _error = null;
    });

    try {
      final service = context.read<GuideService>();
      final guide = await service.getGuide(widget.guideId);
      setState(() {
        _guide = guide;
        _isLoading = false;
      });
    } catch (e) {
      setState(() {
        _error = e.toString().replaceAll('Exception: ', '');
        _isLoading = false;
      });
    }
  }

  Color _statusColor(String status) {
    switch (status) {
      case 'agendado':
        return AppColors.info;
      case 'realizado':
        return AppColors.success;
      case 'cancelado':
        return AppColors.danger;
      default:
        return AppColors.gray500;
    }
  }

  String _formatDate(String date) {
    try {
      final parts = date.split('-');
      if (parts.length == 3) {
        return '${parts[2]}/${parts[1]}/${parts[0]}';
      }
    } catch (_) {}
    return date;
  }

  String _formatDateTime(String dateTime) {
    try {
      final parts = dateTime.split(' ');
      final dateParts = parts[0].split('-');
      if (dateParts.length == 3) {
        final time = parts.length > 1 ? ' ${parts[1].substring(0, 5)}' : '';
        return '${dateParts[2]}/${dateParts[1]}/${dateParts[0]}$time';
      }
    } catch (_) {}
    return dateTime;
  }

  String _formatCurrency(double? value) {
    if (value == null) return 'R\$ 0,00';
    final str = value.toStringAsFixed(2).replaceAll('.', ',');
    return 'R\$ $str';
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('Detalhes da Guia'),
      ),
      body: RefreshIndicator(
        onRefresh: _loadGuide,
        child: _buildBody(),
      ),
    );
  }

  Widget _buildBody() {
    if (_isLoading) {
      return const Center(child: CircularProgressIndicator());
    }

    if (_error != null) {
      return Center(
        child: Padding(
          padding: const EdgeInsets.all(24),
          child: Column(
            mainAxisAlignment: MainAxisAlignment.center,
            children: [
              const Icon(Icons.error_outline, size: 64, color: AppColors.danger),
              const SizedBox(height: 16),
              Text(
                _error!,
                textAlign: TextAlign.center,
                style: const TextStyle(color: AppColors.textSecondary),
              ),
              const SizedBox(height: 16),
              ElevatedButton.icon(
                onPressed: _loadGuide,
                icon: const Icon(Icons.refresh),
                label: const Text(AppStrings.tryAgain),
              ),
            ],
          ),
        ),
      );
    }

    final guide = _guide!;
    final statusColor = _statusColor(guide.status);

    return SingleChildScrollView(
      physics: const AlwaysScrollableScrollPhysics(),
      padding: const EdgeInsets.all(16),
      child: Column(
        children: [
          _buildHeaderCard(guide, statusColor),
          const SizedBox(height: 16),
          _buildSection(
            title: 'Paciente',
            icon: Icons.person,
            children: [
              _buildInfoRow('Nome', guide.pacienteNome ?? '-'),
              if (guide.pacienteCpf != null && guide.pacienteCpf!.isNotEmpty)
                _buildInfoRow(AppStrings.cpf, guide.pacienteCpf!),
            ],
          ),
          const SizedBox(height: 16),
          _buildSection(
            title: 'Procedimento',
            icon: Icons.medical_services,
            children: [
              _buildInfoRow('Procedimento', guide.procedimentoNome ?? '-'),
              if (guide.especialidadeNome != null)
                _buildInfoRow('Especialidade', guide.especialidadeNome!),
              _buildInfoRow('Valor', _formatCurrency(guide.valor)),
            ],
          ),
          const SizedBox(height: 16),
          _buildSection(
            title: 'Agendamento',
            icon: Icons.calendar_today,
            children: [
              _buildInfoRow('Data', _formatDate(guide.dataAgendamento)),
              if (guide.horarioAgendamento != null && guide.horarioAgendamento!.isNotEmpty)
                _buildInfoRow('Horario', guide.horarioAgendamento!),
              _buildInfoRow('Status', guide.statusLabel),
              if (guide.dataEmissao != null)
                _buildInfoRow('Data de Emissao', _formatDateTime(guide.dataEmissao!)),
            ],
          ),
          if (guide.observacoes != null && guide.observacoes!.isNotEmpty) ...[
            const SizedBox(height: 16),
            _buildSection(
              title: AppStrings.observations,
              icon: Icons.notes,
              children: [
                Padding(
                  padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 8),
                  child: Text(
                    guide.observacoes!,
                    style: const TextStyle(fontSize: 14, color: AppColors.textPrimary),
                  ),
                ),
              ],
            ),
          ],
          const SizedBox(height: 32),
        ],
      ),
    );
  }

  Widget _buildHeaderCard(GuideModel guide, Color statusColor) {
    return Container(
      width: double.infinity,
      decoration: BoxDecoration(
        gradient: AppColors.primaryGradient,
        borderRadius: BorderRadius.circular(16),
        boxShadow: AppColors.defaultShadow,
      ),
      padding: const EdgeInsets.all(20),
      child: Column(
        children: [
          const CircleAvatar(
            radius: 40,
            backgroundColor: Colors.white,
            child: Icon(
              Icons.description,
              size: 40,
              color: AppColors.primary,
            ),
          ),
          const SizedBox(height: 12),
          Text(
            guide.codigo ?? '#${guide.id}',
            style: const TextStyle(
              color: Colors.white,
              fontSize: 22,
              fontWeight: FontWeight.w700,
            ),
            textAlign: TextAlign.center,
          ),
          const SizedBox(height: 8),
          Container(
            padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 4),
            decoration: BoxDecoration(
              color: Colors.white.withOpacity(0.2),
              borderRadius: BorderRadius.circular(16),
            ),
            child: Text(
              guide.statusLabel,
              style: const TextStyle(
                color: Colors.white,
                fontSize: 12,
                fontWeight: FontWeight.w600,
              ),
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildSection({
    required String title,
    required IconData icon,
    required List<Widget> children,
  }) {
    return Card(
      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Padding(
            padding: const EdgeInsets.fromLTRB(16, 16, 16, 8),
            child: Row(
              children: [
                Icon(icon, size: 20, color: AppColors.primary),
                const SizedBox(width: 8),
                Text(
                  title,
                  style: const TextStyle(
                    fontSize: 16,
                    fontWeight: FontWeight.w700,
                    color: AppColors.textPrimary,
                  ),
                ),
              ],
            ),
          ),
          const Divider(height: 1),
          ...children,
          const SizedBox(height: 8),
        ],
      ),
    );
  }

  Widget _buildInfoRow(String label, String value) {
    return Padding(
      padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 8),
      child: Row(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          SizedBox(
            width: 130,
            child: Text(
              label,
              style: const TextStyle(
                color: AppColors.textSecondary,
                fontSize: 13,
                fontWeight: FontWeight.w500,
              ),
            ),
          ),
          Expanded(
            child: Text(
              value,
              style: const TextStyle(
                color: AppColors.textPrimary,
                fontSize: 14,
                fontWeight: FontWeight.w500,
              ),
            ),
          ),
        ],
      ),
    );
  }
}
